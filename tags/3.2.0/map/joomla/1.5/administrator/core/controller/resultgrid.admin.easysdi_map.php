<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

class ADMIN_resultgrid 
{
	function listResultGrid ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchResultGrid{$option}", 'searchResultGrid', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_resultgrid";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_resultgrid ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "description" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		$query .= $orderby;
				
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_resultgrid::listResultGrid($rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editResultGrid ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$resultGrid = new resultGrid ($db);
		$resultGrid->load($id);
		
		$resultGrid->tryCheckOut($option,'resultGrid');

		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($resultGrid->created)
		{ 
			if ($resultGrid->createdby and $resultGrid->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$resultGrid->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($resultGrid->updated and $resultGrid->updated<> '0000-00-00 00:00:00')
		{ 
			if ($resultGrid->updatedby and $resultGrid->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$resultGrid->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		//Get available feature type 
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_featuretype WHERE id IN (SELECT ft_id FROM #__sdi_featuretype_usage WHERE usage_id IN (SELECT id from #__sdi_usage WHERE name ='extraDistinctGrid' ))" );
		$rowsResultGridFT = $db->loadObjectList();
		echo $db->getErrorMsg();
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_featuretype WHERE id IN (SELECT ft_id FROM #__sdi_featuretype_usage WHERE usage_id IN (SELECT id from #__sdi_usage WHERE name ='rowDetails' ))" );
		$rowsDetailsFT = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		HTML_resultgrid::editResultGrid($resultGrid,$rowsResultGridFT,$rowsDetailsFT, $createUser, $updateUser,$resultGrid->getFieldsLength(),$option);
	}
	
	function deleteResultGrid($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=resultGrid" );
			exit;
		}
		foreach( $cid as $result_grid_id )
		{
			$resultGrid = new resultGrid ($db);
			$resultGrid->load($result_grid_id);
				
			if (!$resultGrid->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=resultGrid" );
			}				
		}	
		
		$mainframe->redirect("index.php?option=$option&task=resultGrid");
	}
	
	function saveResultGrid($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$resultGrid =& new resultGrid($db);
		if (!$resultGrid->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=resultGrid" );
			exit();
		}		
		
		if (!$resultGrid->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=resultGrid" );
			exit();
		}
		
		$resultGrid->checkin();
		$mainframe->redirect("index.php?option=$option&task=resultGrid");
	}
	
	function cancelResultGrid($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$resultGrid =& new resultGrid($db);
		$resultGrid->bind(JRequest::get('post'));
		$resultGrid->checkin();

		$mainframe->redirect("index.php?option=$option&task=resultGrid" );
	}

}
?>