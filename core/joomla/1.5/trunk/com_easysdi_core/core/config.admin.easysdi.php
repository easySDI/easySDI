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

class ADMIN_config {

	
	function listConfig($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
/*		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (#__users.name LIKE '%$search%'";
			$filter .= " OR #__users.username LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_acronym LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_id LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_code LIKE '%$search%')";		
		}
	*/

		
		$query = "SELECT COUNT(*) FROM #__easysdi_config";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		$query = "SELECT *  FROM #__easysdi_config ";
		$query .= " ORDER BY thekey";
		if ($use_pagination) {
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else{
			$db->setQuery( $query);
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_config::listConfig($use_pagination, $rows, $pageNav,$option);
	}

	
	//id = 0 means new Config entry
	function editConfig( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowConfig = new config( $database );
		$rowConfig->load( $id );
		 

		HTML_config::editConfig( $rowConfig,$option );
	}


	function removeConfig( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listConfig" );
			exit;
		}
		foreach( $cid as $config_id )
		{
			$config = new config( $database );
			$config->load( $config_id );
		
		
			if (!$config->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listConfig" );
			}				
		}				
	}

	
	function saveConfig($option ) {
		global $mainframe;
		$database=& JFactory::getDBO(); 
		
	
		$rowConfig= new config( $database );
		if (!$rowConfig->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listConfig" );
			exit();
		}		
				
		if (!$rowConfig->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
	
		
	}


}

?>
