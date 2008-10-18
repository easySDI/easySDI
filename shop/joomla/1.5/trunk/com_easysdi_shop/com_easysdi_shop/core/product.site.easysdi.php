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

class SITE_product {

	function listProduct(){

		
		global  $mainframe;
		$option=JRequest::getVar("option");
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 5 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		$database =& JFactory::getDBO();		 	
		$user = JFactory::getUser();
		$rootPartner = new partnerByUserId($database);
		$rootPartner->load($user->id);		
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (data_title LIKE '%$search%')";			
		}
		$partner = new partnerByUserId($database);
		$partner->load($user->id);
		
			
		$queryCount = "select count(*) from #__easysdi_product where partner_id = (partner_id = $partner->partner_id AND internal=1) OR (external=1) ";
		
		  
		
		$queryCount .= $filter;
		
		
		
		$database->setQuery($queryCount);
		$total = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
		$query = "select * from #__easysdi_product where partner_id = (partner_id = $partner->partner_id AND internal=1) OR (external=1) ";
		$query .= $filter;
	

echo 	$query;
		$database->setQuery($query,$limitstart,$limit);		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		HTML_product::listProduct($pageNav,$rows,$option);
		
}
}
?>