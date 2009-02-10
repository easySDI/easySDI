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
	
	/**
	 * Check dependencies
	 */
	$name = '';
	$query = "SELECT name FROM #__components where option = 'com_asitvd' AND parent=0";
	$db->setQuery( $query);
	$name = $db->loadResult();
	if ($name) {
		$mainframe->enqueueMessage("DEPENDENT COMPONENT ASITVD IS INSTALLED. CAN NOT UNINSTALL CATALOG","ERROR");
		exit;		
	}
	
	/**
	 * Delete components
	 */
	$db =& JFactory::getDBO();
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_shop'";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
	
		
	$mainframe->enqueueMessage("Congratulation EasySdi shop is uninstalled.
	Pay attention the database is not deleted and could still be used if you install Easysdi again. 
	","INFO");
	

}


?>