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
	
	/*if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'license.txt')){
		$mainframe->enqueueMessage("Core component does not exists. Easysdi_catalog could not be installed. Please install core component first.","ERROR");
		return false;
	}*/
	
	/**
	 * Check the CORE installation
	 */
	$name = '';
	$query = "SELECT name FROM #__components where option = 'com_easysdi_core' AND parent=0";
	$db->setQuery( $query);
	$name = $db->loadResult();
	if (!$name) {
		$mainframe->enqueueMessage("EASYSDI CORE IS NOT INSTALLED","ERROR");
		exit;		
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
	
	$version = '0';
	$query = "SELECT version FROM #__easysdi_version where component = 'com_easysdi_catalog'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version= '0.9';
		$query="INSERT INTO #__easysdi_version (id,component,version) VALUES (null, 'com_easysdi_catalog', '0.9')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		/**
		 * Insert value for CATALOG_URL in configuration table
		 */
		$key='';
		$query = "insert  into #__easysdi_config (thekey, value) values('CATALOG_URL','http://localhost:8081/proxy/ogc/geonetwork')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	
	
	$mainframe->enqueueMessage("Congratulation catalog for EasySdi is installed and ready to be used. Enjoy EasySdi!","INFO");
	return true;
}


?>