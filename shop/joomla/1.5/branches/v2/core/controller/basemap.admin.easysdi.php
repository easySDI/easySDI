<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class ADMIN_basemap {
	
	
	function orderUpBasemapContent($option,$cid){
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$basemap_content = new basemap_content( $db );
		$basemap_content->load( $cid [0]);
		$basemap_content->orderUp();
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_content->basemap_id );
	}
	
	function orderDownBasemapContent($option,$cid){
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$basemap_content = new basemap_content( $db );
		$basemap_content->load( $cid [0]);
		$basemap_content->orderDown();
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_content->basemap_id );
	}
	
	function listBasemapContent($basemap_id,$option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$search = $mainframe->getUserStateFromRequest( "searchBaseMapContent{$option}", 'searchBaseMapContent', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		$order_field = JRequest::getVar('order_field');
		
		if($basemap_id == "")
		{
			$basemap_id = JRequest::getVar('basemap_id');
		}
		
		$basemap_content = new basemap_content( $db );
		$total = $basemap_content->getObjectCount($basemap_id);
		
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// Recherche des enregistrements selon les limites
		if($order_field)
		{
			$query = "SELECT * FROM ".$basemap_content->_tbl." where basemap_id = $basemap_id order by $order_field";
		}
		else
		{
			$query = "SELECT * FROM ".$basemap_content->_tbl." where basemap_id = $basemap_id order by ordering";
		}		
					
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Basemap::listBasemapContent($basemap_id,$use_pagination, $rows, $pageNav,$option, $search);	
	}
	
	function editBasemapContent( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowBasemap = new basemap_content( $database );
		$rowBasemap->load( $id );	
		
		$rowBasemap->basemap_id = JRequest::getVar('basemap_id',-1); 
		
		$rowBasemap->tryCheckOut($option,'listBasemapContent&cid[]='.$rowBasemap->basemap_id);
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("SHOP_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
		
		HTML_Basemap::editBasemapContent( $rowBasemap, $rowsAccount, $id, $option );
	}
	
	function saveBasemapContent($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowBasemap =&	 new basemap_content($database);
				
		$basemap_id = JRequest::getVar('basemap_id');
		if (!$rowBasemap->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_id );
			exit();
		}
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$rowBasemap->user = "";
			$rowBasemap->password = "";
		}
		else
		{
			$rowBasemap->account_id="";
		}
		 
		if (!$rowBasemap->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_id );
			exit();
		}
		
		$rowBasemap->checkin();
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_id);
		}		
		
	}
	
	function deleteBasemapContent($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_id') );
			exit;
		}
		foreach( $cid as $id )
		{
			$Basemap = new basemap_content( $database );
			$Basemap->load( $id );

			if (!$Basemap->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_id') );
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_id') );		
	}
		
	function listBasemap($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$search				= $mainframe->getUserStateFromRequest( "searchBaseMap{$option}",'searchBaseMap','','string' );
		$search				= JString::strtolower( $search );
		
		$basemap = new basemap( $db );
		$total = $basemap->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$basemap->_tbl;	
		if($search)
		{
			$query .= " WHERE LOWER(name) like ".$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}	
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Basemap::listBasemap($use_pagination, $rows, $pageNav,$option, $search);	
	}
	
	function editBasemap( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowBasemap = new basemap( $database );
		$rowBasemap->load( $id );	
		
		$rowBasemap->tryCheckOut($option,'listBasemap');
			
		HTML_Basemap::editBasemap( $rowBasemap,$id, $option );
	}
	
	function saveBasemap($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		$rowBasemap =&	 new basemap($database);
		
		if (!$rowBasemap->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemap" );
			exit();
		}
		
		if (!$rowBasemap->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemap" );
			exit();
		}
		
		$rowBasemap->checkin();
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listBasemap");
		}
	}
	
	function deleteBasemap($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("SHOP_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listBasemap" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Basemap = new Basemap( $database );
			$Basemap->load( $id );
					
			if (!$Basemap->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listBasemap" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listBasemap" );		
	}
	
	function cancelBasemap($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$Basemap = new Basemap( $database );
		$Basemap->bind(JRequest::get('post'));
		$Basemap->checkin();

		$mainframe->redirect("index.php?option=$option&task=listBasemap" );
	}
	
	function cancelBasemapContent($option)
	{
		global $mainframe;
		$database = & JFactory::getDBO();
		$Basemap_content = new basemap_content( $database );
		$Basemap_content->bind(JRequest::get('post'));
		$Basemap_content->checkin();

		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$Basemap_content->basemap_id );
	}
		
}
	
?>
