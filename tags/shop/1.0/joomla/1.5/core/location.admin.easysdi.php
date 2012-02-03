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

class ADMIN_location {
	
	function listLocation($option) {
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

		$query = "SELECT COUNT(*) FROM #__easysdi_location_definition";
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT id,wfs_url,location_name,location_desc FROM #__easysdi_location_definition ";		
									
		
	
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Location::listLocation($use_pagination, $rows, $pageNav,$option);	

	}
	

	function editLocation( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowLocation = new Location( $database );
		$rowLocation->load( $id );					
	
		if ($id == '0'){
			$rowLocation->creation_date =date('d.m.Y H:i:s');
			 			
		}
		$rowLocation->update_date = date('d.m.Y H:i:s'); 
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACCOUNT_SELECT" ));
		$database->setQuery( "SELECT p.partner_id as value, u.name as text FROM #__users u INNER JOIN #__easysdi_community_partner p ON u.id = p.user_id " );
		$rowsAccount = array_merge($rowsAccount, $database->loadObjectList());
		
		
		HTML_Location::editLocation( $rowLocation,$rowsAccount,$id, $option );
	}
	
	function saveLocation($returnList ,$option){
						global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowLocation =&	 new Location($database);
				
		
		if (!$rowLocation->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLocation" );
			exit();
		}
				
		//If a filter location is selected, disable it for the use in location
		if($rowLocation->id_location_filter > 0 )
		{
			$query = "UPDATE #__easysdi_location_definition  SET is_localisation = 0 WHERE id = $rowLocation->id_location_filter";
			$database->setQuery($query);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->stderr(),'error');
			}
		}
		
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$rowLocation->user = "";
			$rowLocation->password = "";
		}
		else
		{
			$rowLocation->easysdi_account_id="";
		}
		
		if (!$rowLocation->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLocation" );
			exit();
		}
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listLocation");
		}
		
		
	}
	
	
	
	function deleteLocation($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listLocation" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Location = new Location( $database );
			$Location->load( $id );
					
			if (!$Location->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listLocation" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listLocation" );		
	}
	
	function copyLocation($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_COPY"),"error");
			$mainframe->redirect("index.php?option=$option&task=listLocation" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$Location = new Location( $database );
			$Location->load( $id );
			$Location->id=0;
					
			if (!$Location->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listLocation" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listLocation" );		
	}
		
		
	
	
}
	
?>