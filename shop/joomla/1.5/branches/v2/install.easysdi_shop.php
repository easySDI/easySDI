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

jimport( 'joomla.filesystem.folder' );

function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();
	
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');

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
	 * Check the CATALOG installation
	 */
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_catalog' ";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Catalog component does not exist. Easysdi Shop could not be installed. Please install Catalog component first.","ERROR");
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
	 * Gets the component version
	 */
	$version = '0.0';
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'SHOP'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version= '0.1';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'SHOP', 'com_easysdi_shop', 'com_easysdi_shop', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_shop', 'com_sdi_shop', '".$version."')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	
		$query ="SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/**
		 * todo : move those SQL statements in the core installation
		 * @var unknown_type
		 */
		$query="CREATE TABLE `#__easysdi_location_definition` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `wfs_url` varchar(4000) NOT NULL default '',
		  `location_name` varchar(4000) NOT NULL default '',
		  `area_field_name` varchar(100) NOT NULL default '',
		  `name_field_name` varchar(100) NOT NULL default '',
		  `id_field_name` varchar(100) NOT NULL default '',
		  `feature_type_name` varchar(400) NOT NULL default '',
		  `filter_field_name` varchar(100) NOT NULL default '',
		  `id_location_filter` bigint(20) NOT NULL default '0',
		  `is_localisation` tinyint(1) NOT NULL default '0',
		  `location_desc` varchar(4000) NOT NULL default '',
		  `maxfeatures` int(11) NOT NULL default '-1',
		  `searchbox` tinyint(1) NOT NULL default '0',
		   `sort` tinyint(1) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_location_definition add column user varchar(400)";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

		$query="ALTER TABLE #__easysdi_location_definition add column password varchar(400)";
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

		$query="ALTER TABLE #__easysdi_product add column metadata_standard_id bigint(20) NOT NULL default '0'";
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

		$query="ALTER TABLE #__easysdi_product_properties_definition add column type_code varchar(100) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
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
		
		$query="ALTER TABLE #__easysdi_product add column `metadata_partner_id` bigint(20) default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="INSERT INTO `#__sdi_list_role` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`, `publish_id`, `roletype_id`) VALUES
					('".helper_easysdi::getUniqueId()."', 'EASYSDI_FAVORITE_RIGHT', 'EASYSDI_FAVORITE_RIGHT', 'EASYSDI_FAVORITE_RIGHT', 'Gestion des favoris', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1'),
					('".helper_easysdi::getUniqueId()."', 'EASYSDI_PRODUCT_RIGHT', 'EASYSDI_PRODUCT_RIGHT', 'EASYSDI_PRODUCT_RIGHT', 'Gestion des produits', '".date('Y-m-d H:i:s')."', '".$user_id."', 0, '1')";
					$db->setQuery( $query);	
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}

		$query="ALTER TABLE `#__easysdi_basemap_content` add column `img_format` varchar(100) NOT NULL default 'image/png'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE `#__easysdi_perimeter_definition` add column `img_format` varchar(100) NOT NULL default 'image/png'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE `#__easysdi_product` add column `hasMetadata` tinyint(4) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}


		$query = "insert  into #__easysdi_config (thekey, value) values('ARCHIVE_DELAY','1')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('HISTORY_DELAY','1')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_order add column buffer bigint(20) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product_perimeter add column isBufferAllowed tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}


		$query="ALTER TABLE #__easysdi_product add column previewBaseMapId bigint(20)";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewWmsUrl varchar(400) default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewWmsLayers varchar(400) default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewMinResolution bigint(20) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewMaxResolution bigint(20) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_basemap_definition add column alias  varchar(400) default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewProjection varchar(400) NOT NULL default 'EPSG:4326'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewUnit varchar(400) NOT NULL default 'degrees'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column previewImageFormat varchar(400) NOT NULL default 'image/png'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column min_resolution bigint(20) NOT NULL default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	
		$query="ALTER TABLE #__easysdi_perimeter_definition add column max_resolution bigint(20) NOT NULL default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

		$query="ALTER TABLE #__easysdi_basemap_content add column ordering int(11) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_perimeter_definition add column user varchar(400) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_perimeter_definition add column password varchar(400) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_basemap_content add column user varchar(400) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_basemap_content add column password varchar(400) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('PROXYHOST','index.php?option=com_easysdi_shop&no_html=1&task=proxy')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_order_product_list add `remark` varchar(4000) default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
	
	 	$query="ALTER TABLE #__easysdi_order_product_list add `price` decimal(10,0) default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column perimeter_code varchar(400) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_order_product_properties add column property_value varchar(4000) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_product_properties_definition add column code varchar(400) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_order_product_properties add column code varchar(400) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_perimeter_definition add column ordering double NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query="ALTER TABLE #__easysdi_basemap_definition add column restrictedExtent tinyint(1) NOT NULL default '0'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_basemap_definition add column restrictedScales varchar(100) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_order add column order_date datetime NOT NULL";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_order drop column archived";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_order_product_list alter column status drop default";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE #__easysdi_order_status_list (id bigint(2) NOT NULL AUTO_INCREMENT, code varchar(20) NOT NULL, translation varchar(50) NOT NULL, name varchar(50) NOT NULL, description varchar(200), created datetime, updated datetime, primary key (id))";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('SAVED','EASYSDI_ORDER_STATUS_SAVED', 'SAVED')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('SENT','EASYSDI_ORDER_STATUS_SENT', 'SENT')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('PROGRESS','EASYSDI_ORDER_STATUS_PROGRESS', 'PROGRESS')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('AWAIT','EASYSDI_ORDER_STATUS_AWAIT', 'AWAIT')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('FINISH','EASYSDI_ORDER_STATUS_FINISH', 'FINISH')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('ARCHIVED','EASYSDI_ORDER_STATUS_ARCHIVED', 'ARCHIVED')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_status_list (code, translation, name) values('HISTORIZED','EASYSDI_ORDER_STATUS_HISTORIZED', 'HISTORIZED')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE #__easysdi_order_product_status_list (id bigint(2) NOT NULL AUTO_INCREMENT, code varchar(20) NOT NULL, translation varchar(50) NOT NULL, name varchar(50) NOT NULL, description varchar(200), created datetime, updated datetime, primary key (id))";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "insert  into #__easysdi_order_product_status_list (code, translation, name) values('AWAIT','EASYSDI_ORDER_PRODUCT_STATUS_AWAIT', 'AWAIT')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_product_status_list (code, translation, name) values('AVAILABLE','EASYSDI_ORDER_PRODUCT_STATUS_AVAILABLE', 'AVAILABLE')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE #__easysdi_order_type_list (id bigint(2) NOT NULL AUTO_INCREMENT, code varchar(20) NOT NULL, translation varchar(50) NOT NULL, name varchar(50) NOT NULL, description varchar(200), created datetime, updated datetime, primary key (id))";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "insert  into #__easysdi_order_type_list (code, translation, name) values('D','EASYSDI_CMD_FILTER_D', 'D')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_order_type_list (code, translation, name) values('O','EASYSDI_CMD_FILTER_O', 'O')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_basemap_definition add column decimalPrecisionDisplayed int(2) NOT NULL default '3'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		//Add diffusion partner field for product
		$query="ALTER TABLE #__easysdi_product add column `diffusion_partner_id` bigint(20) default '0'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_product_properties_definition ADD column `translation` varchar(50) NOT NULL";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_product_properties_values_definition ADD column `translation` varchar(50) NOT NULL";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	 	//Add product treatment type
		$query="ALTER TABLE #__easysdi_product add column `treatment_type`  BIGINT( 20 ) NOT NULL default '1' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	 	//Add email notification field
		$query="ALTER TABLE #__easysdi_product add column `notification_email` varchar(500) NULL";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add lookup table with treatment values
		$query = "CREATE TABLE #__easysdi_product_treatment_type (
					`id` BIGINT( 20 ) NOT NULL auto_increment ,
					`code` TEXT NOT NULL ,
					`translation` TEXT NULL ,
					`description` TEXT NULL ,
					PRIMARY KEY ( `id` )
					) ";
	 	$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	 	$query="ALTER TABLE `#__easysdi_product`
			  ADD CONSTRAINT `#__easysdi_product_ibfk_1` FOREIGN KEY (`treatment_type`) REFERENCES `#__easysdi_product_treatment_type` (`id`) 
			  ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "INSERT INTO #__easysdi_product_treatment_type (code,translation) values ('MANU' , 'EASYSDI_PRODUCT_TREATMENT_TYPE_MANU'),('AUTO' , 'EASYSDI_PRODUCT_TREATMENT_TYPE_AUTO')";
	 	$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Add user/Pw to product preview
	 	$query="ALTER TABLE #__easysdi_product add column `previewUser` varchar(400) NULL,
	 										   add column `previewPassword` varchar(400) NULL ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	 	//Add field for internal metadata
		$query="ALTER TABLE #__easysdi_product add column `metadata_internal`  TINYINT( 1 ) NOT NULL default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	    //Add field for external metadata
		$query="ALTER TABLE #__easysdi_product add column `metadata_external`  TINYINT( 1 ) NOT NULL default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Insert key config for caddy description length
	 	$query = "insert  into #__easysdi_config (thekey, value) values('CADDY_DESCRIPTION_LENGTH','10')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		 //Add field for product administrator
		$query="ALTER TABLE #__easysdi_product add column `admin_partner_id` bigint(20) default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		 //Add total area field to order 
		$query="ALTER TABLE #__easysdi_order add column `surface` bigint(20) default '0' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	  	//Add send date to order 
		$query="ALTER TABLE #__easysdi_order add column `order_send_date` datetime NOT NULL default '0000-00-00 00:00:00' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	 	// Add config keys fort extjs form pos
		$query="INSERT INTO #__easysdi_config (thekey, value) values('DIV_CONTAINER_FRONTEND','div#maincolumn')";
	 	$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	 	$query="INSERT INTO #__easysdi_config (thekey, value) values('DIV_CONTAINER_BACKEND','div#element-box div.m')";
	 	$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
				
	 	/**
	 	 * Add the support of an easySDI account for authentication on each service subject to be called via proxy.php
	 	 */
	 	$query="ALTER TABLE #__easysdi_basemap_content add column easysdi_account_id bigint(20) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	 	$query="ALTER TABLE #__easysdi_perimeter_definition add column easysdi_account_id bigint(20) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	 	$query="ALTER TABLE #__easysdi_location_definition add column easysdi_account_id bigint(20) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	 	$query="ALTER TABLE #__easysdi_product add column easysdi_account_id bigint(20) ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_product add column metadata_update_date datetime NOT NULL default '0000-00-00 00:00:00' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_perimeter_definition add column name_field_search_name varchar(100) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query="ALTER TABLE #__easysdi_basemap_content add column attribution varchar(100) NOT NULL default '' ";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_perimeter_definition add column allowMultipleSelection tinyint(1) NOT NULL default '1'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_location_definition add column allowMultipleSelection tinyint(1) NOT NULL default '1'";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE #__easysdi_basemap_definition add column dflt_fillcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column dflt_strkcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column dflt_strkwidth varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column select_fillcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column select_strkcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column temp_fillcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE #__easysdi_basemap_definition add column temp_strkcolor varchar(10) NOT NULL default ''";
		$db->setQuery( $query);
		if (!$db->query()) {
			//The table does not exists then create it
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		$query = "insert  into #__easysdi_config (thekey, value) values('ENABLE_FAVORITES','1')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_config (thekey, value) values('FAVORITE_ARTICLE_TOP','')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_config (thekey, value) values('MOD_PERIM_AREA_PRECISION','1')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query = "insert  into #__easysdi_config (thekey, value) values('MOD_PERIM_METERTOKILOMETERLIMIT','1000000')";
		$db->setQuery( $query);
		if (!$db->query())
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	 }

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Shop','option=com_easysdi_shop','Easysdi Shop','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	$mainframe->enqueueMessage("Congratulation shop for EasySdi is installed and ready to be used.
	Enjoy EasySdi Shop!","INFO");

}


?>