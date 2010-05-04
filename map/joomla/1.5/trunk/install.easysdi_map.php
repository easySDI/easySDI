<?php
defined('_JEXEC') or die('Restricted access');

function com_install()
{
	global  $mainframe;
	$db =& JFactory::getDBO();

	/**
	 * Check the CORE installation
	 */
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Map could not be installed. Please install core component first.","ERROR");
		/**
		 * Delete components
		 */
		/*	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map'";
		 $db->setQuery( $query);
		 if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}*/
		return false;
	}

	/**
	 * Gets the component versions
	 */

	$db->setQuery( "SELECT version FROM #__easysdi_version where component = 'com_easysdi_map'");
	$version = $db->loadResult();
	if (!$version)
	{
		$version="0";
	}

	if ($version == "0")
	{
		/**
		 * Map config
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_config` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `name` varchar(100) NOT NULL default '',
		 	 `value` varchar(500) ,
		 	 `description` varchar(250),		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="INSERT INTO `#__easysdi_map_config` (`name`, `value`, `description`) VALUES
					('componentPath', '".JURI::base()."components/com_easysdi_map/', ''),
					('componentUrl', '".JURI::base()."index.php?option=com_easysdi_map&Itemid=', ''),
					('projection', 'EPSG:2169', ''),
					('pubWfsUrl', 'http://localhost/geoserver/wfs', 'url of publication database Wfs service proxy'),
					('maxFeatures', '1000', 'maximum number of features for WFS requests'),
					('pubFeatureNS', 'http://www.easysdi.org/eai', 'namespace of publication database Wfs service'),
					('pubFeaturePrefix', 'eai', 'prefix of publication database Wfs feature type'),
					('wpsReportsUrl', 'http://localhost:8081/reports/wps', 'path to WPS service for report hook-up'),
					('shp2GmlUrl', 'http://localhost/reports/shp2gml', 'path to Shp to Gml service - needs to be a local proxy as via Ajax'),
					('featureIdAttribute', 'unit_guid', 'name of attribute used to identify features (=the Primary Key)'),
					('maxSearchBars', '3', '')
			";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}


		/**
		 * Projection
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_projection` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `name` varchar(200) NOT NULL default '',
		 	 `title` varchar(200) ,
		 	 `proj4text` varchar(500) NOT NULL default '' ,
		 	 `numDigits` int(2) NOT NULL default '0',
		 	 `enable` tinyint(1) NOT NULL default '1',		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
			
		/**
		 * Insert new roles MNHN specific
		 */
		/*$query="INSERT INTO `#__easysdi_community_role` (`publish_id`, `type_id`, `role_code`, `role_name`, `role_description`, `role_update`) VALUES
		 ( 0, 1, 'DATA_PRECISION', 'EASYSDI_DATA_PRECISION_RIGHT', 'Access to the data precision tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_TAXA', 'EASYSDI_ADV_SEARCH_TAXA_RIGHT', 'Access to the advanced search taxon tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_BIOTOPE', 'EASYSDI_ADV_SEARCH_BIOTOPE_RIGHT', 'Access to the advanced search biotopes tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_TAX_STS', 'EASYSDI_ADV_SEARCH_TAXON_STATUS_RIGHT', 'Access to the advanced search taxon status tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_BIO_STS', 'EASYSDI_ADV_SEARCH_BIOTOPE_STATUS_RIGHT', 'Access to the advanced search biotope status tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_SURVEY', 'EASYSDI_ADV_SEARCH_SURVEY_RIGHT', 'Access to the advanced search surveys tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_CRITERIA', 'EASYSDI_ADV_SEARCH_CRITERIA_RIGHT', 'Access to the advanced search criteria tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_MISC', 'EASYSDI_ADV_SEARCH_MISC_RIGHT', 'Access to the advanced search misc tab', NULL),
		 ( 0, 1, 'ADV_SEARCH_PLACE', 'EASYSDI_ADV_SEARCH_PLACE_RIGHT', 'Access to the advanced search place filter tab', NULL),
		 ( 0, 1, 'SEARCH_SAVE_LOAD', 'EASYSDI_SEARCH_SAVE_LOAD_RIGHT', 'Access to the save and load searches functionnality', NULL)
		 ";*/
		$query="INSERT INTO `#__easysdi_community_role` (`publish_id`, `type_id`, `role_code`, `role_name`, `role_description`, `role_update`) VALUES
					( 0, 1, 'DATA_PRECISION', 'EASYSDI_DATA_PRECISION_RIGHT', 'Access to the data precision tab', NULL),		
					( 0, 1, 'ADV_SEARCH_MISC', 'EASYSDI_ADV_SEARCH_MISC_RIGHT', 'Access to the advanced search misc tab', NULL),
					( 0, 1, 'ADV_SEARCH_PLACE', 'EASYSDI_ADV_SEARCH_PLACE_RIGHT', 'Access to the advanced search place filter tab', NULL),
					( 0, 1, 'SEARCH_SAVE_LOAD', 'EASYSDI_SEARCH_SAVE_LOAD_RIGHT', 'Access to the save and load searches functionnality', NULL)
					";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Overlay
		 */
		$query = "CREATE TABLE  IF NOT EXISTS  `#__easysdi_overlay_definition` (
			  `id` int(20) NOT NULL auto_increment,
			  `projection` varchar(100) NOT NULL default 'EPSG:4326',
			  `unit` varchar(100) NOT NULL default '',
			  `minResolution` varchar(100) NOT NULL default 'auto',
			  `maxResolution` varchar(100) NOT NULL default 'auto',
			  `def` tinyint(1) NOT NULL default '0',
			  `maxExtent` varchar(100) NOT NULL default '-180,-90,180,90',
			  `alias` varchar(400) ,
			  PRIMARY KEY  (`id`)
			)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "CREATE TABLE  IF NOT EXISTS  `#__easysdi_overlay_content` (
			  `id` bigint(20) NOT NULL auto_increment,
			  `overlay_def_id` bigint(20) NOT NULL default '0',
			  `url` varchar(400) NOT NULL default '',
			  `url_type` varchar(100) NOT NULL default '',
			  `singletile` tinyint(1) NOT NULL default '0',
			  `maxExtent` varchar(100) NOT NULL default '',
			  `minResolution` varchar(100) NOT NULL default 'auto',
			  `maxResolution` varchar(100) NOT NULL default 'auto',
			  `projection` varchar(100) NOT NULL default '',
			  `unit` varchar(100) NOT NULL default '',
			  `name` varchar(100) NOT NULL default '',
			  `layers` varchar(300) NOT NULL default '',
			  `img_format` varchar(100) NOT NULL default 'image/png',
			  `ordering` int(11) default '0',
			  `user` varchar(400) ,
			  `password` varchar(400) ,	
			  `overlay_group_id` BIGINT(20),  
			  PRIMARY KEY  (`id`)
			)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query = "CREATE TABLE  IF NOT EXISTS #__easysdi_overlay_group
			(
			id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
			name VARCHAR(30)
			)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
			
		/**
		 * Results grids
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_extra_result_grid` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `internal_name` varchar(100) NOT NULL default '',
		 	 `title` varchar(500) ,
		 	 `feature_type` varchar(100) NOT NULL default '',
		 	 `distinct_fk` varchar(100) NOT NULL default '',		 	 
		 	 `distinct_pk` varchar(100)NOT NULL default '',
		 	 `row_detail_feature_type` varchar(500) ,
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Simple search types
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_simple_search_type` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `title` varchar(500) NOT NULL default '' ,
		 	 `dropdown_feature_type` varchar(100) NOT NULL default '',
		 	 `dropdown_display_attr` varchar(100) NOT NULL default '',		 	 
		 	 `dropdown_id_attr` varchar(100) NOT NULL default '',
		 	 `search_attribute` varchar(500) NOT NULL default '',
		 	 `operator` varchar(5) NOT NULL default '',		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Simple search additional filters
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_simple_search_additional_filter` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `attribute` varchar(100) NOT NULL default '',		 	 
		 	 `value` varchar(100) NOT NULL default '',
		 	 `operator` varchar(5) NOT NULL default '',		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_sst_saf` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_sst` bigint(20) NOT NULL ,		 	 
		 	 `id_saf` bigint(20) NOT NULL ,		 	 		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_sst_saf`
					ADD FOREIGN KEY ( `id_sst` ) REFERENCES `#__easysdi_map_simple_search_type` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_sst_saf`
					ADD FOREIGN KEY (`id_saf`) REFERENCES `#__easysdi_map_simple_search_additional_filter`
					 (id) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_sst_erg` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_sst` bigint(20) NOT NULL ,		 	 
		 	 `id_erg` bigint(20) NOT NULL ,		 	 		 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_sst_erg`
					ADD FOREIGN KEY ( `id_sst` ) REFERENCES `#__easysdi_map_simple_search_type` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_sst_erg`
					ADD FOREIGN KEY (`id_erg`) REFERENCES `#__easysdi_map_extra_result_grid`
					 (id) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Search Layer
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_search_layer` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `feature_type` varchar(100) NOT NULL default '' ,
		 	 `geometry_name` varchar(100) NOT NULL default '',
		 	 `row_details_feature_type` varchar(100),
		 	 `styles` varchar(500),		
		 	 `enable` tinyint(1) NOT NULL default '0', 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Precision
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_precision` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `name` varchar(100) NOT NULL default '' ,
		 	 `title` varchar(250),
		 	 `min_resolution` bigint(20) ,
		 	 `max_resolution` bigint(20) ,
		 	 `low_scale_switch_to` varchar(100),		
		 	  `style` varchar(500),
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		/**
		 * Feature type and attributes
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_feature_type` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `name` varchar(100) NOT NULL default '' ,		 	
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_use` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `name` varchar(100) NOT NULL default '' ,	
		 	 `translation` varchar(100)  ,
		 	 `description` varchar(500)  ,			 	
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="INSERT INTO `#__easysdi_map_use` (`name`,`translation`,`description`)
					VALUES ('searchLayer','EASYSDI_MAP_USE_SEARCH_LAYER','Feature type used in the main serach layer'),
					 ('extraDistinctGrid','EASYSDI_MAP_USE_DISTINCT_GRID','Feature type used in the extra distinct grid'),
					('rowDetails','EASYSDI_MAP_USE_ROW_DETAIL','Feature type used in the row details to produce report')"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_feature_type_use` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_ft` bigint(20) NOT NULL ,	
		 	 `id_use` bigint(20) NOT NULL ,		 	
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_attribute` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_ft` bigint(20) NOT NULL ,
		 	 `name` varchar(100) NOT NULL default '' ,	
		 	 `data_type` varchar(100) NOT NULL default '' ,
		 	 `width` bigint(20)  ,
		 	 `initial_visibility` tinyint(1) NOT NULL default '0' ,				 	
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_attribute`
					ADD FOREIGN KEY ( `id_ft` ) REFERENCES `#__easysdi_map_feature_type` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_attribute_profile` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_attr` bigint(20) NOT NULL ,		 	 			 	
		 	 `id_prof` bigint(20) NOT NULL ,
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_attribute_profile`
					ADD FOREIGN KEY ( `id_attr` ) REFERENCES `#__easysdi_map_attribute` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_attribute_profile`
					ADD FOREIGN KEY ( `id_prof` ) REFERENCES `#__easysdi_community_profile` 
					(`profile_id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_feature_type_use`
					ADD FOREIGN KEY ( `id_ft` ) REFERENCES `#__easysdi_map_feature_type` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_feature_type_use`
					ADD FOREIGN KEY ( `id_use` ) REFERENCES `#__easysdi_map_use` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
		/**
		 * Alter table to allow the use of __easysdi_map_feature_type_use objects
			*/
		$query ="ALTER TABLE `#__easysdi_map_search_layer`
					MODIFY  COLUMN feature_type bigint(20)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_search_layer`
					MODIFY  COLUMN row_details_feature_type bigint(20)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_extra_result_grid`
					MODIFY  COLUMN feature_type bigint(20)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_extra_result_grid`
					MODIFY  COLUMN row_detail_feature_type bigint(20)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_extra_result_grid`
					CHANGE  COLUMN  row_detail_feature_type row_details_feature_type bigint(20)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_use` ADD UNIQUE (`name`)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Profile and right relation
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_profile_role` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `id_role` bigint(20) NOT NULL ,		 	 			 	
		 	 `id_prof` bigint(20) NOT NULL ,
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_profile_role`
					ADD FOREIGN KEY ( `id_role` ) REFERENCES `#__easysdi_community_role` 
					(`role_id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_profile_role`
					ADD FOREIGN KEY ( `id_prof` ) REFERENCES `#__easysdi_community_profile` 
					(`profile_id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Add a field to map_feature_type to allow the definition of the geometry used in a case of a rowDetailsFeatureType
			*/
		$query ="ALTER TABLE `#__easysdi_map_feature_type`
					ADD  COLUMN geometry varchar(100)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Add a table to store comments feature type
			*/
		$query="CREATE TABLE  IF NOT EXISTS `#__easysdi_map_comment_feature_type` (
		 	 `id` bigint(20) NOT NULL auto_increment,
		 	 `type_name` varchar(250) NOT NULL ,		 	 			 	
		 	 `feature_comment_count` varchar(250) NOT NULL ,
		 	 `enable` tinyint(1) NOT NULL default '0', 	 
		 	 PRIMARY KEY  (`id`)
			)"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$version = "0.1";
		$query="INSERT INTO #__easysdi_version (id,component,version) VALUES (null, 'com_easysdi_map', '0.1')";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "0.1")
	{
		/**
		 * Simple search additional filters
			*/
		$query="ALTER TABLE  `#__easysdi_map_simple_search_additional_filter`
		 	 CHANGE  `operator` `operator` varchar(5) NOT NULL default '=='"; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Simple search additional filters
			*/
		$query="ALTER TABLE  `#__easysdi_map_simple_search_additional_filter`
		 	 ADD COLUMN `title` varchar(200) "; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Feature type attribute
			*/
		$query="ALTER TABLE  `#__easysdi_map_attribute`
		 	 ADD COLUMN `visible` tinyint(1) NOT NULL default '0' "; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
		/**
			* Map context
			* */
		$query="CREATE TABLE #__easysdi_map_context(
					id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
					user_id INT NOT NULL,
					WMC_text TEXT
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
			* Map filter
			* */
		$query="CREATE TABLE #__easysdi_map_filter(
					id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
					user_id INT NOT NULL,
					title VARCHAR(100) NOT NULL,
					description VARCHAR(1000) NOT NULL,
					filter_data TEXT NOT NULL,
					filter_mode INT NOT NULL
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			

		//Service account support for anonymous use of the proxy
		$query="CREATE TABLE IF NOT EXISTS #__easysdi_map_service_account (
					id bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					partner_id bigint(20) NOT NULL 
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
		/**
		 * Feature type attribute
			*/
		$query="ALTER TABLE  `#__easysdi_map_attribute`
		 	 ADD COLUMN `misc_search` tinyint(1) NOT NULL default '0' "; 		
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
		/*
		 * Extension support
		 */
		$query="CREATE TABLE IF NOT EXISTS #__easysdi_map_extension (
					id bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					extended_object varchar(100),
					extension_object  varchar(100)					 
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="CREATE TABLE IF NOT EXISTS #__easysdi_map_extension_resource (
					id bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					id_ext bigint(20), 
					resource_type varchar(100),
					resource_folder  varchar(500),
					resource_file  varchar(500)					 
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_extension_resource`
					ADD FOREIGN KEY ( `id_ext` ) REFERENCES `#__easysdi_map_extension` 
					(`id`) ON DELETE CASCADE ";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		/*
		 * Display options
		 */
		$query="CREATE TABLE IF NOT EXISTS #__easysdi_map_display_options (
					id bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					translation varchar(200),
					object varchar(100),
					enable tinyint(1) NOT NULL DEFAULT '1'	 
					)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		/*
		 * Component param to allow WMS filtering
		 */
		$query="INSERT INTO `#__easysdi_map_config` (`name`, `value`, `description`) VALUES
					('WMSFilterSupport', 'false', 'Does the server support filters in WMS requests.')
			";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="INSERT INTO `#__easysdi_map_display_options` ( `object`, `translation`, `enable`) VALUES
				( 'SimpleSearch', 'EASYSDI_MAP_SIMPLESEARCH', 1),
				( 'AdvancedSearch', 'EASYSDI_MAP_ADVANCEDSEARCH', 1),
				( 'DataPrecision', 'EASYSDI_MAP_DATAPRECISION', 1),
				( 'Localisation', 'EASYSDI_MAP_LOCALISATION', 1),
				( 'ToolBar', 'EASYSDI_MAP_TOOLBAR', 1),
				( 'MapOverview', 'EASYSDI_MAP_MAPOVERVIEW', 1)
				";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
			
		/*
		 * Layers
		 */
		/*$query="ALTER TABLE  `#__easysdi_overlay_content`
		 DROP COLUMN overlay_def_id,
		 DROP COLUMN user,
		 DROP COLUMN password,
		 DROP COLUMN ordering,
		 DROP COLUMN singletile";
			$db->setQuery( $query);
			if (!$db->query())
			{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}*/
		/*
			$query="ALTER TABLE  `#__easysdi_overlay_content`
			RENAME TO `#__easysdi_map_overlay_layer` ";
			$db->setQuery( $query);
			if (!$db->query())
			{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			*/
		/*
			$query="DROP TABLE #__easysdi_overlay_definition";
			$db->setQuery( $query);
			if (!$db->query())
			{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			*/
		$query = "CREATE TABLE IF NOT EXISTS `#__easysdi_map_base_definition` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `projection` varchar(100) NOT NULL DEFAULT 'EPSG:4326',
				  `unit` varchar(100) NOT NULL DEFAULT '',
				  `minResolution` varchar(100) NOT NULL DEFAULT 'auto',
				  `maxResolution` varchar(100) NOT NULL DEFAULT 'auto',
				  `def` tinyint(1) NOT NULL DEFAULT '0',
				  `maxExtent` varchar(100) NOT NULL DEFAULT '-180,-90,180,90',
				  PRIMARY KEY (`id`)
				)";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="
			CREATE TABLE `#__easysdi_map_base_layer` (
			  `id` bigint(20) NOT NULL auto_increment,
			  `id_base` bigint(20) NOT NULL ,
			  `name` varchar(100) NOT NULL default '',
			  `url` varchar(400) NOT NULL default '',
			  `layers` varchar(300) NOT NULL default '',
			  `projection` varchar(100) NOT NULL default '',
			  `img_format` varchar(100) NOT NULL default 'image/png',
			  `maxExtent` varchar(100) NOT NULL default '',
			  `minResolution` varchar(100) NOT NULL default 'auto',
			  `maxResolution` varchar(100) NOT NULL default 'auto',
			  `unit` varchar(100) NOT NULL default '',
			  `default_visibility` tinyint(1) NOT NULL default '0',
			  `order` int(11) NOT NULL default '1',
			  `user` varchar(400) ,
			  `password` varchar(400) ,
			  `easysdi_account_id` bigint(20),
			  PRIMARY KEY  (`id`)
			)"; 
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="
			CREATE TABLE `#__easysdi_map_localisation_layer` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `wfs_url` varchar(4000) NOT NULL DEFAULT '',
			  `layer_name` varchar(4000) NOT NULL DEFAULT '',
			  `title` varchar(4000) NOT NULL DEFAULT '',
			  `area_field_name` varchar(100) NOT NULL DEFAULT '',
			  `name_field_name` varchar(100) NOT NULL DEFAULT '',
			  `id_field_name` varchar(100) NOT NULL DEFAULT '',
			  `feature_type_name` varchar(400) NOT NULL DEFAULT '',
			  `parent_fk_field_name` varchar(100) NOT NULL DEFAULT '',
			  `parent_id` bigint(20) NOT NULL DEFAULT '0',
			  `maxfeatures` int(11) NOT NULL DEFAULT '-1',
			  `img_format` varchar(100) NOT NULL DEFAULT 'image/png',
			  `min_resolution` bigint(20) NOT NULL DEFAULT '0',
			  `max_resolution` bigint(20) NOT NULL DEFAULT '0',
			  `extract_id_from_fid` tinyint (1) NOT NULL DEFAULT '1',
			  `user` varchar(400) ,
			  `password` varchar(400) ,
			  `easysdi_account_id` bigint(20),
			  PRIMARY KEY (`id`)
			)"; 
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
		 * Add visibility and order to overlays
			**/
		$query ="ALTER TABLE `#__easysdi_overlay_content`
					ADD COLUMN  `default_visibility` tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_overlay_content`
					ADD COLUMN `order` int(11) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="CREATE TABLE IF NOT EXISTS `#__easysdi_map_annotation_style` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `name` varchar(100) NOT NULL,
				  `fillColor` varchar(7) DEFAULT NULL,
				  `fillOpacity` float DEFAULT NULL,
				  `strokeColor` varchar(7) DEFAULT NULL,
				  `strokeOpacity` float DEFAULT NULL,
				  `strokeWidth` float DEFAULT NULL,
				  `strokeLinecap` varchar(7) DEFAULT NULL,
				  `strokeDashstyle` varchar(11) DEFAULT NULL,
				  `pointRadius` decimal(10,0) DEFAULT NULL,
				  `externalGraphic` varchar(400) DEFAULT NULL,
				  `graphicWidth` float DEFAULT NULL,
				  `graphicHeight` float DEFAULT NULL,
				  `graphicOpacity` float DEFAULT NULL,
				  `graphicXOffset` float DEFAULT NULL,
				  `graphicYOffset` float DEFAULT NULL,
				  PRIMARY KEY (`id`)
				)";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/**
			* Update version
			* */
		$version = "0.11";
		$query="UPDATE #__easysdi_version SET version ='0.11' where component = 'com_easysdi_map'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "0.11")
	{
		/**
		 * Add default opacity to overlay and map
			**/
		$query ="ALTER TABLE `#__easysdi_overlay_content`
					ADD COLUMN  `default_opacity` float NOT NULL DEFAULT '1'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_overlay_content`
					ADD COLUMN  `metadata_url` VARCHAR (500)";			
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_base_layer`
					ADD COLUMN  `default_opacity` float NOT NULL DEFAULT '1'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query ="ALTER TABLE `#__easysdi_map_base_layer`
					ADD COLUMN  `metadata_url` VARCHAR (500)";			
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
			* Annotation toolbar display option
			* */
		$query ="INSERT INTO `#__easysdi_map_display_options` ( `object`, `translation`, `enable`) VALUES
				( 'Annotation', 'EASYSDI_MAP_ANNOTATION', 1)
				";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="INSERT INTO `#__easysdi_map_config` (`name`, `value`, `description`) VALUES
					('pubWmsUrl', 'http://localhost/geoserver/wms', 'url of publication database Wms service proxy'),
					('defaultCoordMapZoom', '2', ''),
					('autocompleteNumChars', '4', ''),
					('autocompleteUseFID', '1', ''),
					('autocompleteMaxFeat', '50', '')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		/**
			* Update version
			* */
		$version = "0.12";
		$query="UPDATE #__easysdi_version SET version ='0.12' where component = 'com_easysdi_map'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "0.12")
	{
		$query="INSERT INTO `#__easysdi_map_config` (`name`, `value`, `description`) VALUES
					('layerProxyXMLFile', '', '')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query="INSERT INTO `#__easysdi_map_config` (`name`, `value`, `description`) VALUES
					('maptofopURL', '', 'Simple reporting service based on FOP.  HTTP-GET')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_map_base_layer`
					ADD COLUMN  `singletile` tinyint(1) NOT NULL default '0'";			
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="ALTER TABLE `#__easysdi_overlay_group`
					ADD COLUMN `order` int(11) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		$query ="INSERT INTO `#__easysdi_map_display_options` ( `object`, `translation`, `enable`) VALUES
				( 'Coordinate', 'EASYSDI_MAP_COORDINATE', 1)
				";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
		/**
			* Update version
			* */
		$version = "0.13";
		$query="UPDATE #__easysdi_version SET version ='0.13' where component = 'com_easysdi_map'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
			
	}

	if($version == "0.13")
	{
		$query="ALTER TABLE `#__easysdi_map_base_layer` ADD CONSTRAINT `fk_base_def` FOREIGN KEY (`id_base`) REFERENCES `#__easysdi_map_base_definition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		/**
			* Update version
			* */
		$version = "0.14";
		$query="UPDATE #__easysdi_version SET version ='0.14' where component = 'com_easysdi_map'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}


	/**
	 * Menu managment
	 */
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	if (!$id)
	{
		$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
		return false;
	}

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map' ";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
				values($id,'Map','','option=com_easysdi_map','Map','com_easysdi_map','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Map','option=com_easysdi_map','Easysdi Map','com_easysdi_map','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$mainframe->enqueueMessage("Congratulations Map component for EasySdi is now installed and ready to be used. Enjoy EasySdi!","INFO");
	return true;
}
?>