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

class ADMIN_overlay
{
	function listOverlay ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchOverlay{$option}", 'searchOverlay', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_overlay " ;
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		$query = "SELECT l.*, g.name as group_name  FROM #__sdi_overlay l INNER JOIN #__sdi_overlaygroup g ON l.group_id = g.id " ;
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "group_id" && $filter_order <> "ordering" && $filter_order <> "layers" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		if($filter_order == "ordering")
		{
			$orderby 	= ' order by "group_id" ASC, '. $filter_order .' '. $filter_order_Dir;
		}
		else 
		{
			$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		}
		$query .= $orderby;
				
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}

		HTML_overlay::listOverlay( $rows, $pageNav,$search, $filter_order_Dir, $filter_order,  $option);
	}

	function editOverlay ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$overlay = new overlay ($db);
		$overlay->load($id);
		
		$overlay->tryCheckOut($option,'overlay');

		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($overlay->created)
		{ 
			if ($overlay->createdby and $overlay->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$overlay->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($overlay->updated and $overlay->updated<> '0000-00-00 00:00:00')
		{ 
			if ($overlay->updatedby and $overlay->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$overlay->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		//Get availaible overlay groups
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_overlaygroup" );
		$rowsGroup = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_overlay::editOverlay($overlay,$createUser, $updateUser,$rowsGroup,$overlay->getFieldsLength(), $option);
	}

	function saveOverlay($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();
		$overlay =& new overlay($db);

		if (!$overlay->bind( $_POST ))
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlay" );
			exit();
		}
		
		if (!$overlay->store("group_id",$overlay->group_id))
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlay" );
			exit();
		}
		
		$overlay->checkin();
		$mainframe->redirect("index.php?option=$option&task=overlay" );
	}

	function deleteOverlay($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1)
		{
			$mainframe->enqueueMessage(JText::_("MAP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=overlay" );
			exit;
		}
		foreach( $cid as $overlay_id )
		{
			$overlay = new overlay ($db);
			$overlay->load($overlay_id);

			if (!$overlay->delete("group_id",$overlay->group_id)) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=overlay" );
			}
		}
		$mainframe->redirect("index.php?option=$option&task=overlay" );
	}
	
	function cancelOverlay($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$overlay = new overlay ($db);
		$overlay->bind(JRequest::get('post'));
		$overlay->checkin();

		$mainframe->redirect("index.php?option=$option&task=overlay" );
	}

	function listOverlayGroup ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchOverlayGroup{$option}", 'searchOverlayGroup', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_overlaygroup";
		$db->setQuery( $query );
		$query .= $query_search;
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		$query = "SELECT *  FROM #__sdi_overlaygroup g ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" &&  $filter_order <> "ordering" && $filter_order <> "open" && $filter_order <> "desription")
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

		HTML_overlay::listOverlayGroup( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option);
	}

	function editOverlayGroup ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$overlay_group = new overlayGroup ($db);
		$overlay_group->load($id);
		
		$overlay_group->tryCheckOut($option,'overlayGroup');

		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($overlay_group->created)
		{ 
			if ($overlay_group->createdby and $overlay_group->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$overlay_group->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($overlay_group->updated and $overlay_group->updated<> '0000-00-00 00:00:00')
		{ 
			if ($overlay_group->updatedby and $overlay_group->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$overlay_group->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_overlay::editOverlayGroup($overlay_group,$createUser, $updateUser, $overlay_group->getFieldsLength(),$option);
	}

	function deleteOverlayGroup($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1)
		{
			$mainframe->enqueueMessage(JText::_("MAP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit;
		}
		foreach( $cid as $overlay_group_id )
		{
			$overlay_group = new overlayGroup ($db);
			$overlay_group->load($overlay_group_id);

			if (!$overlay_group->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			}
		}
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
	}

	function saveOverlayGroup($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();
			
		$overlay_group =& new overlayGroup($db);
		if (!$overlay_group->bind( $_POST ))
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit();
		}

		if (!$overlay_group->store())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit();
		}
		
		$overlay_group->checkin();
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
	}
	
	function cancelOverlayGroup($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$overlay_group =& new overlayGroup($db);
		$overlay_group->bind(JRequest::get('post'));
		$overlay_group->checkin();

		$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
	}

	function orderUpOverlay($id){
		$db =& JFactory::getDBO();
		$overlay = new overlay( $db );
		$overlay->load( $id);
		$overlay->orderUp("group_id",$overlay->group_id);
		
		$mainframe->redirect("index.php?option=$option&task=overlay");
	}
	
	function orderDownOverlay($id){
		$db =& JFactory::getDBO();
		$overlay = new overlay( $db );
		$overlay->load( $id);
		$overlay->orderDown("group_id",$overlay->group_id);
		
		$mainframe->redirect("index.php?option=$option&task=overlay");
	}
	function orderUpOverlayGroup($id){
		$db =& JFactory::getDBO();
		$overlayGroup = new overlayGroup( $db );
		$overlayGroup->load( $id);
		$overlayGroup->orderUp();
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
	}
	
	function orderDownOverlayGroup($id){
		$db =& JFactory::getDBO();
		$overlayGroup = new overlayGroup( $db );
		$overlayGroup->load( $id);
		$overlayGroup->orderDown();
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
	}	
}
?>