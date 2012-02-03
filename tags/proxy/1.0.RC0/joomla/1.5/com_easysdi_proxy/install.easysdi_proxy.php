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
		$mainframe->enqueueMessage("Core component does not exist. Easysdi_proxy could not be installed. Please install core component first.","ERROR");
		/**
		 * Delete components
		 */
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_proxy'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}
	
	
	$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_config` (
			  `id` bigint(20) NOT NULL auto_increment,
			  `thekey` varchar(100) NOT NULL default '',
			  `value` varchar(100) NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)"; 
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}	

	$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_version` (
			  `component` varchar(100) NOT NULL default '',
			  `id` bigint(20) NOT NULL auto_increment,
			  `version` varchar(100) NOT NULL default '',
			  PRIMARY KEY  (`id`)
			)"; 		
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}
			
	/**
	 * Gets the component versions
	 */
	$version = '0';
	$query = "SELECT version FROM #__easysdi_version where component = 'com_easysdi_proxy'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if ($db->getErrorNum()) {								
		$version = '0';
	}
	if (!$version){
		$version="0";
	}	
	if (strlen($version)==0){$version ='0';}
				
	//When there is no DB version, then we create the full db
	if ($version == '0') 
	{	
		$query="INSERT INTO #__easysdi_version (id,component,version) VALUES (null, 'com_easysdi_proxy', '0.9')";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		$version='0.9';
		
		/**
		 * Insert value for PROXY_CONFIG in configuration table
		 */
		$query = "insert  into #__easysdi_config (thekey, value) values('PROXY_CONFIG','C:/www/tomcat-5-5/webapps/proxy/conf/config.xml')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		
	}
	
		/**
		 * Menu creation
		 */
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
		$db->setQuery( $query);
		$id = $db->loadResult();
		if ($id)
		{
		}
		else
		{
			$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
			return false;	
		}
	
		$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Proxy','','option=com_easysdi_proxy&task=showConfigList','Proxy','com_easysdi_proxy','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		/*
		$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values($id,'Proxy Config','','option=com_easysdi_proxy&task=componentConfig','Proxy Config','com_easysdi_proxy','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}*/
		
	$mainframe->enqueueMessage("Congratulation proxy manager for EasySdi is installed and ready to be used. Enjoy EasySdi Proxy!","INFO");
	return true;
}


?>