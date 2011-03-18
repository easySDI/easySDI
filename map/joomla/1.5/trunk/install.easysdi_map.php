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
		return false;
	}

	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');
	
	$version = '0.0';
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'MAP'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if ($db->getErrorNum()) 
	{
		//The table doesn't exist, that means nothing is installed.
		$mainframe->enqueueMessage("EASYSDI IS NOT INSTALLED","ERROR");		
		exit;		
	}	
	if (!$version)
	{
	$query =
		"
		SET FOREIGN_KEY_CHECKS=0;
		-- ----------------------------
		-- Table structure for `#__sdi_annotationstyle`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_annotationstyle`;
		CREATE TABLE `#__sdi_annotationstyle` (
		  	`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
			`guid`  varchar(36) NOT NULL ,
			`code`  varchar(20) NULL DEFAULT NULL ,
			`name`  varchar(50) NOT NULL ,
			`description`  varchar(100)NULL DEFAULT NULL ,
			`created`  datetime NOT NULL ,
			`updated`  datetime NULL DEFAULT NULL ,
			`createdby`  bigint(20) NOT NULL ,
			`updatedby`  bigint(20) NULL DEFAULT NULL ,
			`label`  varchar(50) NULL DEFAULT NULL ,
			`ordering`  bigint(20) NULL DEFAULT 0 ,
			`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
			`checked_out_time`  datetime NULL DEFAULT NULL ,
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
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_featuretypeattribute`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_featuretypeattribute`;
		CREATE TABLE `#__sdi_featuretypeattribute` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `id_ft` bigint(20) NOT NULL,
		  `data_type` varchar(100) NOT NULL DEFAULT '',
		  `width` bigint(20) DEFAULT NULL,
		  `initial_visibility` tinyint(1) NOT NULL DEFAULT '0',
		  `visible` tinyint(1) NOT NULL DEFAULT '0',
		  `misc_search` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `id_ft` (`id_ft`),
		  CONSTRAINT `#__sdi_featuretypeattribute_ibfk_1` FOREIGN KEY (`id_ft`) REFERENCES `#__sdi_featuretype` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_ftatt_profile`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_ftatt_profile`;
		CREATE TABLE `#__sdi_ftatt_profile` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `ftatt_id` bigint(20) NOT NULL,
		  `profile_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `ftatt_id` (`ftatt_id`),
		  KEY `profile_id` (`profile_id`),
		  CONSTRAINT `#__sdi_ftatt_profile_ibfk_1` FOREIGN KEY (`ftatt_id`) REFERENCES `#__sdi_featuretypeattribute` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_ftatt_profile_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `#__sdi_accountprofile` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_baselayer`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_baselayer`;
		CREATE TABLE `#__sdi_baselayer` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  
		  `url` varchar(400) NOT NULL DEFAULT '',
		  `layers` varchar(300) NOT NULL DEFAULT '',
		  `img_format` varchar(100) NOT NULL DEFAULT 'image/png',
		  `customStyle` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `singletile` tinyint(1) NOT NULL DEFAULT '0',
		  `cache` tinyint(1) NOT NULL DEFAULT '0',
		  `user` varchar(400) DEFAULT NULL,
		  `password` varchar(400) DEFAULT NULL,
		  `easysdi_account_id` bigint(20) DEFAULT NULL,
		  `default_visibility` tinyint(1) NOT NULL DEFAULT '0',
		  `default_opacity` float NOT NULL DEFAULT '1',
		  `metadata_url` varchar(500) DEFAULT NULL,
		  
		  `projection` varchar(100) NOT NULL DEFAULT 'EPSG:4326',
		  `unit` varchar(100) NOT NULL DEFAULT '',
		  `minScale` varchar(100) NOT NULL DEFAULT 'auto',
		  `maxScale` varchar(100) NOT NULL DEFAULT 'auto',
		  `resolutions` text,
		  `resolutionOverScale` tinyint(4) NOT NULL DEFAULT '0',
		  `extent` varchar(100) DEFAULT NULL,
		  `maxExtent` varchar(100) NOT NULL DEFAULT '-180,-90,180,90',
		  
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_commentfeaturetype`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_commentfeaturetype`;
		CREATE TABLE `#__sdi_commentfeaturetype` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `featuretypename` varchar(250) NOT NULL,
		  `countattribute` varchar(250) NOT NULL,
		  `enable` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapconfig`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapconfig`;
		CREATE TABLE `#__sdi_mapconfig` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `key` varchar(100) NOT NULL DEFAULT '',
		  `value` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=24;
		
		-- ----------------------------
		-- Records of #__sdi_mapconfig
		-- ----------------------------
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'componentPath','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'componentPath',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'componentUrl','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'componentUrl',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'projection','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'projection','EPSG:27572');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'pubWfsUrl','url of publication database Wfs service proxy','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'pubWfsUrl',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'maxFeatures','maximum number of features for WFS requests','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'maxFeatures','1000');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'pubFeatureNS','namespace of publication database Wfs service','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'pubFeatureNS',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'pubFeaturePrefix','prefix of publication database Wfs feature type','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'pubFeaturePrefix','ms');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'wpsReportsUrl','path to WPS service for report hook-up','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'wpsReportsUrl',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'shp2GmlUrl','path to Shp to Gml service - needs to be a local proxy as via Ajax','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'shp2GmlUrl',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'featureIdAttribute','name of attribute used to identify features (=the Primary Key)','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'featureIdAttribute','unit_guid');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'maxSearchBars','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'maxSearchBars','3');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'WMSFilterSupport','Does the server support filters in WMS requests.','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'WMSFilterSupport','false');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'pubWmsUrl','url of publication database Wms service proxy','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'pubWmsUrl',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'defaultCoordMapZoom','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'defaultCoordMapZoom','0');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'autocompleteNumChars','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'autocompleteNumChars','4');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'autocompleteUseFID','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'autocompleteUseFID','1');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'autocompleteMaxFeat','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'autocompleteMaxFeat','50');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'layerProxyXMLFile','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'layerProxyXMLFile','');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'maptofopURL','Simple reporting service based on FOP.  HTTP-GET','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'maptofopURL',null);
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'numZoomLevels','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'numZoomLevels','10');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'localisationInputWidth','Width of the geolocation combobox.','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'localisationInputWidth','300');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'legendOrFilterPanelWidth','Width of the legend panel.','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'legendOrFilterPanelWidth','250');
		INSERT INTO `#__sdi_mapconfig` (guid,name,description, created,createdby,checked_out,key, value) VALUES ('".helper_easysdi::getUniqueId()."', 'treePanelWidth','Width of the layers panel.','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'treePanelWidth','250');
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapcontext`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapcontext`;
		CREATE TABLE `#__sdi_mapcontext` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `WMCtext` text,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapdisplayoption`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapdisplayoption`;
		CREATE TABLE `#__sdi_mapdisplayoption` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `translation` varchar(200) DEFAULT NULL, 
		  `object` varchar(100) DEFAULT NULL,
		  `enable` tinyint(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Records of __sdi_mapdisplayoption
		-- ----------------------------
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'SimpleSearch','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_SIMPLESEARCH','SimpleSearch','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'AdvancedSearch','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_ADVANCEDSEARCH','AdvancedSearch','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'DataPrecision','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_DATAPRECISION','DataPrecision','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Localisation','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_LOCALISATION','Localisation','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'ToolBar','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_TOOLBAR','ToolBar','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'MapOverview','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_MAPOVERVIEW','MapOverview','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Annotation','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_ANNOTATION','Annotation','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,translation, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Coordinate','','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'EASYSDI_MAP_COORDINATE','Coordinate','1');
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapextension`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapextension`;
		CREATE TABLE `#__sdi_mapextension` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `extended_object` varchar(100) DEFAULT NULL,
		  `extension_object` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapextensionresource`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapextensionresource`;
		CREATE TABLE `#__sdi_mapextensionresource` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `ext_id` bigint(20) DEFAULT NULL,
		  `resource_type` varchar(100) DEFAULT NULL,
		  `resource_folder` varchar(500) DEFAULT NULL,
		  `resource_file` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `ext_id` (`ext_id`),
		  CONSTRAINT `#__sdi_mapextensionresource_ibfk_1` FOREIGN KEY (`ext_id`) REFERENCES `#__sdi_mapextension` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_resultgrid`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_resultgrid`;
		CREATE TABLE `#__sdi_resultgrid` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `featuretype` bigint(20) DEFAULT NULL,
		  `distinctfk` varchar(100) NOT NULL DEFAULT '',
		  `distinctpk` varchar(100) NOT NULL DEFAULT '',
		  `rowdetailsfeaturetype` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_featuretype`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_featuretype`;
		CREATE TABLE `#__sdi_featuretype` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `geometry` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_featuretype_usage`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_featuretype_usage`;
		CREATE TABLE `#__sdi_featuretype_usage` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `ft_id` bigint(20) NOT NULL,
		  `usage_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `ft_id` (`ft_id`),
		  KEY `usage_id` (`usage_id`),
		  CONSTRAINT `#__sdi_featuretype_usage_ibfk_1` FOREIGN KEY (`ft_id`) REFERENCES `#__sdi_featuretype` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_featuretype_usage_ibfk_2` FOREIGN KEY (`usage_id`) REFERENCES `#__sdi_usage` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapfilter`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_mapfilter`;
		CREATE TABLE `#__sdi_mapfilter` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `user_id` int(11) NOT NULL,
		  `title` varchar(100) NOT NULL,
		  `filter_data` text NOT NULL,
		  `filter_mode` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_geolocation`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_geolocation`;
		CREATE TABLE `#__sdi_geolocation` (
		   `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `wfsurl` varchar(4000) NOT NULL DEFAULT '',
		  `wmsurl` varchar(4000) NOT NULL DEFAULT '',
		  `layername` varchar(4000) NOT NULL DEFAULT '',
		  `areafield` varchar(100) NOT NULL DEFAULT '',
		  `namefield` varchar(100) NOT NULL DEFAULT '',
		  `idfield` varchar(100) NOT NULL DEFAULT '',
		  `featuretypename` varchar(400) NOT NULL DEFAULT '',
		  `parentfkfield` varchar(100) NOT NULL DEFAULT '',
		  `parentid` bigint(20) NOT NULL DEFAULT '0',
		  `maxfeatures` int(11) NOT NULL DEFAULT '-1',
		  `imgformat` varchar(100) NOT NULL DEFAULT 'image/png',
		  `minresolution` bigint(20) NOT NULL DEFAULT '0',
		  `maxresolution` bigint(20) NOT NULL DEFAULT '0',
		  `extractidfromfid` tinyint(1) NOT NULL DEFAULT '1',
		  `user` varchar(400) DEFAULT NULL,
		  `password` varchar(400) DEFAULT NULL,
		  `easysdi_account_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_precision`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_precision`;
		CREATE TABLE `#__sdi_precision` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `minresolution` bigint(20) DEFAULT NULL,
		  `maxresolution` bigint(20) DEFAULT NULL,
		  `lowscaleswitchto` varchar(100) DEFAULT NULL,
		  `style` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_projection`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_projection`;
		CREATE TABLE `#__sdi_projection` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `proj4text` varchar(500) NOT NULL DEFAULT '',
		  `numDigits` int(2) NOT NULL DEFAULT '0',
		  `enable` tinyint(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_searchlayer`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_searchlayer`;
		CREATE TABLE `#__sdi_searchlayer` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `featuretype` bigint(20) DEFAULT NULL,
		  `geometryname` varchar(100) NOT NULL DEFAULT '',
		  `rowdetailsfeaturetype` bigint(20) DEFAULT NULL,
		  `styles` varchar(500) DEFAULT NULL,
		  `enable` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_map_serviceaccount`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_map_serviceaccount`;
		CREATE TABLE `#__sdi_map_serviceaccount` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `account_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_simplesearchfilter`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_simplesearchfilter`;
		CREATE TABLE `#__sdi_simplesearchfilter` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `attribute` varchar(100) NOT NULL DEFAULT '',
		  `value` varchar(100) NOT NULL DEFAULT '',
		  `operator` varchar(5) NOT NULL DEFAULT '==',
		  `title` varchar(200) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_simplesearchtype`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_simplesearchtype`;
		CREATE TABLE `#__sdi_simplesearchtype` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `title` varchar(500) NOT NULL DEFAULT '',
		  `dropdown_feature_type` varchar(100) NOT NULL DEFAULT '',
		  `dropdown_display_attr` varchar(100) NOT NULL DEFAULT '',
		  `dropdown_id_attr` varchar(100) NOT NULL DEFAULT '',
		  `search_attribute` varchar(500) NOT NULL DEFAULT '',
		  `operator` varchar(5) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_sst_erg`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_sst_erg`;
		CREATE TABLE `#__sdi_sst_erg` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `id_sst` bigint(20) NOT NULL,
		  `id_erg` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `id_sst` (`id_sst`),
		  KEY `id_erg` (`id_erg`),
		  CONSTRAINT `#__sdi_sst_erg_ibfk_1` FOREIGN KEY (`id_sst`) REFERENCES `#__sdi_simplesearchtype` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_sst_erg_ibfk_2` FOREIGN KEY (`id_erg`) REFERENCES `#__sdi_resultgrid` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_sst_saf`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_sst_saf`;
		CREATE TABLE `#__sdi_sst_saf` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `id_sst` bigint(20) NOT NULL,
		  `id_saf` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `id_sst` (`id_sst`),
		  KEY `id_saf` (`id_saf`),
		  CONSTRAINT `#__sdi_sst_saf_ibfk_1` FOREIGN KEY (`id_sst`) REFERENCES `#__sdi_simplesearchtype` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_sst_saf_ibfk_2` FOREIGN KEY (`id_saf`) REFERENCES `#__sdi_resultgrid` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_usage`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_usage`;
		CREATE TABLE `#__sdi_usage` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `name` varchar(100) NOT NULL DEFAULT '',
		  `translation` varchar(100) DEFAULT NULL,
		  `description` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `name` (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_overlaygroup`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_overlaygroup`;
		CREATE TABLE `#__sdi_overlaygroup` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  `open` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_overlay`
		-- ----------------------------
		DROP TABLE IF EXISTS `#__sdi_overlay`;
		CREATE TABLE `#__sdi_overlay` (
		  `id`  bigint(20) NOT NULL AUTO_INCREMENT ,
		  `guid`  varchar(36) NOT NULL ,
		  `code`  varchar(20) NULL DEFAULT NULL ,
		  `name`  varchar(50) NOT NULL ,
		  `description`  varchar(100)NULL DEFAULT NULL ,
		  `created`  datetime NOT NULL ,
		  `updated`  datetime NULL DEFAULT NULL ,
		  `createdby`  bigint(20) NOT NULL ,
		  `updatedby`  bigint(20) NULL DEFAULT NULL ,
		  `label`  varchar(50) NULL DEFAULT NULL ,
		  `ordering`  bigint(20) NULL DEFAULT 0 ,
		  `checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
		  `checked_out_time`  datetime NULL DEFAULT NULL ,
		  
		  `group_id` bigint(20) NOT NULL DEFAULT '0',
		  `url` varchar(400) NOT NULL DEFAULT '',
		  `type` varchar(100) NOT NULL DEFAULT '',
		  `layers` varchar(300) NOT NULL DEFAULT '',
		  `maxextent` varchar(100) NOT NULL DEFAULT '',
		  `maxscale` varchar(100) NOT NULL DEFAULT 'auto',
		  `minscale` varchar(100) NOT NULL DEFAULT 'auto',
		  `resolutions` text,
		  `resolutionoverscale` tinyint(1) NOT NULL DEFAULT '0',
		  `projection` varchar(100) NOT NULL DEFAULT '',
		  `imgformat` varchar(100) NOT NULL DEFAULT 'image/png',
		  `customstyle` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `cache` tinyint(1) NOT NULL DEFAULT '0',
		  `unit` varchar(100) NOT NULL DEFAULT '',
		  `singletile` tinyint(1) NOT NULL DEFAULT '0',
		  `user` varchar(400) DEFAULT NULL,
		  `password` varchar(400) DEFAULT NULL,
		  `defaultvisibility` tinyint(1) NOT NULL DEFAULT '0',
		  `order` int(11) NOT NULL DEFAULT '0',
		  `defaultopacity` float NOT NULL DEFAULT '1',
		  `metadataurl` varchar(500) DEFAULT NULL,
		  
		  `minresolution` varchar(100) NOT NULL DEFAULT 'auto',
		  `maxresolution` varchar(100) NOT NULL DEFAULT 'auto',
		  
		  PRIMARY KEY (`id`),
		  CONSTRAINT `#__sdi_overlay_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_overlaygroup` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
		";

		$db->setQuery( $query);
		if (!$db->queryBatch())		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$version= '2.0.0';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
				VALUES ('".helper_easysdi::getUniqueId()."', 'MAP', 'com_easysdi_map', 'com_easysdi_map', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_map', 'com_sdi_map', '".$version."')";
		$db->setQuery( $query);
		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}	

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map'";
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
		values('EasySDI - Map','option=com_easysdi_map','Map','com_easysdi_map','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$mainframe->enqueueMessage("Congratulations Map component for EasySdi is now installed and ready to be used. Enjoy EasySdi!","INFO");

	return true;
}
?>