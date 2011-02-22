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

function com_uninstall(){
	
	global  $mainframe;
	$db =& JFactory::getDBO();
	
	/**
	 * Check dependencies
	 */
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_catalog' ";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count > 0) {
		$mainframe->enqueueMessage("WARNING : Dependent component catalog is installed. You must uninstall it.","ERROR");
		//return false;		
	}
	
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` where `option` =  'com_easysdi_proxy' ";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count > 0) {
		$mainframe->enqueueMessage("WARNING : Dependent component proxy is installed. You must uninstall it.","ERROR");
		//return false;		
	}
	
	/**
	 * Delete components
	 */
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_partner' ";	
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_core' ";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		return false;
	}
	
		
	
	$mainframe->enqueueMessage("Congratulation EasySdi core component is uninstalled.
	Pay attention the database is not deleted and could still be used if you install Easysdi again. 
	","INFO");
	
	return true;

}


?>