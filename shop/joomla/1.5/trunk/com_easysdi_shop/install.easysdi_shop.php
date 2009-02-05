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

		$mainframe->enqueueMessage("Core component does not exists. Easysdi_shop could not be installed. Please install core component first.","ERROR");
		return false;
		
	}

	
	if (!file_exists(JPATH_SITE.DS.'components'.DS.'com_easysdi_catalog'.DS.'license.txt')){

		$mainframe->enqueueMessage("Catalog component is not installed. Easysdi_shop could not be installed. Please install catalog component first.","ERROR");
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
	
	
	/**
	 * Creates the database structure
	 */
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
	$query = "SELECT version FROM #__easysdi_version where component = 'com_easysdi_shop'";
	$db->setQuery( $query);

	$version = $db->loadResult();
	if ($db->getErrorNum()) {								
		//The table does'nt exist
		//Then nothing is installed.
		$version = '0';
	//	$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}
	if (!$version){
		$version="0";
	}	
	if (strlen($version)==0){$version ='0';}
				
	//When there is no DB version, then we create the full db
if ($version == '0') {	
	
	$query ="SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'";	
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	
		$query="INSERT INTO #__easysdi_version (id,component,version) VALUES
(null, 'com_easysdi_shop', '0.9')";

		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		
		
		$query="ALTER TABLE #__easysdi_product add column metadata_standard_id bigint(20) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		
	$query="
CREATE TABLE `#__easysdi_basemap_content` (
  `id` bigint(20) NOT NULL auto_increment,
  `basemap_def_id` bigint(20) NOT NULL default '0',
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
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_basemap_definition` (
  `id` int(20) NOT NULL auto_increment,
  `projection` varchar(100) NOT NULL default 'EPSG:4326',
  `unit` varchar(100) NOT NULL default '',
  `minResolution` varchar(100) NOT NULL default 'auto',
  `maxResolution` varchar(100) NOT NULL default 'auto',
  `def` tinyint(1) NOT NULL default '0',
  `maxExtent` varchar(100) NOT NULL default '-180,-90,180,90',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
	
	$query="CREATE TABLE `#__easysdi_order` (
  `order_id` bigint(20) NOT NULL auto_increment,
  `remark` varchar(4000) NOT NULL default '',
  `provider_id` bigint(20) NOT NULL default '0',
  `name` varchar(4000) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  `status` varchar(100) NOT NULL default '',
  `order_update` datetime NOT NULL default '0000-00-00 00:00:00',
  `third_party` bigint(20) NOT NULL default '0',
  `archived` tinyint(1) NOT NULL default '0',
  `RESPONSE_DATE` datetime NOT NULL default '0000-00-00 00:00:00',
  `RESPONSE_SEND` tinyint(1) NOT NULL default '0',
  `user_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`order_id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_order_product_list` (
  `id` bigint(20) NOT NULL auto_increment,
  `product_id` bigint(20) NOT NULL default '0',
  `order_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_idx1` (`product_id`,`order_id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_order_product_perimeters` (
  `id` bigint(20) NOT NULL auto_increment,
  `perimeter_id` bigint(20) NOT NULL default '0',
  `order_id` bigint(20) NOT NULL default '0',
  `value` varchar(400) NOT NULL default '',
  `text` varchar(400) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_order_product_properties` (
  `id` bigint(20) NOT NULL auto_increment,
  `order_product_list_id` bigint(20) NOT NULL default '0',
  `property_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}


	$query="CREATE TABLE `#__easysdi_perimeter_definition` (
  `id` bigint(20) NOT NULL auto_increment,
  `wfs_url` varchar(4000) NOT NULL default '',
  `layer_name` varchar(4000) NOT NULL default '',
  `perimeter_name` varchar(4000) NOT NULL default '',
  `area_field_name` varchar(100) NOT NULL default '',
  `name_field_name` varchar(100) NOT NULL default '',
  `id_field_name` varchar(100) NOT NULL default '',
  `wms_url` varchar(400) NOT NULL default '',
  `feature_type_name` varchar(400) NOT NULL default '',
  `perimeter_desc` varchar(4000) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_product` (
  `metadata_id` varchar(300) NOT NULL default '',
  `id` bigint(20) NOT NULL auto_increment,
  `update_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `supplier_name` varchar(4000) NOT NULL default '',
  `surface_min` bigint(20) NOT NULL default '0',
  `surface_max` bigint(20) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `orderable` int(1) NOT NULL default '0',
  `weight` bigint(20) NOT NULL default '0',
  `external` tinyint(1) NOT NULL default '0',
  `internal` tinyint(1) NOT NULL default '0',
  `partner_id` bigint(20) NOT NULL default '0',
  `data_title` varchar(4000) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_product_perimeter` (
  `id` bigint(20) NOT NULL auto_increment,
  `product_id` bigint(20) NOT NULL default '0',
  `perimeter_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniqueKey` (`product_id`,`perimeter_id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_product_properties_definition` (
  `id` bigint(20) NOT NULL auto_increment,
  `order` bigint(20) NOT NULL default '0',
  `mandatory` tinyint(1) NOT NULL default '1',
  `partner_id` bigint(20) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `update_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` varchar(4000) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_product_properties_values_definition` (
  `id` bigint(20) NOT NULL auto_increment,
  `properties_id` bigint(20) NOT NULL default '0',
  `order` bigint(20) NOT NULL default '0',
  `value` varchar(4000) NOT NULL default '',
  `text` varchar(4000) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$query="CREATE TABLE `#__easysdi_product_property` (
  `id` bigint(20) NOT NULL auto_increment,
  `product_id` bigint(20) NOT NULL default '0',
  `property_value_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_idx1` (`product_id`,`property_value_id`)
)"; 
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	$version = "0.9";
}

if ($version == "0.9"){
		
	$query="UPDATE #__easysdi_version set version = '0.91' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.91";

		
		
$query="CREATE TABLE `#__easysdi_metadata_classes` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `iso_key` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) NOT NULL default '0',
  `is_global` tinyint(1) NOT NULL default '0',
  `description` varchar(4000) NOT NULL default '',
  `is_final` tinyint(1) NOT NULL default '0',
  `is_editable` tinyint(1) NOT NULL default '1',
  `type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)" ;
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_classes_classes` (
  `id` bigint(20) NOT NULL auto_increment,
  `classes_from_id` bigint(20) NOT NULL default '0',
  `classes_to_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_classes_ext` (
  `id` bigint(20) NOT NULL auto_increment,
  `classes_id` bigint(20) NOT NULL default '0',
  `ext_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_classes_freetext` (
  `id` bigint(20) NOT NULL auto_increment,
  `classes_id` bigint(20) NOT NULL default '0',
  `freetext_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)" ;
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_classes_list` (
  `id` bigint(20) NOT NULL auto_increment,
  `classes_id` bigint(20) NOT NULL default '0',
  `list_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_classes_locfreetext` (
  `id` bigint(20) NOT NULL auto_increment,
  `classes_id` bigint(20) NOT NULL default '0',
  `loc_freetext_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `partner_id` bigint(20) default '0',
  `name` varchar(100) NOT NULL default '',
  `is_global` tinyint(1) NOT NULL default '0',
  `iso_key` varchar(100) NOT NULL default 'gco:DateTime',
  `default_value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_ext` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(400) NOT NULL default '',
  `value` varchar(1000) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_freetext` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) default '0',
  `is_global` tinyint(1) NOT NULL default '0',
  `is_constant` tinyint(1) NOT NULL default '0',
  `is_date` tinyint(1) NOT NULL default '0',
  `is_id` tinyint(1) NOT NULL default '0',
  `default_value` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_list` (
  `id` bigint(20) NOT NULL auto_increment,
  `partner_id` bigint(20) NOT NULL default '0',
  `multiple` tinyint(1) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)" ;
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_list_content` (
  `id` bigint(20) NOT NULL auto_increment,
  `list_id` bigint(20) NOT NULL default '0',
  `code_key` varchar(100) NOT NULL default '',
  `key` varchar(100) NOT NULL default '',
  `value` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) default '0',
  `is_global` tinyint(1) NOT NULL default '0',
  `default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)" ;
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_loc_freetext` (
  `id` bigint(20) NOT NULL auto_increment,
  `lang` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) default '0',
  `is_global` tinyint(1) NOT NULL default '0',
  `default_value` varchar(4000) NOT NULL default '',
  PRIMARY KEY  (`id`)
)" ;
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_numeric` (
  `id` bigint(20) NOT NULL auto_increment,
  `default_value` bigint(20) NOT NULL default '0',
  `min_value` bigint(20) default '0',
  `name` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) default '0',
  `max_value` bigint(20) default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_standard` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `inherited` varchar(100) default '',
  `partner_id` bigint(20) default '0',
  `is_global` tinyint(1) NOT NULL default '0',
  `is_deleted` tinyint(1) NOT NULL default '0',
  `key` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_standard_classes` (
  `id` bigint(20) NOT NULL auto_increment,
  `standard_id` bigint(20) NOT NULL default '0',
  `position` bigint(20) NOT NULL default '0',
  `partner_id` bigint(20) NOT NULL default '0',
  `tab_id` bigint(20) NOT NULL default '0',
  `class_id` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metadata_tabs` (
  `id` bigint(20) NOT NULL auto_increment,
  `partner_id` bigint(20) NOT NULL default '0',
  `text` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 
$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}
$query="CREATE TABLE `#__easysdi_metatada_constant` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `partner_id` bigint(20) default '0',
  `value` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 



	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

	
	
		
		
	
}

if ($version == "0.91"){
		
	$query="UPDATE #__easysdi_version set version = '0.92' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.92";
	
	
}
if ($version == "0.92" || $version == "0.93" || $version == "0.94" || $version == "0.95" || $version == "0.96"){
		
	$query="UPDATE #__easysdi_version set version = '0.97' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.97";
	
	
	$query="ALTER TABLE #__easysdi_metadata_freetext add column is_number tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	
	
		$query="SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\"";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
$query = "DELETE FROM `#__easysdi_metadata_classes`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

		
$query="INSERT INTO `#__easysdi_metadata_classes` (`id`, `name`, `iso_key`, `partner_id`, `is_global`, `description`, `is_final`, `is_editable`, `type`) VALUES
(2, 'gmd:fileIdentifier', 'gmd:fileIdentifier', 0, 0, 'Unique id fileIdentifier', 1, 1, 'freetext'),
(3, 'gmd:organisationName', 'gmd:organisationName', 0, 0, 'Organisation name', 0, 1, 'locfreetext'),
(4, 'gmd:individualName', 'gmd:individualName', 0, 0, 'Individual name', 0, 1, 'freetext'),
(5, 'gmd:CI_ResponsibleParty (Point Of Contact)', 'gmd:CI_ResponsibleParty', 0, 0, 'Responsible Party', 0, 1, 'class'),
(6, 'gmd:voice', 'gmd:voice', 0, 0, 'Phone voice number', 0, 1, 'freetext'),
(7, 'gmd:facsimile', 'gmd:facsimile', 0, 0, 'Fax', 0, 1, 'freetext'),
(8, 'gmd:CI_Telephone', 'gmd:CI_Telephone', 0, 0, 'Téléphone', 0, 1, 'class'),
(9, 'gmd:metadataStandardName', 'gmd:metadataStandardName', 0, 0, 'Nom de la norme de la métadonnée', 1, 1, 'freetext'),
(10, 'gmd:deliveryPoint', 'gmd:deliveryPoint', 0, 0, 'gmd:deliveryPoint', 0, 1, 'freetext'),
(11, 'gmd:city', 'gmd:city', 0, 0, 'City', 0, 1, 'freetext'),
(12, 'gmd:postalCode', 'gmd:postalCode', 0, 0, 'Postal Code', 0, 1, 'freetext'),
(13, 'gmd:country', 'gmd:country', 0, 0, 'Country', 0, 1, 'freetext'),
(14, 'gmd:electronicMailAddress', 'gmd:electronicMailAddress', 0, 0, 'Electronic Mail Address', 0, 1, 'freetext'),
(15, 'gmd:CI_Address', 'gmd:CI_Address', 0, 0, 'Address', 0, 1, 'class'),
(16, 'gmd:phone', 'gmd:phone', 0, 0, 'Phone', 0, 1, 'class'),
(17, 'gmd:address', 'gmd:addres', 0, 0, 'Address', 0, 1, 'class'),
(18, 'gmd:CI_Contact', 'gmd:CI_Contact', 0, 0, 'CI_Contact', 0, 1, 'class'),
(19, 'gmd:contactInfo', 'gmd:contactInfo', 0, 0, 'contactInfo', 0, 1, 'class'),
(20, 'gmd:contact', 'gmd:contact', 0, 0, 'Contact', 1, 1, 'class'),
(21, 'gmd:textGroup', 'gmd:textGroup', 0, 0, 'TextGroup', 0, 1, 'locfreetext'),
(22, 'gmd:PT_FreeText', 'gmd:PT_FreeText', 0, 0, 'gmd:PT_FreeText', 0, 1, 'class'),
(23, 'gmd:title', 'gmd:title', 0, 0, 'Titre', 0, 1, 'locfreetext'),
(24, 'gmd:CI_Citation', 'gmd:CI_Citation', 0, 0, 'gmd:CI_Citation', 0, 1, 'class'),
(25, 'gmd:abstract', 'gmd:abstract', 0, 0, 'Description générale', 0, 1, 'locfreetext'),
(27, 'gmd:metadataExtensionInfo', 'gmd:metadataExtensionInfo', 0, 1, 'Extension', 1, 1, 'ext'),
(28, 'gmd:CI_Citation', 'gmd:CI_Citation', 0, 0, 'gmd:CI_Citation', 0, 1, 'class'),
(29, 'gmd:citation', 'gmd:citation', 0, 0, 'gmd:citation', 0, 1, 'class'),
(30, 'gmd:pointOfContact', 'gmd:pointOfContact', 0, 0, 'gmd:pointOfContact', 0, 1, 'class'),
(31, 'gmd:MD_DataIdentification', 'gmd:MD_DataIdentification', 0, 0, 'gmd:MD_DataIdentification', 0, 1, 'class'),
(32, 'gmd:identificationInfo', 'gmd:identificationInfo', 0, 1, 'Identification de la donnée', 1, 1, 'class'),
(33, 'gmd:fees', 'gmd:fees', 0, 0, 'Principe de tartification', 0, 1, 'locfreetext'),
(34, 'gmd:MD_StandardOrderProcess', 'gmd:MD_StandardOrderProcess', 0, 0, 'gmd:MD_StandardOrderProcess', 0, 1, 'class'),
(35, 'gmd:distributionOrderProcess', 'gmd:distributionOrderProcess', 0, 0, 'gmd:distributionOrderProcess', 0, 1, 'class'),
(36, 'gmd:MD_Distributor', 'gmd:MD_Distributor', 0, 0, 'gmd:MD_Distributor', 0, 1, 'class'),
(37, 'gmd:distributor', 'gmd:distributor', 0, 0, 'gmd:distributor', 0, 1, 'class'),
(38, 'gmd:MD_Distribution', 'gmd:MD_Distribution', 0, 0, 'gmd:MD_Distribution', 0, 1, 'class'),
(39, 'gmd:distributionInfo', 'gmd:distributionInfo', 0, 0, 'Information de diffusion', 1, 1, 'class'),
(40, 'gmd:statement', 'gmd:statement', 0, 0, 'Explications', 0, 1, 'locfreetext'),
(41, 'gmd:description', 'gmd:description', 0, 0, 'gmd:description', 0, 1, 'locfreetext'),
(42, 'gmd:LI_Source', 'gmd:LI_Source', 0, 0, 'gmd:LI_Source', 0, 1, 'class'),
(43, 'gmd:source', 'gmd:source', 0, 0, 'gmd:source', 0, 1, 'class'),
(44, 'gmd:LI_ProcessStep', 'gmd:LI_ProcessStep', 0, 0, 'gmd:LI_ProcessStep', 0, 1, 'class'),
(45, 'gmd:processStep', 'gmd:processStep', 0, 0, 'gmd:processStep', 0, 1, 'class'),
(46, 'gmd:LI_Lineage', 'gmd:LI_Lineage', 0, 0, 'gmd:LI_Lineage', 0, 1, 'class'),
(47, 'gmd:lineage', 'gmd:lineage', 0, 0, 'gmd:lineage', 0, 1, 'class'),
(48, 'gmd:DQ_DataQuality', 'gmd:DQ_DataQuality', 0, 0, 'DATA_QUALITY', 0, 1, 'class'),
(49, 'gmd:dataQualityInfo', 'gmd:dataQualityInfo', 0, 1, 'Qualité de la donnée', 1, 1, 'class'),
(50, 'gmd:otherConstraints (Legal constaint)', 'gmd:otherConstraints', 0, 0, 'Contrainte légale', 0, 1, 'locfreetext'),
(51, 'gmd:MD_LegalConstraints', 'gmd:MD_LegalConstraints', 0, 0, 'Contrainte légale', 0, 1, 'class'),
(52, 'gmd:resourceConstraints  (Legal Constaint)', 'gmd:resourceConstraints ', 0, 0, 'gmd:resourceConstraints ', 0, 1, 'class'),
(53, 'gmd:useLimitation', 'gmd:useLimitation', 0, 0, 'Limite d\'utilisation', 0, 1, 'locfreetext'),
(54, 'gmd:resourceConstraints(Use limitation)', 'gmd:resourceConstraints', 0, 0, 'gmd:resourceConstraints', 0, 1, 'class'),
(55, 'gmd:MD_LegalConstraints(Use limitation)', 'gmd:MD_LegalConstraints', 0, 0, 'gmd:MD_LegalConstraints(Use limitation)', 0, 1, 'class'),
(56, 'gmd:MD_TopicCategoryCode', 'gmd:MD_TopicCategoryCode', 0, 0, 'Thématique', 0, 1, 'list'),
(57, 'gmd:topicCategory', 'gmd:topicCategory', 0, 0, 'gmd:topicCategory', 0, 1, 'class'),
(58, 'gmd:westBoundLongitude', 'gmd:westBoundLongitude', 0, 0, 'Ouest', 0, 1, 'freetext'),
(62, 'gmd:southBoundLatitude', 'gmd:southBoundLatitude', 0, 0, 'Sud', 0, 1, 'freetext'),
(61, 'gmd:eastBoundLongitude', 'gmd:eastBoundLongitude', 0, 0, 'Est', 0, 1, 'freetext'),
(63, 'gmd:northBoundLatitude', 'gmd:northBoundLatitude', 0, 0, 'Nord', 0, 1, 'freetext'),
(64, 'gmd:EX_GeographicBoundingBox', 'gmd:EX_GeographicBoundingBox', 0, 0, 'gmd:EX_GeographicBoundingBox', 0, 1, 'class'),
(65, 'gmd:geographicElement', 'gmd:geographicElement', 0, 0, 'gmd:geographicElement', 0, 1, 'class'),
(66, 'gmd:EX_Extent', 'gmd:EX_Extent', 0, 0, 'gmd:EX_Extent', 0, 1, 'class'),
(67, 'gmd:CI_RoleCode (Point Of Contact)', 'gmd:CI_RoleCode', 0, 0, 'gmd:CI_RoleCode ', 0, 1, 'freetext'),
(68, 'gmd:role', 'gmd:role', 0, 0, 'gmd:role', 0, 1, 'class'),
(69, 'gmd:extent', 'gmd:extent', 0, 0, 'gmd:extent', 0, 1, 'class'),
(70, 'gmd:description (Extent)', 'gmd:description', 0, 0, 'Description de l\'étendue géographique', 0, 1, 'locfreetext'),
(71, 'gmd:description (DQ)', 'gmd:description', 0, 0, 'Description du processus qualité', 0, 1, 'locfreetext'),
(72, 'gmd:description(Source DQ)', 'gmd:description', 0, 0, 'Description source', 0, 1, 'locfreetext')";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_classes_classes`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_classes_classes` (`id`, `classes_from_id`, `classes_to_id`) VALUES
(75, 15, 12),
(74, 15, 14),
(73, 15, 10),
(131, 5, 3),
(130, 5, 4),
(70, 8, 6),
(69, 8, 7),
(72, 15, 13),
(71, 15, 11),
(76, 16, 8),
(77, 17, 15),
(79, 18, 16),
(78, 18, 17),
(80, 19, 18),
(129, 5, 19),
(81, 20, 5),
(82, 22, 21),
(144, 69, 65),
(84, 24, 23),
(143, 69, 70),
(53, 28, 23),
(54, 29, 24),
(133, 30, 5),
(141, 31, 54),
(140, 31, 52),
(139, 31, 30),
(153, 32, 31),
(86, 34, 33),
(87, 35, 34),
(88, 36, 35),
(90, 37, 36),
(91, 38, 37),
(152, 39, 38),
(146, 42, 72),
(148, 43, 42),
(150, 44, 43),
(149, 44, 71),
(97, 45, 44),
(98, 46, 45),
(99, 46, 40),
(100, 47, 46),
(132, 48, 47),
(151, 49, 48),
(154, 51, 50),
(105, 52, 51),
(108, 54, 55),
(107, 55, 53),
(109, 57, 56),
(110, 64, 61),
(111, 64, 63),
(112, 64, 62),
(113, 64, 58),
(114, 65, 64),
(115, 66, 41),
(116, 66, 65),
(138, 31, 69),
(137, 31, 29),
(136, 31, 25),
(123, 68, 67),
(128, 5, 67),
(142, 31, 57)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_classes_ext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_classes_ext` (`id`, `classes_id`, `ext_id`) VALUES
(6, 27, 2)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_classes_freetext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_classes_freetext` (`id`, `classes_id`, `freetext_id`) VALUES
(23, 7, 1),
(19, 2, 4),
(21, 4, 1),
(25, 9, 2),
(27, 10, 1),
(28, 11, 0),
(30, 12, 0),
(31, 13, 0),
(32, 14, 0),
(41, 58, 5),
(22, 6, 0),
(40, 61, 5),
(43, 62, 5),
(42, 63, 5),
(39, 67, 6)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

$query = "DELETE FROM `#__easysdi_metadata_classes_list`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}		
$query="INSERT INTO `#__easysdi_metadata_classes_list` (`id`, `classes_id`, `list_id`) VALUES
(4, 56, 1)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_classes_locfreetext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_classes_locfreetext` (`id`, `classes_id`, `loc_freetext_id`) VALUES
(24, 21, 2),
(23, 3, 2),
(41, 72, 2),
(33, 33, 2),
(38, 71, 2),
(37, 40, 2),
(36, 70, 2),
(27, 41, 2),
(35, 25, 2),
(42, 50, 2),
(34, 23, 2),
(30, 53, 2)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

$query = "DELETE FROM `#__easysdi_metadata_ext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_ext` (`id`, `name`, `value`, `description`, `partner_id`) VALUES
(1, 'champ 1 ', '', '', 0),
(2, 'champ 2', '', '', 0)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_freetext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
$query="INSERT INTO `#__easysdi_metadata_freetext` (`id`, `name`, `description`, `partner_id`, `is_global`, `is_constant`, `is_date`, `is_number`, `is_id`, `default_value`) VALUES
(1, 'Simple freetext without a default value', 'Simple freetext field without default value', 0, 0, 0, 0, 0, 0, ''),
(2, 'ISO 19115:2003/19139 Freetext', 'Free text contenant la norme des métadonnées utilisée', 0, 0, 1, 0, 0, 0, 'ISO 19115:2003/19139'),
(3, 'Texte en  Francais suisse', 'Texte en  Francais suisse', 0, 1, 0, 0, 0, 0, ''),
(4, 'UUID', 'dentification du fichier', 0, 0, 1, 0, 0, 1, ''),
(5, 'Number', 'Number', 0, 0, 0, 0, 1, 0, ''),
(6, 'pointOfContact', 'pointOfContact', 0, 0, 0, 0, 0, 0, 'pointOfContact')";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

$query = "DELETE FROM `#__easysdi_metadata_list`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_list` (`id`, `partner_id`, `multiple`, `name`) VALUES
(1, 0, 1, 'thématique')";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_list_content`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_list_content` (`id`, `list_id`, `code_key`, `key`, `value`, `partner_id`, `is_global`, `default`) VALUES
(1, 0, '21', '22', '23', 1, 0, 0),
(15, 1, 'farming', 'farming', 'farming', 0, 0, 0),
(14, 1, 'biota', 'biota', 'biota', 0, 0, 0),
(10, 0, 'farming', 'farming', 'farming', 0, 0, 0),
(11, 0, 'biota', 'biota', 'biota', 0, 0, 0),
(12, 0, 'boundaries', 'boundaries', 'boundaries', 0, 0, 0),
(13, 1, 'boundaries', 'boundaries', 'boundaries', 0, 0, 0),
(16, 1, 'climatologyMeterologyAtmosphere', 'climatologyMeterologyAtmosphere', 'climatologyMeterologyAtmosphere', 0, 0, 0),
(17, 1, 'economy', 'economy', 'economy', 0, 0, 0),
(18, 1, 'elevation', 'elevation', 'elevation', 0, 0, 0),
(19, 1, 'environment', 'environment', 'environment', 0, 0, 0),
(20, 1, 'geoscientificInformation', 'geoscientificInformation', 'geoscientificInformation', 0, 0, 0),
(21, 1, 'health', 'health', 'health', 0, 0, 0),
(22, 1, 'imageryBaseMapsEarthCover', 'imageryBaseMapsEarthCover', 'imageryBaseMapsEarthCover', 0, 0, 0),
(23, 1, 'intelligenceMilitary', 'intelligenceMilitary', 'intelligenceMilitary', 0, 0, 0),
(24, 1, 'inlandWaters', 'inlandWaters', 'inlandWaters', 0, 0, 0),
(25, 1, 'location', 'location', 'location', 0, 0, 0),
(26, 1, 'oceans', 'oceans', 'oceans', 0, 0, 0),
(27, 1, 'planningCadastre', 'planningCadastre', 'planningCadastre', 0, 0, 0),
(28, 1, 'society', 'society', 'society', 0, 0, 0),
(29, 1, 'structure', 'structure', 'structure', 0, 0, 0),
(30, 1, 'transportation', 'transportation', 'transportation', 0, 0, 0),
(31, 1, 'utilitiesCommunications', 'utilitiesCommunications', 'utilitiesCommunications', 0, 0, 0)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

$query = "DELETE FROM `#__easysdi_metadata_loc_freetext`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_loc_freetext` (`id`, `lang`, `name`, `description`, `partner_id`, `is_global`, `default_value`) VALUES
(1, 'fr-CH', 'Texte en  Francais suisse sans valeur par défaut', 'Texte en  Francais suisse sans valeur par défaut', 1, 1, ''),
(2, 'en-GB', 'Texte en  Anglais GB sans valeur par défaut', 'Texte en  Anglais GB sans valeur par défaut', 1, 1, '')";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

$query = "DELETE FROM `#__easysdi_metadata_standard`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_standard` (`id`, `name`, `inherited`, `partner_id`, `is_global`, `is_deleted`, `key`) VALUES
(1, 'ISO19115 - CORE', '0', 0, 1, 1, 'ISO19115'),
(2, 'ISO 19115:2003/19139 ', '0', 0, 1, 0, ''),
(3, 'qui hérite de 19115 ', '2', 0, 0, 1, ''),
(4, '2 hérite  de 191339', '2', 1, 0, 1, ''),
(5, 'dddd', '2', 1, 0, 1, '')";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_standard_classes`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_standard_classes` (`id`, `standard_id`, `position`, `partner_id`, `tab_id`, `class_id`) VALUES
(66, 1, 0, 1, 1, 2),
(87, 2, 0, 0, 10, 49),
(68, 1, 0, 1, 2, 20),
(86, 2, 0, 0, 1, 39),
(70, 1, 0, 1, 1, 9),
(84, 2, 0, 0, 2, 32),
(72, 1, 0, 1, 3, 20),
(83, 2, 0, 0, 9, 20),
(75, 0, 0, 1, 2, 20),
(76, 0, 0, 1, 1, 2),
(77, 0, 0, 1, 1, 2),
(78, 0, 0, 1, 1, 2),
(79, 5, 0, 1, 1, 2),
(80, 5, 0, 1, 1, 20),
(81, 5, 0, 0, 1, 27),
(82, 2, 0, 0, 2, 2)";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query = "DELETE FROM `#__easysdi_metadata_tabs`";
	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
$query="INSERT INTO `#__easysdi_metadata_tabs` (`id`, `partner_id`, `text`) VALUES
(1, 0, 'Diffusion'),
(2, 0, 'Identification'),
(3, 0, 'Statut juridique'),
(4, 0, 'Mise à jour'),
(5, 0, 'Représentation'),
(6, 0, 'Produit vecteur'),
(7, 0, 'Produit raster'),
(8, 0, 'Attribut'),
(9, 0, 'Contact'),
(10, 0, 'Aquisition')";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

		

}

if ($version == "0.97"){
		
	$query="UPDATE #__easysdi_version set version = '0.98' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.98";
	
		$query="ALTER TABLE #__easysdi_product_properties_definition add column type_code varchar(100) NOT NULL default ''";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	
	
		
		
}


if ($version == "0.98"){
	
$query="UPDATE #__easysdi_version set version = '0.99' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.99";
	
		$query="ALTER TABLE #__easysdi_product add column is_free tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	
		$query="ALTER TABLE #__easysdi_perimeter_definition add column wms_scale_min double NOT NULL default '0'";
		
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column wms_scale_max double NOT NULL default '0'";
		
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column filter_field_name varchar(100) NOT NULL default ''";
		
		$db->setQuery( $query);
	
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}		

		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column id_perimeter_filter bigint(20) NOT NULL default '0'";
		
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}		
		
 
  	$query="ALTER TABLE #__easysdi_perimeter_definition add column is_localisation tinyint(1) NOT NULL default '0'";
		
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}	
  
		
		$query="CREATE TABLE `#__easysdi_user_product_favorite` (`id` bigint(20) NOT NULL auto_increment, `partner_id` bigint(20) NOT NULL default '0', `product_id` bigint(20) NOT NULL default '0', `notify_metadata_modification` tinyint(1) NOT NULL default '0', PRIMARY KEY  (`id`))"; 
$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}	
		
		
	
}
if ($version == "0.99"){
	
$query="UPDATE #__easysdi_version set version = '0.991' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.991";
	
		$query="ALTER TABLE #__easysdi_order_product_list add column `status` varchar(100) NOT NULL default 'AWAIT'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}


		$query="ALTER TABLE #__easysdi_order_product_list add column `data` longblob ";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		  

		$query="ALTER TABLE #__easysdi_order_product_list add `filename` varchar(100) default '' ";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add `maxfeatures` int(11) NOT NULL default '-1'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add `searchbox` tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add `sort` tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}	  
}


if ($version == "0.991"){
	
	$query="UPDATE #__easysdi_version set version = '0.992' where component = 'com_easysdi_shop'";

	$db->setQuery( $query);

	if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	$version = "0.992";
	
		$query="ALTER TABLE #__easysdi_order_product_list add column `remark` varchar(4000) NOT NULL default ''";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}


  	$query="ALTER TABLE #__easysdi_order_product_list add column `price` decimal(10,0) NOT NULL default '0'";
		$db->setQuery( $query);

		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
  

}



	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	
	if ($id){
	
	}else{
			$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
		    return false;	
	
	//Insert the EasySdi Main Menu		
	/*$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Easy SDI','option=com_easysdi_proxy','option=com_easysdi_proxy','Easysdi main menu','com_easysdi_proxy','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();
	*/	
	}
	
			
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'SHOP','','option=com_easysdi_shop','SHOP','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	
	
	
	
	
$mainframe->enqueueMessage("Congratulation shop for EasySdi is installed and ready to be used. 
Enjoy EasySdi!","INFO");

}


?>