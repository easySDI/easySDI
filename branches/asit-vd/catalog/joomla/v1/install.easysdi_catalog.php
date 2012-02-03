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
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Catalog could not be installed. Please install core component first.","ERROR");
		/**
		 * Delete components
		 */
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_catalog'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;		
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
		return false;
	}
	
	$version = '0';
	$query = "SELECT version FROM `#__easysdi_version` where `component` = 'com_easysdi_catalog'";
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
			return false;
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
			return false;
		}
	}
	if($version== '0.9')
	{
			
		$version = '0.91';
		$query="UPDATE #__easysdi_version set version = '0.91' where component = 'com_easysdi_catalog'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}
	if($version == '0.91')
	{	
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_metadata_topic_category` (			  
			  `id` bigint(20) NOT NULL auto_increment,
			  `code` varchar(100) NOT NULL UNIQUE default '',
			  `value` varchar(100) NOT NULL default '',
			  PRIMARY KEY  (`id`)
				)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$query = "INSERT INTO `#__easysdi_metadata_topic_category` (`code`, `value`) VALUES 
				  ('farming', 'EASYSDI_METADATA_CATEGORY_FARMING'),
				  ('biota', 'EASYSDI_METADATA_CATEGORY_BIOTA'),
				  ('bounderies', 'EASYSDI_METADATA_CATEGORY_BOUNDERIES'),
				  ('climatologyMeteorologyAtmosphere', 'EASYSDI_METADATA_CATEGORY_CLIMATOLOGYMETEOROLOGYATMOSPHERE'),
				  ('economy', 'EASYSDI_METADATA_CATEGORY_ECONOMY'),
				  ('elevation', 'EASYSDI_METADATA_CATEGORY_ELEVATION'),
				  ('environment', 'EASYSDI_METADATA_CATEGORY_ENVIRONMENT'),
				  ('geoscientificinformation', 'EASYSDI_METADATA_CATEGORY_GEOSCIENTIFICINFORMATION'),
				  ('health', 'EASYSDI_METADATA_CATEGORY_HEALTH'),
				  ('imageryBaseMapsEarthCover', 'EASYSDI_METADATA_CATEGORY_IMAGERYBASEMAPSEARTHCOVER'),
				  ('intelligenceMilitary', 'EASYSDI_METADATA_CATEGORY_INTELLIGENCEMILITARY'),
				  ('inlandWaters', 'EASYSDI_METADATA_CATEGORY_INLANDWATERS'),
				  ('location', 'EASYSDI_METADATA_CATEGORY_LOCATION'),
				  ('oceans', 'EASYSDI_METADATA_CATEGORY_OCEANS'),
				  ('planningCadastre', 'EASYSDI_METADATA_CATEGORY_PLANNINGCADASTRE'),
				  ('society', 'EASYSDI_METADATA_CATEGORY_SOCIETY'),
				  ('structure', 'EASYSDI_METADATA_CATEGORY_STRUCTURE'),
				  ('transportation', 'EASYSDI_METADATA_CATEGORY_TRANSPORTATION'),
				  ('utilitiesCommunication','EASYSDI_METADATA_CATEGORY_UTILITIESCOMMUNICATION')
				";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$version = '0.92';
		$query="UPDATE #__easysdi_version set version = '0.92' where component = 'com_easysdi_catalog'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}
	
	/**
	 * Copy View files in Core component to allow  Menu Item Manger to find entries
	 */
		/*	mkdir(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'views'.DS.'catalog', 0700);
			mkdir(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'views'.DS.'catalog'.DS.'tmpl', 0700);
			$file = JPATH_SITE.DS.'components'.DS.'com_easysdi_catalog'.DS.'views'.DS.'catalog'.DS.'metadata.xml';
			$newfile = JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'views'.DS.'catalog'.DS.'metadata.xml';
			if (!copy($file, $newfile)) {
			    $mainframe->enqueueMessage("Failed to copy VIEWS file in Core component","ERROR");
				return false;
			}
			$file = JPATH_SITE.DS.'components'.DS.'com_easysdi_catalog'.DS.'views'.DS.'catalog'.DS.'tmpl'.DS.'default.xml';
			$newfile = JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'views'.DS.'catalog'.DS.'tmpl'.DS.'default.xml';
			if (!copy($file, $newfile)) {
			    $mainframe->enqueueMessage("Failed to copy VIEWS file in Core component","ERROR");
				return false;
			}
			$file = JPATH_SITE.DS.'components'.DS.'com_easysdi_catalog'.DS.'views'.DS.'catalog'.DS.'tmpl'.DS.'default.php';
			$newfile = JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'views'.DS.'catalog'.DS.'tmpl'.DS.'default.php';
			if (!copy($file, $newfile)) {
			   $mainframe->enqueueMessage("Failed to copy VIEWS file in Core component","ERROR");
				return false;
			}*/
	
			$query = "DELETE FROM #__components where `option`= 'com_easysdi_catalog' ";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
			}
			
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Catalog','option=com_easysdi_catalog','Easysdi Catalog','com_easysdi_catalog','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation catalog for EasySdi is installed and ready to be used. Enjoy EasySdi Catalog!","INFO");
	return true;
}


?>