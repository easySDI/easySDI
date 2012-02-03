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

	/**
	 * Gets the component versions
	 */

	$db->setQuery( "SELECT version FROM #__easysdi_version where component = 'com_easysdi_map'");
	$version = $db->loadResult();
	if (!$version)
	{
		$version="1.0";
		$query =
		"
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `#__easysdi_map_annotation_style`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_annotation_style`;
CREATE TABLE `#__easysdi_map_annotation_style` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_annotation_style
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_attribute`;
CREATE TABLE `#__easysdi_map_attribute` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ft` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `data_type` varchar(100) NOT NULL DEFAULT '',
  `width` bigint(20) DEFAULT NULL,
  `initial_visibility` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `misc_search` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_ft` (`id_ft`),
  CONSTRAINT `#__easysdi_map_attribute_ibfk_1` FOREIGN KEY (`id_ft`) REFERENCES `#__easysdi_map_feature_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_attribute_profile`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_attribute_profile`;
CREATE TABLE `#__easysdi_map_attribute_profile` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_attr` bigint(20) NOT NULL,
  `id_prof` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_attr` (`id_attr`),
  KEY `id_prof` (`id_prof`),
  CONSTRAINT `#__easysdi_map_attribute_profile_ibfk_1` FOREIGN KEY (`id_attr`) REFERENCES `#__easysdi_map_attribute` (`id`) ON DELETE CASCADE,
  CONSTRAINT `#__easysdi_map_attribute_profile_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `#__easysdi_community_profile` (`profile_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_attribute_profile
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_base_definition`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_base_definition`;
CREATE TABLE `#__easysdi_map_base_definition` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `projection` varchar(100) NOT NULL DEFAULT 'EPSG:4326',
  `unit` varchar(100) NOT NULL DEFAULT '',
  `minScale` varchar(100) NOT NULL DEFAULT 'auto',
  `maxScale` varchar(100) NOT NULL DEFAULT 'auto',
  `resolutions` text,
  `resolutionOverScale` tinyint(4) NOT NULL DEFAULT '0',
  `def` tinyint(1) NOT NULL DEFAULT '0',
  `extent` varchar(100) DEFAULT NULL,
  `maxExtent` varchar(100) NOT NULL DEFAULT '-180,-90,180,90',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_base_definition
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_base_layer`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_base_layer`;
CREATE TABLE `#__easysdi_map_base_layer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_base` bigint(20) NOT NULL,
  `url` varchar(400) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `layers` varchar(300) NOT NULL DEFAULT '',
  `maxExtent` varchar(100) NOT NULL DEFAULT '',
  `minScale` varchar(100) NOT NULL DEFAULT 'auto',
  `maxScale` varchar(100) NOT NULL DEFAULT 'auto',
  `resolutions` text,
  `resolutionOverScale` tinyint(1) NOT NULL DEFAULT '0',
  `projection` varchar(100) NOT NULL DEFAULT '',
  `unit` varchar(100) NOT NULL DEFAULT '',
  `img_format` varchar(100) NOT NULL DEFAULT 'image/png',
  `customStyle` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `singletile` tinyint(1) NOT NULL DEFAULT '0',
  `cache` tinyint(1) NOT NULL DEFAULT '0',
  `user` varchar(400) DEFAULT NULL,
  `password` varchar(400) DEFAULT NULL,
  `easysdi_account_id` bigint(20) DEFAULT NULL,
  `default_visibility` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '1',
  `default_opacity` float NOT NULL DEFAULT '1',
  `metadata_url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_base_def` (`id_base`),
  CONSTRAINT `fk_base_def` FOREIGN KEY (`id_base`) REFERENCES `#__easysdi_map_base_definition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_base_layer
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_comment_feature_type`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_comment_feature_type`;
CREATE TABLE `#__easysdi_map_comment_feature_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(250) NOT NULL,
  `feature_comment_count` varchar(250) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_comment_feature_type
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_config`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_config`;
CREATE TABLE `#__easysdi_map_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(500) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24;

-- ----------------------------
-- Records of #__easysdi_map_config
-- ----------------------------
INSERT INTO `#__easysdi_map_config` VALUES ('1', 'componentPath', null, '');
INSERT INTO `#__easysdi_map_config` VALUES ('2', 'componentUrl', null, '');
INSERT INTO `#__easysdi_map_config` VALUES ('3', 'projection', 'EPSG:27572', '');
INSERT INTO `#__easysdi_map_config` VALUES ('4', 'pubWfsUrl', null, 'url of publication database Wfs service proxy');
INSERT INTO `#__easysdi_map_config` VALUES ('5', 'maxFeatures', '1000', 'maximum number of features for WFS requests');
INSERT INTO `#__easysdi_map_config` VALUES ('6', 'pubFeatureNS', '', 'namespace of publication database Wfs service');
INSERT INTO `#__easysdi_map_config` VALUES ('7', 'pubFeaturePrefix', 'ms', 'prefix of publication database Wfs feature type');
INSERT INTO `#__easysdi_map_config` VALUES ('8', 'wpsReportsUrl', null, 'path to WPS service for report hook-up');
INSERT INTO `#__easysdi_map_config` VALUES ('9', 'shp2GmlUrl', null, 'path to Shp to Gml service - needs to be a local proxy as via Ajax');
INSERT INTO `#__easysdi_map_config` VALUES ('10', 'featureIdAttribute', 'unit_guid', 'name of attribute used to identify features (=the Primary Key)');
INSERT INTO `#__easysdi_map_config` VALUES ('11', 'maxSearchBars', '3', '');
INSERT INTO `#__easysdi_map_config` VALUES ('12', 'WMSFilterSupport', 'false', 'Does the server support filters in WMS requests.');
INSERT INTO `#__easysdi_map_config` VALUES ('13', 'pubWmsUrl', null, 'url of publication database Wms service proxy');
INSERT INTO `#__easysdi_map_config` VALUES ('14', 'defaultCoordMapZoom', '0', '');
INSERT INTO `#__easysdi_map_config` VALUES ('15', 'autocompleteNumChars', '4', '');
INSERT INTO `#__easysdi_map_config` VALUES ('16', 'autocompleteUseFID', '1', '');
INSERT INTO `#__easysdi_map_config` VALUES ('17', 'autocompleteMaxFeat', '50', '');
INSERT INTO `#__easysdi_map_config` VALUES ('18', 'layerProxyXMLFile', '', '');
INSERT INTO `#__easysdi_map_config` VALUES ('19', 'maptofopURL', null, 'Simple reporting service based on FOP.  HTTP-GET');
INSERT INTO `#__easysdi_map_config` VALUES ('20', 'numZoomLevels', '10', '');
INSERT INTO `#__easysdi_map_config` VALUES ('21', 'localisationInputWidth', '300', 'largeur de la liste déroulante des localisations');
INSERT INTO `#__easysdi_map_config` VALUES ('22', 'legendOrFilterPanelWidth', '250', 'Largeur du panel Légende');
INSERT INTO `#__easysdi_map_config` VALUES ('23', 'treePanelWidth', '250', 'Largeur du panel Couches');

-- ----------------------------
-- Table structure for `#__easysdi_map_context`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_context`;
CREATE TABLE `#__easysdi_map_context` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `WMC_text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_context
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_display_options`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_display_options`;
CREATE TABLE `#__easysdi_map_display_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `translation` varchar(200) DEFAULT NULL,
  `object` varchar(100) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of jos_easysdi_map_display_options
-- ----------------------------
INSERT INTO `jos_easysdi_map_display_options` VALUES ('1', 'EASYSDI_MAP_SIMPLESEARCH', 'SimpleSearch', '0');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('2', 'EASYSDI_MAP_ADVANCEDSEARCH', 'AdvancedSearch', '0');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('3', 'EASYSDI_MAP_DATAPRECISION', 'DataPrecision', '0');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('4', 'EASYSDI_MAP_LOCALISATION', 'Localisation', '1');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('5', 'EASYSDI_MAP_TOOLBAR', 'ToolBar', '1');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('6', 'EASYSDI_MAP_MAPOVERVIEW', 'MapOverview', '1');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('7', 'EASYSDI_MAP_ANNOTATION', 'Annotation', '0');
INSERT INTO `jos_easysdi_map_display_options` VALUES ('8', 'EASYSDI_MAP_COORDINATE', 'Coordinate', '1');

-- ----------------------------
-- Table structure for `#__easysdi_map_extension`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_extension`;
CREATE TABLE `#__easysdi_map_extension` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `extended_object` varchar(100) DEFAULT NULL,
  `extension_object` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_extension
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_extension_resource`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_extension_resource`;
CREATE TABLE `#__easysdi_map_extension_resource` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ext` bigint(20) DEFAULT NULL,
  `resource_type` varchar(100) DEFAULT NULL,
  `resource_folder` varchar(500) DEFAULT NULL,
  `resource_file` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ext` (`id_ext`),
  CONSTRAINT `#__easysdi_map_extension_resource_ibfk_1` FOREIGN KEY (`id_ext`) REFERENCES `#__easysdi_map_extension` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_extension_resource
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_extra_result_grid`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_extra_result_grid`;
CREATE TABLE `#__easysdi_map_extra_result_grid` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(500) DEFAULT NULL,
  `feature_type` bigint(20) DEFAULT NULL,
  `distinct_fk` varchar(100) NOT NULL DEFAULT '',
  `distinct_pk` varchar(100) NOT NULL DEFAULT '',
  `row_details_feature_type` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_extra_result_grid
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_feature_type`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_feature_type`;
CREATE TABLE `#__easysdi_map_feature_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `geometry` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_feature_type
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_feature_type_use`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_feature_type_use`;
CREATE TABLE `#__easysdi_map_feature_type_use` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_ft` bigint(20) NOT NULL,
  `id_use` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ft` (`id_ft`),
  KEY `id_use` (`id_use`),
  CONSTRAINT `#__easysdi_map_feature_type_use_ibfk_1` FOREIGN KEY (`id_ft`) REFERENCES `#__easysdi_map_feature_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `#__easysdi_map_feature_type_use_ibfk_2` FOREIGN KEY (`id_use`) REFERENCES `#__easysdi_map_use` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_feature_type_use
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_filter`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_filter`;
CREATE TABLE `#__easysdi_map_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `filter_data` text NOT NULL,
  `filter_mode` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_filter
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_localisation_layer`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_localisation_layer`;
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
  `extract_id_from_fid` tinyint(1) NOT NULL DEFAULT '1',
  `user` varchar(400) DEFAULT NULL,
  `password` varchar(400) DEFAULT NULL,
  `easysdi_account_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_localisation_layer
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_precision`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_precision`;
CREATE TABLE `#__easysdi_map_precision` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `title` varchar(250) DEFAULT NULL,
  `min_resolution` bigint(20) DEFAULT NULL,
  `max_resolution` bigint(20) DEFAULT NULL,
  `low_scale_switch_to` varchar(100) DEFAULT NULL,
  `style` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_precision
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_projection`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_projection`;
CREATE TABLE `#__easysdi_map_projection` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `title` varchar(200) DEFAULT NULL,
  `proj4text` varchar(500) NOT NULL DEFAULT '',
  `numDigits` int(2) NOT NULL DEFAULT '0',
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_projection
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_search_layer`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_search_layer`;
CREATE TABLE `#__easysdi_map_search_layer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `feature_type` bigint(20) DEFAULT NULL,
  `geometry_name` varchar(100) NOT NULL DEFAULT '',
  `row_details_feature_type` bigint(20) DEFAULT NULL,
  `styles` varchar(500) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_search_layer
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_service_account`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_service_account`;
CREATE TABLE `#__easysdi_map_service_account` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `partner_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_service_account
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_simple_search_additional_filter`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_simple_search_additional_filter`;
CREATE TABLE `#__easysdi_map_simple_search_additional_filter` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(100) NOT NULL DEFAULT '',
  `operator` varchar(5) NOT NULL DEFAULT '==',
  `title` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_simple_search_additional_filter
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_simple_search_type`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_simple_search_type`;
CREATE TABLE `#__easysdi_map_simple_search_type` (
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
-- Records of #__easysdi_map_simple_search_type
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_sst_erg`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_sst_erg`;
CREATE TABLE `#__easysdi_map_sst_erg` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_sst` bigint(20) NOT NULL,
  `id_erg` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_sst` (`id_sst`),
  KEY `id_erg` (`id_erg`),
  CONSTRAINT `#__easysdi_map_sst_erg_ibfk_1` FOREIGN KEY (`id_sst`) REFERENCES `#__easysdi_map_simple_search_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `#__easysdi_map_sst_erg_ibfk_2` FOREIGN KEY (`id_erg`) REFERENCES `#__easysdi_map_extra_result_grid` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_sst_erg
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_sst_saf`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_sst_saf`;
CREATE TABLE `#__easysdi_map_sst_saf` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_sst` bigint(20) NOT NULL,
  `id_saf` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_sst` (`id_sst`),
  KEY `id_saf` (`id_saf`),
  CONSTRAINT `#__easysdi_map_sst_saf_ibfk_1` FOREIGN KEY (`id_sst`) REFERENCES `#__easysdi_map_simple_search_type` (`id`) ON DELETE CASCADE,
  CONSTRAINT `#__easysdi_map_sst_saf_ibfk_2` FOREIGN KEY (`id_saf`) REFERENCES `#__easysdi_map_simple_search_additional_filter` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_sst_saf
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_map_use`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_map_use`;
CREATE TABLE `#__easysdi_map_use` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `translation` varchar(100) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_map_use
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_overlay_content`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_overlay_content`;
CREATE TABLE `#__easysdi_overlay_content` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `overlay_def_id` bigint(20) NOT NULL DEFAULT '0',
  `url` varchar(400) NOT NULL DEFAULT '',
  `url_type` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `layers` varchar(300) NOT NULL DEFAULT '',
  `maxExtent` varchar(100) NOT NULL DEFAULT '',
  `maxScale` varchar(100) NOT NULL DEFAULT 'auto',
  `minScale` varchar(100) NOT NULL DEFAULT 'auto',
  `resolutions` text,
  `resolutionOverScale` tinyint(1) NOT NULL DEFAULT '0',
  `projection` varchar(100) NOT NULL DEFAULT '',
  `img_format` varchar(100) NOT NULL DEFAULT 'image/png',
  `customStyle` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cache` tinyint(1) NOT NULL DEFAULT '0',
  `unit` varchar(100) NOT NULL DEFAULT '',
  `singletile` tinyint(1) NOT NULL DEFAULT '0',
  `user` varchar(400) DEFAULT NULL,
  `password` varchar(400) DEFAULT NULL,
  `overlay_group_id` bigint(20) DEFAULT NULL,
  `default_visibility` tinyint(1) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `default_opacity` float NOT NULL DEFAULT '1',
  `metadata_url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_overlay_content
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_overlay_definition`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_overlay_definition`;
CREATE TABLE `#__easysdi_overlay_definition` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `projection` varchar(100) NOT NULL DEFAULT 'EPSG:4326',
  `unit` varchar(100) NOT NULL DEFAULT '',
  `minResolution` varchar(100) NOT NULL DEFAULT 'auto',
  `maxResolution` varchar(100) NOT NULL DEFAULT 'auto',
  `def` tinyint(1) NOT NULL DEFAULT '0',
  `maxExtent` varchar(100) NOT NULL DEFAULT '-180,-90,180,90',
  `alias` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_overlay_definition
-- ----------------------------

-- ----------------------------
-- Table structure for `#__easysdi_overlay_group`
-- ----------------------------
DROP TABLE IF EXISTS `#__easysdi_overlay_group`;
CREATE TABLE `#__easysdi_overlay_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `open` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #__easysdi_overlay_group
-- ----------------------------
		
		";

		$db->setQuery( $query);
		if (!$db->queryBatch())		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/**
			* Update version
			* */
		$query="INSERT #__easysdi_version (component, version )values ('com_easysdi_map', $version)";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
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