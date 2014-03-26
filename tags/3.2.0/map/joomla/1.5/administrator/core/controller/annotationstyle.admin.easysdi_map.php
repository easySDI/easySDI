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

class ADMIN_annotationstyle 
{
	function listAnnotationStyle ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchAnnotationStyle{$option}", 'searchAnnotationStyle', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_annotationstyle ";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_annotationstyle ";
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
		
		HTML_annotationstyle::listAnnotationStyle( $rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editAnnotationStyle ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$annotationStyle = new annotationStyle ($db);
		$annotationStyle->load($id);
					
		$annotationStyle->tryCheckOut($option,'annotationStyle');
		
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($annotationStyle->created)
		{ 
			if ($annotationStyle->createdby and $annotationStyle->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$annotationStyle->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($annotationStyle->updated and $annotationStyle->updated<> '0000-00-00 00:00:00')
		{ 
			if ($annotationStyle->updatedby and $annotationStyle->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$annotationStyle->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_annotationstyle::editAnnotationStyle($annotationStyle,$createUser, $updateUser, $annotationStyle->getFieldsLength(), $option);
	}
	
	function deleteAnnotationStyle($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit;
		}
		foreach( $cid as $annotationStyle_id )
		{
			$annotationStyle = new annotationStyle ($db);
			$annotationStyle->load($annotationStyle_id);
				
			if (!$annotationStyle->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=annotationStyle");
	}
	
	function saveAnnotationStyle($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$annotationStyle = new annotationStyle ($db);
		if (!$annotationStyle->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit();
		}		

		if (!$annotationStyle->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
			exit();
		}
		
		$annotationStyle->checkin();
		$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
	}
	
	function cancelAnnotationStyle($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$annotationStyle = new annotationStyle ($db);
		$annotationStyle->bind(JRequest::get('post'));
		$annotationStyle->checkin();

		$mainframe->redirect("index.php?option=$option&task=annotationStyle" );
	}
}
?>