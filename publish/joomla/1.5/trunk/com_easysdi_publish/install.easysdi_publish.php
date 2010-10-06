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


function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();

	/**
	 * Check the CORE installation
	 */
	 
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE  `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Shop could not be installed. Please install core component first.","ERROR");
		/**
		 * Delete components
		 */
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_shop'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				
		}
		return false;
	}

	/**
	 * Creates the database structure
	 */
	 
	 
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
	$query = "SELECT version FROM #__easysdi_version where component = 'com_easysdi_publish'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if ($db->getErrorNum()) {
		$version = '0';
		//	$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}
	if (!$version){
		$version="0";
	}
	if (strlen($version)==0){$version ='0';}

	//When there is no DB version, then we create the full db
	if ($version == '0')
	{
		$query ="SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="INSERT INTO #__easysdi_version (id,component,version) VALUES (null, 'com_easysdi_publish', '0.1')";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/*TODO: put here all tables and values required for a new installation*/
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_config` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `default_publisher_layer_number` int(20) unsigned DEFAULT NULL,
		  `default_dataset_upload_size` int(20) unsigned DEFAULT NULL,
		  `default_diffusion_server_id` bigint(20) unsigned DEFAULT NULL,
		  `default_datasource_handler` bigint(20) unsigned DEFAULT NULL,
		  PRIMARY KEY (`id`)
		 )"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_diffusor` (
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
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_diffusor_type` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `type` varchar(45) NOT NULL,
		  PRIMARY KEY (`id`)
		)"; 
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_script` (
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
			
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_script_map` (
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
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_user` (
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
		
		$query="CREATE TABLE IF NOT EXISTS  `#__easysdi_publish_featuresource` (
 		 `id` bigint(20) unsigned NOT NULL auto_increment,
 		 `featureGUID` varchar(100) default NULL,
 		 `partner_id` bigint(20) unsigned default NULL,
 		 `name` varchar(100) default NULL,
 		 `projection` varchar(100) default NULL,
 		 `formatId` bigint(20) unsigned default NULL,
 		 `scriptId` bigint(20) unsigned default NULL,
 		 `fileList` varchar(1000) default NULL,
 		 `fieldsName` varchar(500) DEFAULT NULL,
 		 `fieldsaliases` varchar(1000) default NULL,
 		 `creation_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
 		 `update_date` timestamp NOT NULL default '0000-00-00 00:00:00',
 		  PRIMARY KEY  (`id`)
		)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="CREATE TABLE IF NOT EXISTS `#__easysdi_publish_layer` (
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
		$query = "insert into `#__easysdi_publish_diffusor_type` (type)
			values('geoserver')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Insert a default diffuser
		$db->setQuery( "SELECT id FROM #__easysdi_publish_diffusor_type where type='geoserver'");
		$query = "insert into `#__easysdi_publish_diffusor` (diffusion_server_name, diffusor_type_id)
			values('diffuserlocalhost', '".$db->loadResult()."')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Some value in the default config
		$db->setQuery( "SELECT id FROM #__easysdi_publish_diffusor where diffusion_server_name='diffuserlocalhost'");
		$query = "insert into `#__easysdi_publish_config` (default_publisher_layer_number, default_dataset_upload_size, default_diffusion_server_id)
			values('10', '15', '".$db->loadResult()."')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add roles
		$db->setQuery( "SELECT role_id FROM #__easysdi_community_role where role_code='GEOSERVICE_DATA_MANA'");
		if($db->loadResult() == 0){
			$query = "insert into `#__easysdi_community_role` (publish_id, type_id, role_code, role_name, role_description, role_update) values(0, 1, 'GEOSERVICE_DATA_MANA', 'EASYSDI_GEOSERVICE_DATA_MANAGER_RIGHT', null, null)";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		$db->setQuery( "SELECT role_id FROM #__easysdi_community_role where role_code='GEOSERVICE_MANAGER'");
		if($db->loadResult() == 0){
			$query = "insert into `#__easysdi_community_role` (publish_id, type_id, role_code, role_name, role_description, role_update) values(0, 1, 'GEOSERVICE_MANAGER', 'EASYSDI_GEOSERVICE_MANAGER_RIGHT', null, null)";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
		}
		
		//Add the default OGR format
		$query = "insert into `#__easysdi_publish_script` (publish_script_name, publish_script_is_public)
			values('OGR', '1')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add the WPS_PUBLISHER key to the config
		$query = "insert into `#__easysdi_config` (thekey, value) values('WPS_PUBLISHER', 'http://localhost:8083/servletWPS/WPSServlet')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$version = "0.1";
	}
	if ($version == "0.1")
	{
		$query="UPDATE #__easysdi_version set version = '0.1' where component = 'com_easysdi_publish'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$version = "0.2";
	}
	
	/**
	 * Menu creation in Joomla!
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

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_publish' ";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
			}
		
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
	values($id,'Publish','','option=com_easysdi_publish','Publish','com_easysdi_publish','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Publish','option=com_easysdi_publish','EasySDI Publish','com_easysdi_publish','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	$mainframe->enqueueMessage("Congratulation! Publish for EasySdi is installed and ready to be used.
	Enjoy EasySdi - Publish!","INFO");

}


?>