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
/*
foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';
*/
defined('_JEXEC') or die('Restricted access');

class ADMIN_baselayer 
{
	function listBaseLayer ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchBaseLayer{$option}", 'searchBaseLayer', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(url) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(layers) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_baselayer ";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_baselayer l ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "url" && $filter_order <> "layers" && $filter_order <> "ordering" && $filter_order <> "description")
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
		
		HTML_baselayer::listBaseLayer($rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editBaseLayer ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$baseLayer = new baseLayer ($db);
		$baseLayer->load($id);
		
		$baseLayer->tryCheckOut($option,'baseLayer');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		
		if ($baseLayer->created)
		{ 
			if ($baseLayer->createdby and $baseLayer->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseLayer->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($baseLayer->updated and $baseLayer->updated<> '0000-00-00 00:00:00')
		{ 
			if ($baseLayer->updatedby and $baseLayer->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseLayer->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_baselayer::editBaseLayer($baseLayer,$createUser, $updateUser, $baseLayer->getFieldsLength(), $option);
	}
	
	function deleteBaseLayer($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit;
		}
		foreach( $cid as $baseLayer_id )
		{
			$baseLayer = new baseLayer ($db);
			$baseLayer->load($baseLayer_id);
			
			if (!$baseLayer->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
	}
	
	function saveBaseLayer($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$baseLayer =& new baseLayer($db);
		if (!$baseLayer->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}				
		  
		if (!$baseLayer->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=baseLayer" );
			exit();
		}
		
		$baseLayer->checkin();
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
	}
	
	function orderUpBasemapLayer($id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderUp();
		$mainframe->redirect("index.php?option=$option&task=baseLayer&order_field=".$order_field );
	}
	
	function orderDownBasemapLayer($id){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->load( $id);
		$baseLayer->orderDown();
		$mainframe->redirect("index.php?option=$option&task=baseLayer&order_field=".$order_field );
	}
	
	function cancelBaseLayer($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$baseLayer = new baseLayer( $db );
		$baseLayer->bind(JRequest::get('post'));
		$baseLayer->checkin();

		$mainframe->redirect("index.php?option=$option&task=baseLayer" );
	}

	function editBaseMap ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$baseMap = new baseMap ($db);
		$query = "SELECT id FROM #__sdi_basemapdefinition LIMIT 0,1" ;
		$db->setQuery($query);
		$baseMap->load($db->loadResult());
		
		$baseMap->tryCheckOut($option,'baseMap');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		
		if ($baseMap->created)
		{ 
			if ($baseMap->createdby and $baseMap->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseMap->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($baseMap->updated and $baseMap->updated<> '0000-00-00 00:00:00')
		{ 
			if ($baseMap->updatedby and $baseMap->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$baseMap->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_baselayer::editBaseMap($baseMap,$createUser, $updateUser, $baseMap->getFieldsLength(), $option);
	}
	
	function saveBaseMap($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$baseMap = new baseMap( $db );
		if (!$baseMap->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=overlayCtrlPanel" );
			exit();
		}				
		  
		if (!$baseMap->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayCtrlPanel" );
			exit();
		}
		
		$baseMap->checkin();
		$mainframe->redirect("index.php?option=$option&task=overlayCtrlPanel");
	}
	
	function cancelBaseMap($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$baseMap = new baseMap( $db );
		$baseMap->bind(JRequest::get('post'));
		$baseMap->checkin();

		$mainframe->redirect("index.php?option=$option&task=overlayCtrlPanel" );
	}
}
?>