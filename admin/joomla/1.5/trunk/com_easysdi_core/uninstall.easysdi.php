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

function com_uninstall(){
	
	global  $mainframe;
	$db =& JFactory::getDBO();
	
	/**
	 * Check dependencies
	 */
	$name = '';
	$query = "SELECT name FROM #__components where option = 'com_easysdi_catalog' AND parent=0";
	$db->setQuery( $query);
	$name = $db->loadResult();
	if ($name) {
		$mainframe->enqueueMessage("DEPENDENT COMPONENT CATALOG IS INSTALLED. CAN NOT UNINSTALL CORE","ERROR");
		exit;		
	}
	$name = '';
	$query = "SELECT name FROM #__components where option = 'com_easysdi_proxy' AND parent=0";
	$db->setQuery( $query);
	$name = $db->loadResult();
	if ($name) {
		$mainframe->enqueueMessage("DEPENDENT COMPONENT PROXY IS INSTALLED. CAN NOT UNINSTALL CORE","ERROR");
		exit;		
	}
	
	/**
	 * Delete components
	 */
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_partner' ";	
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}
	
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_core' ";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
		
	
	$mainframe->enqueueMessage("Congratulation EasySdi core component is uninstalled.
	Pay attention the database is not deleted and could still be used if you install Easysdi again. 
	","INFO");

}


?>