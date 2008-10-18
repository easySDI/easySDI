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

	/**
	 * Creates the database structure
	 */
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
	if (strlen($version)==0){$version ='0';}
	$mainframe->enqueueMessage("Db version : $version","INFO");			
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
			$query="CREATE TABLE `#__easysdi_version` (
  `component` varchar(100) NOT NULL default '',
  `id` bigint(20) NOT NULL auto_increment,
  `version` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
)"; 		
			$db->setQuery( $query);

			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
	$query="INSERT INTO #__easysdi_version (id,component,version) VALUES
(null, 'com_easyssdi_shop', '0.9')";

		$db->setQuery( $query);

		if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

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

	$query="CREATE TABLE `#__easysdi_config` (
  `id` bigint(20) NOT NULL auto_increment,
  `thekey` varchar(100) NOT NULL default '',
  `value` varchar(100) NOT NULL default '',
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

	$query="CREATE TABLE `#__easysdi_partner_extension` (
  `ext_id` bigint(20) NOT NULL auto_increment,
  `tab_id` bigint(20) NOT NULL,
  `order_number` bigint(20) NOT NULL,
  `code` varchar(4000) NOT NULL,
  `action` varchar(4000) NOT NULL,
  `tab_location` varchar(3000) NOT NULL,
  PRIMARY KEY  (`ext_id`)
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
  `wms_url` varchar(4000) NOT NULL default '',
  `feature_type_name` varchar(4000) NOT NULL default '',
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

	
}


	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	
	if ($id){
		
	}else{
	//Insert the EasySdi Main Menu		
	$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Easy SDI','option=com_easysdi_proxy','option=com_easysdi_proxy','Easysdi main menu','com_easysdi_proxy','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	
	}
	
			
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'Liste les produits','','option=com_easysdi_shop&task=listProduct','Liste les produits','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'Périmètres','','option=com_easysdi_shop&task=listPerimeter','Périmètres','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'Propriétés de la commande','','option=com_easysdi_shop&task=listProperties','Propriétés de la commande','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'basemap configurator','','option=com_easysdi_shop&task=listBasemap','basemap configurator','com_easysdi_shop','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	
	
$mainframe->enqueueMessage("Congratulation shop for EasySdi is installed and ready to be used. 
Enjoy EasySdi!","INFO");

}


?>