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
		
		$location = new location( $db );
		$total = $location->getObjectCount();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM ".$location->_tbl ;
		$search	= $mainframe->getUserStateFromRequest( "$option.searchLocation",'searchLocation','','string' );
		$search	= JString::strtolower( $search );
		if ($search)
		{
			$query .= ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(urlwfs) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );			
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
	
		HTML_Location::listLocation($use_pagination, $rows, $pageNav,$option, $search);	
	}
	
	function editLocation( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowLocation = new location( $database );
		$rowLocation->load( $id );					
		
		//Select all available easysdi Account
		$rowsAccount = array();
		$rowsAccount[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACCOUNT_SELECT" ));
		$rowsAccount = array_merge($rowsAccount,account::getEasySDIAccountsList());
		
		HTML_Location::editLocation( $rowLocation,$rowsAccount,$id, $option );
	}
	
	function saveLocation($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$location =&	 new location($database);
				
		if (!$location->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLocation" );
			exit();
		}
				
		//If a filter location is selected, disable it for the use in location
		if($location->filterlocation_id > 0 )
		{
			$loc =&	 new perimeter($database);
			$loc->load($location->filterlocation_id);
			$loc->setLocalisation(0);
		}
		else
		{
			//delete the default value
			$location->filterlocation_id = null;
		}
		echo $location->filterlocation_id;
		$service_type = JRequest::getVar('service_type');
		if($service_type == "via_proxy")
		{
			$location->user = "";
			$location->password = "";
		}
		else
		{
			$location->account_id="";
		}
		
		if (!$location->store()) {
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
			$Location = new location( $database );
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
			$Location = new location( $database );
			$Location->load( $id );
			$Location->id=0;
			$location->guid=0;
			if (!$Location->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listLocation" );
			}												
		}
		$mainframe->redirect("index.php?option=$option&task=listLocation" );		
	}
	
}
	
?>