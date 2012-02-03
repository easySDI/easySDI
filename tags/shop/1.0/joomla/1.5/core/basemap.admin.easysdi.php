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
	
	
	function orderUpBasemapContent($id,$basemapId){
		

		global  $mainframe;
		$database =& JFactory::getDBO(); 
		

		$query = "SELECT *  FROM #__easysdi_basemap_content  WHERE id = $id AND basemap_def_id = $basemapId";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}

		$query = "SELECT *  FROM #__easysdi_basemap_content  WHERE  basemap_def_id = $basemapId AND ordering < $row1->ordering  order by ordering DESC";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		

		$query = "update #__easysdi_basemap_content set ordering= $row1->ordering where id =$row2->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
				
		$query = "update #__easysdi_basemap_content set ordering= $row2->ordering where id =$row1->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
		
		
		
		
	}
	
		function orderDownBasemapContent($id,$basemapId){
		

		global  $mainframe;
		$database =& JFactory::getDBO(); 

		$query = "SELECT *  FROM #__easysdi_basemap_content  WHERE id = $id AND basemap_def_id = $basemapId";
		$database->setQuery( $query );
		$row1 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}

		$query = "SELECT *  FROM #__easysdi_basemap_content  WHERE  basemap_def_id = $basemapId AND ordering > $row1->ordering  order by ordering";
		$database->setQuery( $query );
		$row2 = $database->loadObject() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		

		$query = "update #__easysdi_basemap_content set ordering= $row1->ordering where id =$row2->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
				
		$query = "update #__easysdi_basemap_content set ordering= $row2->ordering where id =$row1->id";
			$database->setQuery( $query );				
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");								
			}		
		
		
		
		
	}
	
	function listBasemapContent($basemap_id,$option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		$order_field = JRequest::getVar('order_field');
		
		if($basemap_id == "")
		{
			$basemap_id = JRequest::getVar('basemap_def_id');
		}
		

		$query = "SELECT COUNT(*) FROM #__easysdi_basemap_content where basemap_def_id = ".$basemap_id;
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		if($order_field)
		{
			$query = "SELECT * FROM #__easysdi_basemap_content where basemap_def_id = $basemap_id order by $order_field";
		}
		else
		{
			$query = "SELECT * FROM #__easysdi_basemap_content where basemap_def_id = $basemap_id order by ordering";
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
	
		HTML_Basemap::listBasemapContent($basemap_id,$use_pagination, $rows, $pageNav,$option);	

	}
	

	function editBasemapContent( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowBasemap = new basemap_content( $database );
		$rowBasemap->load( $id );					
	
		$rowBasemap->basemap_def_id = JRequest::getVar('basemap_def_id',-1); 
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACCOUNT_SELECT" ));
		$database->setQuery( "SELECT p.partner_id as value, u.name as text FROM #__users u INNER JOIN #__easysdi_community_partner p ON u.id = p.user_id " );
		$rowsAccount = array_merge($rowsAccount, $database->loadObjectList());
		
		HTML_Basemap::editBasemapContent( $rowBasemap, $rowsAccount, $id, $option );
	}
	
	
	
	function saveBasemapContent($returnList ,$option){
						global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowBasemap =&	 new basemap_content($database);
				
		$basemap_def_id = JRequest::getVar('basemap_def_id');
		if (!$rowBasemap->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_def_id );
			exit();
		}
		$query = "SELECT COUNT(*) FROM  #__easysdi_basemap_content where basemap_def_id = ".$basemap_def_id ;
		
		$database->setQuery( $query );
		$total = $database->loadResult();	
			
		$rowBasemap->ordering = $total;		
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$rowBasemap->user = "";
			$rowBasemap->password = "";
		}
		else
		{
			$rowBasemap->easysdi_account_id="";
		}
		 
		
		if (!$rowBasemap->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_def_id );
			exit();
		}
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".$basemap_def_id);
		}		
		
	}
	
	function deleteBasemapContent($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listBasemapContent" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Basemap = new basemap_content( $database );
			$Basemap->load( $id );
					
			if (!$Basemap->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listBasemapContent" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );		
	}
		
	function listBasemap($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$query = "SELECT COUNT(*) FROM #__easysdi_basemap_definition";
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT * FROM #__easysdi_basemap_definition ";		
									
		
	
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Basemap::listBasemap($use_pagination, $rows, $pageNav,$option);	

	}
	
	function editBasemap( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowBasemap = new basemap( $database );
		$rowBasemap->load( $id );					
	
		
		
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
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listBasemap");
		}
		
		
	}
	
	function deleteBasemap($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
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
		
		
	
	
}
	
?>
