<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class SITE_properties {
	
	
	function goDown($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_product_properties_values_definition  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_product_properties_values_definition  where properties_id=$row1->properties_id and `order` > $row1->order   order by `order` ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$row1->properties_id" );
	}
	function goUp($cid,$option){

				global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_product_properties_values_definition  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_product_properties_values_definition  where properties_id=$row1->properties_id and `order` < $row1->order  order by `order` ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row1->order where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_product_properties_values_definition set `order`= $row2->order where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=$row1->properties_id" );				
	}
	function publish($cid,$published){
			global  $mainframe;
			$db =& JFactory::getDBO(); 
		if ($published){
			$query = "update #__easysdi_product_properties_definition  set published = 1  where id=$cid[0]";			
			
		}else{
			$query = "update #__easysdi_product_properties_definition  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query );
		if (!$db->query()) {		
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		
		
	}
	
	function listProperties($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = JRequest::getVar( 'limit', 10 );
		$limitstart = JRequest::getVar( 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',1);		
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		
		$user = JFactory::getUser();	
		$partner = new partnerByUserId($db);
		$partner->load($user->id);
		
		
		
		$query = "SELECT COUNT(*) FROM #__easysdi_product_properties_definition where (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id)) ";
		
		
		$db->setQuery( $query );
		$total = $db->loadResult();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT * FROM #__easysdi_product_properties_definition where (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id))";		
									
		
		if ($use_pagination) {
			$db->setQuery( $query,$pageNav->limitstart, $pageNav->limit);			
		}else{
			$db->setQuery( $query );
		}
		
		$rows = $db->loadObjectList();
		
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		HTML_properties::listProperties($use_pagination, $rows, $pageNav,$option);	

	}
	

	function editProperties( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowProperties = new properties( $database );
		$rowProperties->load( $id );				
			
		$rowProperties->update_date = date('d.m.Y H:i:s'); 		
			if ($id==0){
				$rowProperties->order="0";
			}
		
			
		$user = JFactory::getUser();	
		$partner = new partnerByUserId($database);
		$partner->load($user->id);
		
		
		HTML_properties::editProperties( $rowProperties,$id, $option,$partner);
	}
	
	function saveProperties($option){
		
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowProperties =&	 new Properties($database);
				
		
		if (!$rowProperties->bind( $_POST )) {			
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";			
		}
				
		if ($rowProperties->order == "0")
		{
			$query = "select max( `order`)+1  FROM #__easysdi_product_properties_definition";
			$database->setQuery( $query );
			$maxOrder = $database->loadResult();
			$rowProperties->order = $maxOrder; 
			 
		}
		if (!$rowProperties->store()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";				
		}
					
		
	}
	
	function deleteProperties($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProperties" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$properties = new properties( $database );
			$properties->load( $id );
					
			if (!$properties->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
			}												
		}		
		
		$mainframe->redirect("index.php?option=$option&task=listProperties" );		
	}
		
		
	
	
	
	
	
	
function listPropertiesValues($properties_id , $option) {
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

		$query = "SELECT COUNT(*) FROM #__easysdi_product_properties_values_definition WHERE properties_id=".$properties_id;
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT * FROM #__easysdi_product_properties_values_definition where properties_id=$properties_id order by `order`";		
									
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query );
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo $db->getErrorMsg(); 			
		}		
	
		HTML_properties::listPropertiesValues($properties_id,$use_pagination, $rows, $pageNav,$option);	

	}
	

	function editPropertiesValues( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowProperties = new properties_values( $database );
		$rowProperties->load( $id );					
						if ($id==0){
				$rowProperties->order="0";
			}
		
		$rowProperties->update_date = date('d.m.Y H:i:s'); 		
		
		HTML_properties::editPropertiesValues( $rowProperties,$id, $option );
	}
	
	function savePropertiesValues($option){
		
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowProperties =&	 new properties_values($database);
				
		
		if (!$rowProperties->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValue" );			
		}
				
	if ($rowProperties->order == "0")
		{
			$query = "select max( `order`)+1  FROM #__easysdi_product_properties_values_definition WHERE properties_id=$rowProperties->properties_id";
			$database->setQuery( $query );
			$maxOrder = $database->loadResult();
			$rowProperties->order = $maxOrder; 
			 
		}
	
		if (!$rowProperties->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValue" );			
		}
		
				
		
	}
	
	function deletePropertiesValues($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );
			echo $db->getErrorMsg();
		}
		foreach( $cid as $id )
		{
			$properties = new properties_values( $database );
			$properties->load( $id );
					
			if (!$properties->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPropertiesValues" );
			}												
		}		
		
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
				
	}
	
}
	
?>