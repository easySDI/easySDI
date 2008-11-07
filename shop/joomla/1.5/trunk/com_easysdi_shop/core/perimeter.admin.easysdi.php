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

class ADMIN_perimeter {
	
	function listPerimeter($option) {
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

		$query = "SELECT COUNT(*) FROM #__easysdi_perimeter_definition";
		
		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		
		$query = "SELECT id,wfs_url,layer_name,perimeter_name,perimeter_desc FROM #__easysdi_perimeter_definition ";		
									
		
	
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_Perimeter::listPerimeter($use_pagination, $rows, $pageNav,$option);	

	}
	

	function editPerimeter( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowPerimeter = new Perimeter( $database );
		$rowPerimeter->load( $id );					
	
		if ($id == '0'){
			$rowPerimeter->creation_date =date('d.m.Y H:i:s');
			 			
		}
		$rowPerimeter->update_date = date('d.m.Y H:i:s'); 
		
		
		HTML_Perimeter::editPerimeter( $rowPerimeter,$id, $option );
	}
	
	function savePerimeter($returnList ,$option){
						global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowPerimeter =&	 new Perimeter($database);
				
		
		if (!$rowPerimeter->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit();
		}
				
		
		if (!$rowPerimeter->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit();
		}
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listPerimeter");
		}
		
		
	}
	
	function deletePerimeter($cid ,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			exit;
		}
		foreach( $cid as $id )
		{
			$Perimeter = new Perimeter( $database );
			$Perimeter->load( $id );
					
			if (!$Perimeter->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
			}												
		}

		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );		
	}
		
		
	
	
}
	
?>