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
		$version= '2.0.0';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
				VALUES ('".helper_easysdi::getUniqueId()."', 'MAP', 'com_easysdi_map', 'com_easysdi_map', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_map', 'com_sdi_map', '".$version."')";
		$db->setQuery( $query);
		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$module_id = $db->insertid();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'MAP_PANEL', 'Map Panel', 'Map Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$module_id."', 'com_easysdi_map/core/view/sub.ctrlpanel.admin.easysdi.html.php', '7')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		$query =
		"
		SET FOREIGN_KEY_CHECKS=0;
		-- ----------------------------
		-- Table structure for `#__sdi_annotationstyle`
		-- ----------------------------
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
			`fillcolor` varchar(7) DEFAULT NULL,
			`fillopacity` float DEFAULT NULL,
			`strokecolor` varchar(7) DEFAULT NULL,
			`strokeopacity` float DEFAULT NULL,
			`strokewidth` float DEFAULT NULL,
			`strokelinecap` varchar(7) DEFAULT NULL,
			`strokedashstyle` varchar(11) DEFAULT NULL,
			`pointradius` decimal(10,0) DEFAULT NULL,
			`externalgraphic` varchar(400) DEFAULT NULL,
			`graphicwidth` float DEFAULT NULL,
			`graphicheight` float DEFAULT NULL,
			`graphicopacity` float DEFAULT NULL,
			`graphicxoffset` float DEFAULT NULL,
			`graphicyoffset` float DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			
		-- ----------------------------
		-- Table structure for `#__sdi_featuretypeattribute`
		-- ----------------------------
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
		  `ft_id` bigint(20) NOT NULL,
		  `datatype` varchar(100) NOT NULL DEFAULT '',
		  `width` bigint(20) DEFAULT NULL,
		  `initialvisibility` tinyint(1) NOT NULL DEFAULT '0',
		  `visible` tinyint(1) NOT NULL DEFAULT '0',
		  `miscsearch` tinyint(1) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `ft_id` (`ft_id`),
		  CONSTRAINT `#__sdi_featuretypeattribute_ibfk_1` FOREIGN KEY (`ft_id`) REFERENCES `#__sdi_featuretype` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_ftatt_profile`
		-- ----------------------------
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
		  `version` varchar(10) NOT NULL DEFAULT '',
		  `layers` varchar(300) NOT NULL DEFAULT '',
		  `imgformat` varchar(100) NOT NULL DEFAULT 'image/png',
		  `customStyle` tinyint(1) unsigned NOT NULL DEFAULT '0',
		  `singletile` tinyint(1) NOT NULL DEFAULT '0',
		  `cache` tinyint(1) NOT NULL DEFAULT '0',
		  `user` varchar(400) DEFAULT NULL,
		  `password` varchar(400) DEFAULT NULL,
		  `account_id` bigint(20) DEFAULT NULL,
		  `defaultvisibility` tinyint(1) NOT NULL DEFAULT '0',
		  `defaultopacity` float NOT NULL DEFAULT '1',
		  `metadataurl` varchar(500) DEFAULT NULL,
		  
		  `projection` varchar(100) NOT NULL DEFAULT 'EPSG:4326',
		  `unit` varchar(100) NOT NULL DEFAULT '',
		  `minscale` varchar(100) NOT NULL DEFAULT 'auto',
		  `maxscale` varchar(100) NOT NULL DEFAULT 'auto',
		  `resolutions` text,
		  `resolutionoverscale` tinyint(4) NOT NULL DEFAULT '0',
		  `extent` varchar(100) DEFAULT NULL,
		  `maxextent` varchar(100) NOT NULL DEFAULT '-180,-90,180,90',
		  
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_commentfeaturetype`
		-- ----------------------------
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
		-- Records of #__sdi_configuration
		-- ----------------------------
						
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'componentPath', 'componentPath','','".date('Y-m-d H:i:s')."', '".$user_id."', null, '".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'componentUrl','componentUrl','','".date('Y-m-d H:i:s')."', '".$user_id."',  null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'projection','projection','','".date('Y-m-d H:i:s')."', '".$user_id."','EPSG:27582','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubWfsUrl','pubWfsUrl','url of publication database Wfs service proxy','".date('Y-m-d H:i:s')."', '".$user_id."',null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'maxFeatures', 'maxFeatures','maximum number of features for WFS requests','".date('Y-m-d H:i:s')."', '".$user_id."','1000','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubFeatureNS','pubFeatureNS','namespace of publication database Wfs service','".date('Y-m-d H:i:s')."', '".$user_id."', null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubFeaturePrefix','pubFeaturePrefix','prefix of publication database Wfs feature type','".date('Y-m-d H:i:s')."', '".$user_id."','ms','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'wpsReportsUrl','wpsReportsUrl','path to WPS service for report hook-up','".date('Y-m-d H:i:s')."', '".$user_id."',null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'shp2GmlUrl','shp2GmlUrl','path to Shp to Gml service - needs to be a local proxy as via Ajax','".date('Y-m-d H:i:s')."', '".$user_id."', null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'featureIdAttribute','featureIdAttribute','name of attribute used to identify features (=the Primary Key)','".date('Y-m-d H:i:s')."', '".$user_id."','unit_guid','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'maxSearchBars','maxSearchBars','','".date('Y-m-d H:i:s')."', '".$user_id."', '3','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'WMSFilterSupport', 'WMSFilterSupport','Does the server support filters in WMS requests.','".date('Y-m-d H:i:s')."', '".$user_id."','false','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubWmsUrl','pubWmsUrl','url of publication database Wms service proxy','".date('Y-m-d H:i:s')."', '".$user_id."',null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'defaultCoordMapZoom','defaultCoordMapZoom','','".date('Y-m-d H:i:s')."', '".$user_id."','0','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'autocompleteNumChars','autocompleteNumChars','','".date('Y-m-d H:i:s')."', '".$user_id."','4','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'autocompleteUseFID','autocompleteUseFID','','".date('Y-m-d H:i:s')."', '".$user_id."', '0','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'autocompleteMaxFeat','autocompleteMaxFeat','','".date('Y-m-d H:i:s')."', '".$user_id."', '50','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'layerProxyXMLFile','layerProxyXMLFile','','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'maptofopURL', 'maptofopURL','Simple reporting service based on FOP.  HTTP-GET','".date('Y-m-d H:i:s')."', '".$user_id."', null,'".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'numZoomLevels', 'numZoomLevels','','".date('Y-m-d H:i:s')."', '".$user_id."','10','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'localisationInputWidth', 'localisationInputWidth','Width of the geolocation combobox.','".date('Y-m-d H:i:s')."', '".$user_id."', '300','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'legendOrFilterPanelWidth','legendOrFilterPanelWidth','Width of the legend panel.','".date('Y-m-d H:i:s')."', '".$user_id."', '250','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'treePanelWidth','treePanelWidth','Width of the layers panel.','".date('Y-m-d H:i:s')."', '".$user_id."', '250','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubWfsVersion','pubWfsVersion','Version of publication WFS service.','".date('Y-m-d H:i:s')."', '".$user_id."', '1.0.0','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'pubWmsVersion','pubWmsVersion','Version of publication WMS service.','".date('Y-m-d H:i:s')."', '".$user_id."', '1.1.1','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'enableQueryEngine','enableQueryEngine','Enable the query engine.','".date('Y-m-d H:i:s')."', '".$user_id."', '0','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapUnit','mapUnit','Map unit.','".date('Y-m-d H:i:s')."', '".$user_id."', 'm','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapResolutionOverScale','mapResolutionOverScale','','".date('Y-m-d H:i:s')."', '".$user_id."', '0','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapMaxScale','mapMaxScale','Map maximum scale.','".date('Y-m-d H:i:s')."', '".$user_id."', '1','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapMinScale','mapMinScale','Map minimum scale.','".date('Y-m-d H:i:s')."', '".$user_id."', '5000000','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapResolutions','mapResolutions','Map resolutions list.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."');
		INSERT INTO `#__sdi_configuration` (guid,code,name,description, created,createdby,value,module_id) VALUES  ('".helper_easysdi::getUniqueId()."', 'mapMaxExtent','mapMaxExtent','Map maximum extent.','".date('Y-m-d H:i:s')."', '".$user_id."', '','".$module_id."');
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapcontext`
		-- ----------------------------
		CREATE TABLE `#__sdi_mapcontext` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `user_id` int(11) NOT NULL,
		  `WMCtext` text,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapdisplayoption`
		-- ----------------------------
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
		  `object` varchar(100) DEFAULT NULL,
		  `enable` tinyint(1) NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Records of __sdi_mapdisplayoption
		-- ----------------------------
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'SimpleSearch','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_SIMPLESEARCH','SimpleSearch','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'AdvancedSearch','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_ADVANCEDSEARCH','AdvancedSearch','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'DataPrecision','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_DATAPRECISION','DataPrecision','0');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Localisation','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_LOCALISATION','Localisation','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'ToolBar','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_TOOLBAR','ToolBar','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'MapOverview','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_MAPOVERVIEW','MapOverview','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Annotation','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_ANNOTATION','Annotation','1');
		INSERT INTO `#__sdi_mapdisplayoption` (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'Coordinate','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_COORDINATE','Coordinate','1');
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapextension`
		-- ----------------------------
		CREATE TABLE `#__sdi_mapextension` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `extended_object` varchar(100) DEFAULT NULL,
		  `extension_object` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_mapextensionresource`
		-- ----------------------------
		CREATE TABLE `#__sdi_mapextensionresource` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `ext_id` bigint(20) DEFAULT NULL,
		  `resourcetype` varchar(100) DEFAULT NULL,
		  `resourcefolder` varchar(500) DEFAULT NULL,
		  `resourcefile` varchar(500) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `ext_id` (`ext_id`),
		  CONSTRAINT `#__sdi_mapextensionresource_ibfk_1` FOREIGN KEY (`ext_id`) REFERENCES `#__sdi_mapextension` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_resultgrid`
		-- ----------------------------
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
		  `featuretypename`  varchar(100) NOT NULL ,
		  `geometry` varchar(100) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_featuretype_usage`
		-- ----------------------------
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
		  `filterdata` text NOT NULL,
		  `filtermode` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_geolocation`
		-- ----------------------------
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
		  `account_id` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_precision`
		-- ----------------------------
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
		-- Table structure for `#__sdi_simplesearchfilter`
		-- ----------------------------
		CREATE TABLE `#__sdi_simplesearchfilter` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
		  `attribute` varchar(100) NOT NULL DEFAULT '',
		  `value` varchar(100) NOT NULL DEFAULT '',
		  `operator` varchar(5) NOT NULL DEFAULT '==',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_simplesearchtype`
		-- ----------------------------
		CREATE TABLE `#__sdi_simplesearchtype` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
		  `dropdownfeaturetype` varchar(100) NOT NULL DEFAULT '',
		  `dropdowndisplayattr` varchar(100) NOT NULL DEFAULT '',
		  `dropdownidattr` varchar(100) NOT NULL DEFAULT '',
		  `searchattribute` varchar(500) NOT NULL DEFAULT '',
		  `operator` varchar(5) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_sst_erg`
		-- ----------------------------
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
		CREATE TABLE `#__sdi_sst_saf` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `id_sst` bigint(20) NOT NULL,
		  `id_saf` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `id_sst` (`id_sst`),
		  KEY `id_saf` (`id_saf`),
		  CONSTRAINT `#__sdi_sst_saf_ibfk_1` FOREIGN KEY (`id_sst`) REFERENCES `#__sdi_simplesearchtype` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_sst_saf_ibfk_2` FOREIGN KEY (`id_saf`) REFERENCES `#__sdi_simplesearchfilter` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		-- ----------------------------
		-- Table structure for `#__sdi_usage`
		-- ----------------------------
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
		  `version` varchar(10) NOT NULL DEFAULT '',
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
		  `account_id` bigint(20) DEFAULT NULL,
		  `defaultvisibility` tinyint(1) NOT NULL DEFAULT '0',
		  `defaultopacity` float NOT NULL DEFAULT '1',
		  `metadataurl` varchar(500) DEFAULT NULL,
		  
		  `minresolution` varchar(100) NOT NULL DEFAULT 'auto',
		  `maxresolution` varchar(100) NOT NULL DEFAULT 'auto',
		  
		  PRIMARY KEY (`id`),
		  CONSTRAINT `#__sdi_overlay_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `#__sdi_overlaygroup` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
		-- ----------------------------
		-- Table structure for `#__sdi_profile_role`
		-- ----------------------------
		CREATE TABLE `#__sdi_profile_role` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `profile_id` bigint(20) NOT NULL,
		  `role_id` bigint(20) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `profile_id` (`profile_id`),
		  KEY `role_id` (`role_id`),
		  CONSTRAINT `#__sdi_profile_role_ibfk_1` FOREIGN KEY (`profile_id`) REFERENCES `#__sdi_accountprofile` (`id`) ON DELETE CASCADE,
		  CONSTRAINT `#__sdi_profile_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `#__sdi_list_role` (`id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'DATA_PRECISION', 'EASYSDI_DATA_PRECISION_RIGHT','Access to the data precision tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_TAXA', 'EASYSDI_ADV_SEARCH_TAXA_RIGHT','Access to the advanced search taxon tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_BIOTOPE', 'EASYSDI_ADV_SEARCH_BIOTOPE_RIGHT','Access to the advanced search taxon status tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_TAX_STS', 'EASYSDI_ADV_SEARCH_TAXON_STATUS_RIGHT','Access to the advanced search taxon status tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_BIO_STS', 'EASYSDI_ADV_SEARCH_BIOTOPE_STATUS_RIGHT','Access to the advanced search biotope status tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_SURVEY', 'EASYSDI_ADV_SEARCH_SURVEY_RIGHT','Access to the advanced search surveys tabb','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_CRITERIA', 'EASYSDI_ADV_SEARCH_CRITERIA_RIGHT','Access to the advanced search criteria tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_MISC', 'EASYSDI_ADV_SEARCH_MISC_RIGHT','Access to the advanced search misc tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'ADV_SEARCH_PLACE', 'EASYSDI_ADV_SEARCH_PLACE_RIGHT','Access to the advanced search place filter tab','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
		INSERT INTO `#__sdi_list_role` (guid,code,name,description, created,createdby,publish_id,roletype_id) 
		VALUES  ('".helper_easysdi::getUniqueId()."', 'SEARCH_SAVE_LOAD', 'EASYSDI_SEARCH_SAVE_LOAD_RIGHT','Access to the save and load searches functionnality','".date('Y-m-d H:i:s')."', '".$user_id."', 0, 1);
		
					";
		
		$db->setQuery( $query);
		if (!$db->queryBatch())		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}	
	if ($version == "2.0.0")
	{
		// Update component version
		$version="2.0.1";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='MAP'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if ($version == "2.0.1")
	{
		// Update component version
	
		$query="ALTER TABLE #__sdi_baselayer MODIFY name varchar(100)"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__sdi_overlay MODIFY name varchar(100)"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__sdi_overlaygroup MODIFY name varchar(100)"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__sdi_baselayer ADD published tinyint(1) NOT NULL DEFAULT 1"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__sdi_overlay ADD published tinyint(1) NOT NULL DEFAULT 1"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__sdi_overlaygroup ADD published tinyint(1) NOT NULL DEFAULT 1"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__sdi_mapdisplayoption  MODIFY name varchar(100)"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__sdi_mapdisplayoption  MODIFY code varchar(100)"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="delete  from #__sdi_mapdisplayoption where code in ('MAP_LOCALISATION', 'MAP_TOOLBAR', 'MAP_ANNOTATION')";
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query ="INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'locAutocomplete','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_locAutocomplete','locAutocomplete','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'previousButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_previousButton','previousButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'nextButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_nextButton','nextButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'navButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_navButton','navButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'selectButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_selectButton','selectButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'zoomInBoxButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_zoomInBoxButton','zoomInBoxButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'zoomOutBoxButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_zoomOutBoxButton','zoomOutBoxButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'zoomToScaleField','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_zoomToScaleField','zoomToScaleField','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'zoomToMaxExtentButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_zoomToMaxExtentButton','zoomToMaxExtentButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'printMapButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_printMapButton','printMapButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'saveMapButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_saveMapButton','saveMapButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'pdfButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_pdfButton','pdfButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'getFeatureButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_getFeatureButton','getFeatureButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'rectangleButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_rectangleButton','rectangleButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'polygonButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_polygonButton','polygonButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'pointButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_pointButton','pointButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'modifyFeatureButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_modifyFeatureButton','modifyFeatureButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'pathButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_pathButton','pathButton','1');
		
		INSERT INTO #__sdi_mapdisplayoption (guid,name,created,createdby,checked_out,code, object,enable) VALUES ('".helper_easysdi::getUniqueId()."', 'selectFeatureButton','".date('Y-m-d H:i:s')."', '".$user_id."',0,  'MAP_selectFeatureButton','selectFeatureButton','1');";
		
		$queryArr = explode(";",$query);
		for( $i = 0; $i< count($queryArr); $i++){
				if(trim($queryArr[$i])!==""){
					$db->setQuery(trim ($queryArr[$i]));
					if (!$db->query())
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					}
				}
			
		}
		
		$version="2.0.2";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='MAP'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
	
	}

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map'";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
				values('EasySDI - Map','option=com_easysdi_map','EasySDI Map','com_easysdi_map','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}

	$mainframe->enqueueMessage("Congratulations Map component for EasySdi is now installed and ready to be used. Enjoy EasySdi!","INFO");
	return true;
}
?>