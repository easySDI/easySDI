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

class ADMIN_resources {

	
	function listResources($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
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
	
		HTML_resources::listResources($use_pagination, $rows, $pageNav,$option);
	}

	
	//id = 0 means new Config entry
	function editResource( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowConfig = new config( $database );
		$rowConfig->load( $id );
		 

		HTML_resources::editResource( $rowConfig,$option );
	}

}

?>
