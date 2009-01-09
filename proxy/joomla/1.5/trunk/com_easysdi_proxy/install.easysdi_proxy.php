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


function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();

	
		if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'license.txt')){

		$mainframe->enqueueMessage("Core component does not exists. Easysdi_proxy could not be installed. Please install core component first.","ERROR");
		return false;
		
	}
	
	
	/**
	 * Creates the database structure
	 */
	/**
	 * Gets the component versions
	 */
	
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();

	if ($id){
		
	}else{
		
	$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
	return false;	
	//Insert the EasySdi Main Menu		
	/*$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Easy SDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	*/
	}

	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'Proxy','','option=com_easysdi_proxy&task=showConfigList','Proxy','com_easysdi_proxy','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'Proxy Config','','option=com_easysdi_proxy&task=componentConfig','Proxy Config','com_easysdi_proxy','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	
	$mainframe->enqueueMessage("Congratulation proxy manager for EasySdi is installed and ready to be used. Enjoy EasySdi!","INFO");
	return true;
}


?>