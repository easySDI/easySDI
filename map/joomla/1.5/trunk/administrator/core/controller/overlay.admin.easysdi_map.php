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
	function listOverlayContent ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		$ordering_field = JRequest::getVar('order_field');

		$query ="SELECT COUNT(*) FROM #__easysdi_overlay_content " ;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		$query = "SELECT *  FROM #__easysdi_overlay_content l " ;
		if($ordering_field)
		{
			$query .= " ORDER BY l.$ordering_field";
		}
		else
		{
			$query .= " ORDER BY l.name";
		}


		if ($use_pagination)
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}

		HTML_overlay::listOverlayContent($use_pagination, $rows, $pageNav, $option);
	}

	function editOverlayContent ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$overlay_content = new overlay_content ($db);
		$overlay_content->load($id);

		//Get availaible overlay groups
		$db->setQuery( "SELECT id as value, name as text FROM #__easysdi_overlay_group" );
		$rowsGroup = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_overlay::editOverlayContent($overlay_content,$rowsGroup, $option);
	}

	function saveOverlayContent($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();
		$overlay_content =& new overlay_content($db);

		if (!$overlay_content->bind( $_POST ))
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayContent" );
			exit();
		}
		
		if($overlay_content->order == '' || $overlay_content->order == 0 )
		{
			$query ="SELECT MAX(o.order) FROM #__easysdi_overlay_content o " ;
			$db->setQuery( $query );
			$total = $db->loadResult();
			$overlay_content->order = $total + 1;
		}

		if (!$overlay_content->store())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayContent" );
			exit();
		}
	}

	function deleteOverlayContent($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1)
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=overlayContent" );
			exit;
		}
		foreach( $cid as $overlay_content_id )
		{
			$overlay_content = new overlay_content ($db);
			$overlay_content->load($overlay_content_id);

			$query ="UPDATE #__easysdi_overlay_content o SET o.order = o.order-1 WHERE o.order > $overlay_content->order" ;
			$db->setQuery( $query );
			$db->query();

			if (!$overlay_content->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=overlayContent" );
			}
		}
	}

	function listOverlayGroup ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		$ordering_field = JRequest::getVar('order_field');

		$query ="SELECT COUNT(*) FROM #__easysdi_overlay_group";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		$query = "SELECT *  FROM #__easysdi_overlay_group g ";
		if($ordering_field)
		{
			$query .= " ORDER BY g.$ordering_field";
		}
		else
		{
			$query .= " ORDER BY g.name";
		}
		if ($use_pagination)
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum())
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}

		HTML_overlay::listOverlayGroup($use_pagination, $rows, $pageNav, $option);
	}

	function editOverlayGroup ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		$overlay_group = new overlay_group ($db);
		$overlay_group->load($id);

		HTML_overlay::editOverlayGroup($overlay_group, $option);
	}

	function deleteOverlayGroup($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1)
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit;
		}
		foreach( $cid as $overlay_group_id )
		{
			$overlay_group = new overlay_group ($db);
			$overlay_group->load($overlay_group_id);

			if (!$overlay_group->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			}
		}
	}

	function saveOverlayGroup($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();
			
		$overlay_group =& new overlay_group($db);
		if (!$overlay_group->bind( $_POST ))
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit();
		}

		if($overlay_group->order == '' || $overlay_group->order == 0 )
		{
			$query ="SELECT MAX(g.order) FROM #__easysdi_overlay_group g";
			$db->setQuery( $query );
			$total = $db->loadResult();
			$overlay_group->order = $total + 1;
		}

		if (!$overlay_group->store())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=overlayGroup" );
			exit();
		}
	}

	/*
	 * Re order the overlays
	 */
	function orderUpOverlay($id, $tableName)
	{

		global  $mainframe;
		$database =& JFactory::getDBO();


		$query = "SELECT *  FROM $tableName  WHERE id = $id ";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$query = "SELECT *  FROM $tableName l  WHERE l.order< $row1->order  order by l.order DESC LIMIT 1";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}


		$query = "update $tableName l set l.order= $row1->order where l.id =$row2->id";
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$query = "update $tableName l set l.order= $row2->order where l.id =$row1->id";
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	}

	/*
	 * Re order the overlays
	 */
	function orderDownOverlay($id,$tableName)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();

		$query = "SELECT *  FROM $tableName  WHERE id = $id ";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$query = "SELECT *  FROM $tableName l  WHERE  l.order > $row1->order  order by l.order ASC LIMIT 1";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}


		$query = "update $tableName l set l.order= $row1->order where l.id =$row2->id";
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		$query = "update $tableName l set l.order= $row2->order where l.id =$row1->id";
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	}
}
?>