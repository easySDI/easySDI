<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

jimport( 'joomla.filesystem.folder' );

function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');
 
	 //Check the CORE installation
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE  `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Publish could not be installed. Please install core component first.","ERROR");
		// Delete component
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_publish'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}

	// Gets the component version
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'PUBLISH'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		/*
		* EasySDI parameters
		*/
		
		$version= '0.1';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'PUBLISH', 'com_easysdi_publish', 'com_easysdi_publish', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_publish', 'com_sdi_publish', '".$version."')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$module_id = $db->insertid();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'PUBLISH_PANEL', 'Publish Panel', 'Publish Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$module_id."', 'com_easysdi_publish/core/sub.ctrlpanel.admin.easysdi.html.php', '5')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	        
		//Insert configuration keys
		$query = "INSERT  INTO #__sdi_configuration (guid, code, name, description, created, createdby,  value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'WPS_PUBLISHER', 'WPS_PUBLISHER', 'PUBLISH', '".date('Y-m-d H:i:s')."', '".$user_id."',  'http://localhost:8080/sdi_publish/wps', '".$module_id."')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		
		/*
		* Publish parameters
		*/
		$query ="SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/*TODO: put here all tables and values required for a new installation*/
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_config` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `default_publisher_layer_number` int(20) unsigned DEFAULT NULL,
		  `default_dataset_upload_size` int(20) unsigned DEFAULT NULL,
		  `default_diffusion_server_id` bigint(20) unsigned DEFAULT NULL,
		  `default_datasource_handler` bigint(20) unsigned DEFAULT NULL,
		  `default_prefered_crs` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`)
		 )"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
    $query="CREATE TABLE IF NOT EXISTS `#__sdi_publish_crs` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `code` varchar(45) DEFAULT NULL,
      `name` varchar(100) DEFAULT NULL,
      PRIMARY KEY (`id`)
    )";
    $db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_diffuser` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `diffusor_type_id` bigint(20) unsigned DEFAULT NULL,
		  `diffusion_server_host` varchar(45) DEFAULT NULL,
		  `diffusion_server_port` int(10) unsigned DEFAULT NULL,
		  `diffusion_server_name` varchar(45) DEFAULT NULL,
		  `diffusion_server_service_name` varchar(45) DEFAULT NULL,
		  `diffusion_server_password` varchar(45) DEFAULT NULL,
		  `diffusion_server_username` varchar(45) DEFAULT NULL,
		  `diffusion_server_db_name` varchar(45) DEFAULT NULL,
		  `diffusion_server_db_schema` varchar(45) DEFAULT NULL,
		  `diffusion_server_db_port` int(20) unsigned DEFAULT NULL,
		  `diffusion_server_db_username` varchar(45) DEFAULT NULL,
		  `diffusion_server_db_password` varchar(400) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_diffuser_type` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `type` varchar(45) NOT NULL,
		  PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_script` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `publish_script_name` varchar(45) DEFAULT NULL,
		  `publish_script_display_name` varchar(45) DEFAULT NULL,
		  `publish_script_description` varchar(400) DEFAULT NULL,
		  `publish_script_is_public` tinyint(1) unsigned DEFAULT NULL,
		 PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_script_map` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `publish_user_id` bigint(20) unsigned DEFAULT NULL,
		  `publish_script_id` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_user` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `easysdi_user_id` bigint(20) unsigned DEFAULT NULL,
		  `publish_user_max_layers` int(20) unsigned DEFAULT NULL,
		  `publish_user_total_space` int(20) unsigned DEFAULT NULL,
		  `publish_user_diff_server_id` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}		
		
		$query="CREATE TABLE IF NOT EXISTS  `#__sdi_publish_featuresource` (
 		 `id` bigint(20) unsigned NOT NULL auto_increment,
 		 `featureGUID` varchar(100) default NULL,
 		 `partner_id` bigint(20) unsigned default NULL,
 		 `name` varchar(100) default NULL,
 		 `projection` varchar(100) default NULL,
 		 `formatId` bigint(20) unsigned default NULL,
 		 `scriptId` bigint(20) unsigned default NULL,
 		 `fileList` varchar(1000) default NULL,
 		 `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
 		 `update_date` timestamp NOT NULL default '0000-00-00 00:00:00',
 		  PRIMARY KEY  (`id`)
		)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="CREATE TABLE IF NOT EXISTS `#__sdi_publish_layer` (
		  `id` bigint(20) unsigned NOT NULL auto_increment,
		  `featuresourceId` bigint(20) unsigned default NULL,
		  `name` varchar(100) default NULL,
		  `title` varchar(100) default NULL,
		  `description` varchar(1000) default NULL,
		  `quality_area` varchar(500) default NULL,
		  `keywords` varchar(1000) default NULL,
		  `layerGuid` varchar(100) default NULL,
		  `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  `update_date` timestamp NOT NULL default '0000-00-00 00:00:00',
		  `partner_id` bigint(20) unsigned default NULL,
		  `wmsUrl` varchar(1000) DEFAULT NULL,
  		`wfsUrl` varchar(1000) DEFAULT NULL,
  		`kmlUrl` varchar(1000) DEFAULT NULL,
  		`bbox` varchar(400) default NULL,
  		`geometry` varchar(45) default NULL,
		  PRIMARY KEY  (`id`)
		)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		
		//
		//Insert default values
		//
		
		//Add a diffuser type (geoserver)
		$query = "insert into `#__sdi_publish_diffuser_type` (type)
			values('geoserver')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Insert a default diffuser
		$db->setQuery( "SELECT id FROM #__sdi_publish_diffuser_type where type='geoserver'");
		$query = "insert into `#__sdi_publish_diffuser` (diffusion_server_name, diffusor_type_id)
			values('diffuserlocalhost', '".$db->loadResult()."')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add the default OGR format
		$query = "insert into `#__sdi_publish_script` (publish_script_name, publish_script_display_name, publish_script_is_public)
			values('OGR', 'built-in', '1')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add crs systems
		$query = "insert into `#__sdi_publish_crs` (code, name)
			values('EPSG:4326', 'WGS84 - lon/lat'),('EPSG:21781','CH1903 / LV03'),('EPSG:26986','North American Datum 1983'),('EPSG:2277','NAD83 / Texas Central (ftUS)')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Some value in the default config
		$db->setQuery( "SELECT id FROM #__sdi_publish_diffuser where diffusion_server_name='diffuserlocalhost'");
		$dfltDiffId = $db->loadResult();
		$db->setQuery( "SELECT id FROM #__sdi_publish_script where publish_script_name='OGR'");
		$dfltScriptId = $db->loadResult();
		
		$query = "insert into `#__sdi_publish_config` (default_publisher_layer_number, default_dataset_upload_size, default_diffusion_server_id, default_datasource_handler,default_prefered_crs)
			values('10', '15', '".$dfltDiffId."', '".$dfltScriptId."','2')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add roles
		$db->setQuery( "SELECT id FROM #__sdi_list_role where code='GEOSERVICE_DATA_MANA'");
		if($db->loadResult() == 0){
			$query="INSERT INTO `#__sdi_list_role` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `publish_id`, `roletype_id`) VALUES
					('".helper_easysdi::getUniqueId()."', 'GEOSERVICE_DATA_MANA', 'GEOSERVICE_DATA_MANA', 'CORE_ACCOUNT_GEOSERVICE_DATA_MANAGER_RIGHT', 'Manages underlying geoservice data', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1')";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		$db->setQuery( "SELECT id FROM #__sdi_list_role where code='GEOSERVICE_MANAGER'");
		if($db->loadResult() == 0){	
			$query="INSERT INTO `#__sdi_list_role` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `publish_id`, `roletype_id`) VALUES
					('".helper_easysdi::getUniqueId()."', 'GEOSERVICE_MANAGER', 'GEOSERVICE_MANAGER', 'CORE_ACCOUNT_GEOSERVICE_MANAGER_RIGHT', 'Manages geoservice layers', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1')";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
	}
	if ($version == "0.1")
	{
		// Update component version
		$version="2.2.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PUBLISH'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	
	
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_publish' ";
	$db->setQuery($query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Publish','option=com_easysdi_publish&task=editGlobalSettings','Easysdi Publish','com_easysdi_publish','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation Publish for EasySDI is installed and ready to be used. Enjoy EasySdi Publish!
	 Do not forget to check/change the WPS_PUBLISHER key depending on your servlet container location.","INFO");

}


?>