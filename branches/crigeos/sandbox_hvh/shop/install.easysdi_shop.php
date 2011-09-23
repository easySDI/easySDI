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

	//Check the CORE installation
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE  `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Shop could not be installed. Please install core component first.","ERROR");
		// Delete component
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_shop'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}

	// Check the CATALOG installation
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_catalog' ";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Catalog component does not exist. Easysdi Shop could not be installed. Please install Catalog component first.","ERROR");
		// Delete component
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_shop'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}

		
	// Gets the component version
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'SHOP'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version= '1.0';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'SHOP', 'com_easysdi_shop', 'com_easysdi_shop', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_shop', 'com_sdi_shop', '".$version."')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$module_id = $db->insertid();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'SHOP_PANEL', 'Shop Panel', 'Shop Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$module_id."', 'com_easysdi_shop/core/view/sub.ctrlpanel.admin.easysdi.html.php', '3')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		//Insert configuration keys
		$query = "INSERT  INTO #__sdi_configuration (guid, code, name, description, created, createdby,  value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_PROXYHOST', 'SHOP_CONFIGURATION_PROXYHOST', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  'index.php?option=com_easysdi_shop&no_html=1&task=proxy', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_ARCHIVE_DELAY', 'SHOP_CONFIGURATION_ARCHIVE_DELAY', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '10', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_HISTORY_DELAY', 'SHOP_CONFIGURATION_HISTORY_DELAY', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '20', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_CADDY_DESC_LENGTH', 'SHOP_CONFIGURATION_CADDY_DESC_LENGTH', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '10', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION', 'SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '2', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT', 'SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '1000000', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_ARTICLE_STEP4', 'SHOP_CONFIGURATION_ARTICLE_STEP4', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_ARTICLE_STEP5', 'SHOP_CONFIGURATION_ARTICLE_STEP5', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '', '".$module_id."'),
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_ARTICLE_TERMS_OF_USE', 'SHOP_CONFIGURATION_ARTICLE_TERMS_OF_USE', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '', '".$module_id."')
				";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	
		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_basemap` (
							`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
							`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
							`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
							`created`  datetime NOT NULL ,
							`updated`  datetime NULL DEFAULT NULL ,
							`createdby`  bigint(20) NOT NULL ,
							`updatedby`  bigint(20) NULL DEFAULT NULL ,
							`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
							`ordering`  bigint(20) NULL DEFAULT NULL ,
							`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
							`checked_out_time`  datetime NULL DEFAULT NULL ,
							`projection`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`unit`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`minresolution`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`maxresolution`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`default`  tinyint(1) NOT NULL DEFAULT 0 ,
							`maxextent`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`restrictedextent`  tinyint(1) NOT NULL DEFAULT 0 ,
							`restrictedscales`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`decimalprecision`  int(2) NOT NULL DEFAULT 2 ,
							`dfltfillcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`dfltstrkcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`dfltstrkwidth`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`selectfillcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`selectstrkcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`tempfillcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							`tempstrkcolor`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
							PRIMARY KEY (`id`),
							UNIQUE INDEX `guid` USING BTREE (`guid`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	
		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_perimeter`  (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						`urlwms`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`minscale`  double NOT NULL ,
						`maxscale`  double NOT NULL ,
						`minresolution`  bigint(20) NOT NULL ,
						`maxresolution`  bigint(20) NOT NULL ,
						`imgformat`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`layername`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`urlwfs`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`featuretype`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldid`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldname`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldarea`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldsearch`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldfilter`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`filterperimeter_id`  bigint(20) NULL DEFAULT NULL ,
						`islocalisation`  tinyint(1) NOT NULL DEFAULT 0 ,
						`maxfeatures`  int(11) NOT NULL DEFAULT '-1' ,
						`multipleselection`  tinyint(1) NOT NULL DEFAULT 0 ,
						`searchbox`  tinyint(1) NOT NULL DEFAULT 0 ,
						`sort`  tinyint(1) NOT NULL DEFAULT 0 ,
						`user`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`password`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`account_id`  bigint(20) NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						INDEX `id` USING BTREE (`id`, `filterperimeter_id`) ,
						INDEX `filterperimeter_id` USING BTREE (`filterperimeter_id`) 
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE `#__sdi_perimeter` ADD CONSTRAINT `#__sdi_perimeter_pfk_1` FOREIGN KEY (`filterperimeter_id`) REFERENCES `#__sdi_perimeter` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}

		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_location`  (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						`urlwfs`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`featuretype`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldid`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldname`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`fieldfilter`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`filterlocation_id`  bigint(20) NULL DEFAULT NULL ,
						`islocalisation`  tinyint(1) NOT NULL DEFAULT 0 ,
						`multipleselection`  tinyint(1) NOT NULL DEFAULT 0 ,
						`maxfeatures`  int(11) NOT NULL DEFAULT '-1' ,
						`searchbox`  tinyint(1) NOT NULL DEFAULT 0 ,
						`sort`  tinyint(1) NOT NULL DEFAULT 0 ,
						`user`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`password`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`account_id`  bigint(20) NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						INDEX `id` USING BTREE (`id`, `filterlocation_id`) ,
						INDEX `filterlocation_id` USING BTREE (`filterlocation_id`) 
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE `#__sdi_location` ADD CONSTRAINT `#__sdi_location_pfk_1` FOREIGN KEY (`filterlocation_id`) REFERENCES `#__sdi_location` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_basemapcontent` (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`basemap_id`  bigint(20) NOT NULL ,
					`url`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`urltype`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`singletile`  tinyint(1) NOT NULL DEFAULT 0 ,
					`maxextent`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`minresolution`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`maxresolution`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`projection`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`unit`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`layers`  varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`imgformat`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`attribution`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`user`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`password`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`account_id`  bigint(20) NULL DEFAULT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					UNIQUE INDEX `code` USING BTREE (`code`),
					FOREIGN KEY (`basemap_id`) REFERENCES `#__sdi_basemap` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					INDEX `fk_bc_basemap` USING BTREE (`basemap_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_list_orderstatus` (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						UNIQUE INDEX `code` USING BTREE (`code`)
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query ="CREATE TABLE IF NOT EXISTS `#__sdi_list_ordertype` (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						UNIQUE INDEX `code` USING BTREE (`code`)
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query ="CREATE TABLE IF NOT EXISTS `#__sdi_list_productstatus` (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						UNIQUE INDEX `code` USING BTREE (`code`)
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query= "CREATE TABLE IF NOT EXISTS `#__sdi_list_treatmenttype` (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						UNIQUE INDEX `code` USING BTREE (`code`)
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query ="CREATE TABLE IF NOT EXISTS `#__sdi_order` (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						`type_id`  bigint(20) NOT NULL ,
						`status_id`  bigint(20) NOT NULL ,
						`user_id`  bigint(20) NOT NULL ,
						`thirdparty_id`  bigint(20) NOT NULL ,
						`buffer`  bigint(20) NOT NULL ,
						`surface`  bigint(20) NULL DEFAULT NULL ,
						`remark`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`response`  datetime NULL DEFAULT NULL ,
						`responsesent`  tinyint(1) NOT NULL DEFAULT 0 ,
						`sent`  datetime NULL DEFAULT NULL ,						
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						UNIQUE INDEX `code` USING BTREE (`code`), 
						FOREIGN KEY (`status_id`) REFERENCES `#__sdi_list_orderstatus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
						FOREIGN KEY (`type_id`) REFERENCES `#__sdi_list_ordertype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
						INDEX `fk_order_status` USING BTREE (`status_id`) ,
						INDEX `fk_order_type` USING BTREE (`type_id`) 
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_order_perimeter`  (
						`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
						`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`created`  datetime NOT NULL ,
						`updated`  datetime NULL DEFAULT NULL ,
						`createdby`  bigint(20) NOT NULL ,
						`updatedby`  bigint(20) NULL DEFAULT NULL ,
						`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						`ordering`  bigint(20) NULL DEFAULT NULL ,
						`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
						`checked_out_time`  datetime NULL DEFAULT NULL ,
						`order_id`  bigint(20) NOT NULL ,
						`perimeter_id`  bigint(20) NOT NULL ,
						`value`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
						`text`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
						PRIMARY KEY (`id`),
						UNIQUE INDEX `guid` USING BTREE (`guid`) ,
						FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
						FOREIGN KEY (`perimeter_id`) REFERENCES `#__sdi_perimeter` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
						INDEX `fk_oper_peri` USING BTREE (`perimeter_id`) ,
						INDEX `fk_oper_order` USING BTREE (`order_id`) 
						)
						ENGINE=InnoDB
						DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		

		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_product`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`objectversion_id`  bigint(20) NOT NULL ,
					`surfacemin`  bigint(20) NOT NULL ,
					`surfacemax`  bigint(20) NOT NULL ,
					`published`  tinyint(1) NOT NULL DEFAULT 0 ,
					`visibility_id`  bigint(20) NOT NULL DEFAULT 0 ,
					`available`  tinyint(1) NULL DEFAULT NULL ,
					`free`  tinyint(1) NOT NULL ,
					`diffusion_id`  bigint(20) NOT NULL ,
					`treatmenttype_id`  bigint(20) NOT NULL ,
					`notification`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewbasemap_id`  bigint(20) NULL DEFAULT NULL ,
					`viewurlwms`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewlayers`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewminresolution`  bigint(20) NULL DEFAULT NULL ,
					`viewmaxresolution`  bigint(20) NULL DEFAULT NULL ,
					`viewprojection`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewunit`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewimgformat`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewuser`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewpassword`  varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`viewaccount_id`  bigint(20) NULL DEFAULT NULL ,					
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					UNIQUE INDEX `code` USING BTREE (`code`),
					FOREIGN KEY (`viewbasemap_id`) REFERENCES `#__sdi_basemap` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					FOREIGN KEY (`treatmenttype_id`) REFERENCES `#__sdi_list_treatmenttype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					FOREIGN KEY (`objectversion_id`) REFERENCES `#__sdi_objectversion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					INDEX `fk_p_treatment` USING BTREE (`treatmenttype_id`) ,
					INDEX `fk_p_basemap` USING BTREE (`viewbasemap_id`) ,
					INDEX `fk_p_version` USING BTREE (`objectversion_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
			$query="CREATE TABLE IF NOT EXISTS `#__sdi_product_perimeter`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`product_id`  bigint(20) NOT NULL ,
					`perimeter_id`  bigint(20) NOT NULL ,
					`buffer`  tinyint(1) NOT NULL DEFAULT 0 ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					FOREIGN KEY (`perimeter_id`) REFERENCES `#__sdi_perimeter` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					FOREIGN KEY (`product_id`) REFERENCES `#__sdi_product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					INDEX `fk_pp_peri` USING BTREE (`perimeter_id`) ,
					INDEX `fk_pp_prod` USING BTREE (`product_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_property`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`mandatory`  tinyint(1) NOT NULL DEFAULT 0 ,
					`published`  tinyint(1) NOT NULL DEFAULT 0 ,
					`account_id`  bigint(20) NULL DEFAULT NULL ,
					`type_id`  bigint(20) NULL DEFAULT NULL ,
					`type`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					UNIQUE INDEX `code` USING BTREE (`code`)
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_propertyvalue`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`property_id`  bigint(20) NOT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					FOREIGN KEY (`property_id`) REFERENCES `#__sdi_property` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					INDEX `fk_pv_property` USING BTREE (`property_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_product_property`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`product_id`  bigint(20) NOT NULL ,
					`propertyvalue_id`  bigint(20) NOT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					FOREIGN KEY (`product_id`) REFERENCES `#__sdi_product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					FOREIGN KEY (`propertyvalue_id`) REFERENCES `#__sdi_propertyvalue` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					INDEX `fk_ppv_value` USING BTREE (`propertyvalue_id`) ,
					INDEX `fk_ppv_prod` USING BTREE (`product_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
		";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_order_product`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`order_id`  bigint(20) NOT NULL ,
					`product_id`  bigint(20) NOT NULL ,
					`status_id`  bigint(20) NOT NULL ,
					`remark`  varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`price`  decimal(10,0) NULL DEFAULT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					FOREIGN KEY (`order_id`) REFERENCES `#__sdi_order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					FOREIGN KEY (`product_id`) REFERENCES `#__sdi_product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					FOREIGN KEY (`status_id`) REFERENCES `#__sdi_list_productstatus` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
					INDEX `fk_op_prod` USING BTREE (`product_id`) ,
					INDEX `fk_op_order` USING BTREE (`order_id`) ,
					INDEX `fk_op_status` USING BTREE (`status_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
		";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_order_property`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NULL DEFAULT NULL ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`orderproduct_id`  bigint(20) NOT NULL ,
					`property_id`  bigint(20) NOT NULL ,
					`propertyvalue_id`  bigint(20) NULL DEFAULT NULL ,
					`propertyvalue`  varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `guid` USING BTREE (`guid`) ,
					FOREIGN KEY (`orderproduct_id`) REFERENCES `#__sdi_order_product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					FOREIGN KEY (`property_id`) REFERENCES `#__sdi_property` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					FOREIGN KEY (`propertyvalue_id`) REFERENCES `#__sdi_propertyvalue` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
					INDEX `fk_orp_op` USING BTREE (`orderproduct_id`) ,
					INDEX `fk_orp_prop` USING BTREE (`property_id`) ,
					INDEX `fk_orp_val` USING BTREE (`propertyvalue_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
		";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_orderproduct_file`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`orderproduct_id`  bigint(20) NOT NULL ,
					`data`  longblob NULL DEFAULT NULL ,
					`filename`  varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					PRIMARY KEY (`id`),
					FOREIGN KEY (`orderproduct_id`) REFERENCES `#__sdi_order_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					INDEX `fk_order_file` USING BTREE (`orderproduct_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
		";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_product_file`  (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`product_id`  bigint(20) NOT NULL ,
					`filename`  varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`data`  longblob NULL DEFAULT NULL ,
					`type`  varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`size`  int(20) NULL DEFAULT NULL ,	
					PRIMARY KEY (`id`),
					FOREIGN KEY (`product_id`) REFERENCES `#__sdi_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
					INDEX `fk_product_file` USING BTREE (`product_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
		";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="INSERT INTO `#__sdi_list_orderstatus` ( `guid`, `code`, `name`, `description`, `created`, `updated`, `createdby`, `updatedby`, `label`, `ordering`) VALUES 
						( '".helper_easysdi::getUniqueId()."', 'ARCHIVED', 'Archived', 'Archived', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_ARCHIVED', 6),
						( '".helper_easysdi::getUniqueId()."', 'HISTORIZED', 'Historized', 'Historized', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_HISTORIZED', 7),
						( '".helper_easysdi::getUniqueId()."', 'FINISH', 'Finish', 'Finish', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_FINISH', 5),
						( '".helper_easysdi::getUniqueId()."', 'AWAIT', 'Await', 'Await', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_AWAIT', 4),
						( '".helper_easysdi::getUniqueId()."', 'PROGRESS', 'Progress', 'Progress', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_PROGRESS', 3),
						( '".helper_easysdi::getUniqueId()."', 'SENT', 'Sent', 'Sent', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_SENT', 2),
						( '".helper_easysdi::getUniqueId()."', 'SAVED', 'Saved', 'Saved', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_STATUS_SAVED', 1)
						";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="INSERT INTO `#__sdi_list_ordertype` ( `guid`, `code`, `name`, `description`, `created`, `updated`, `createdby`, `updatedby`, `label`, `ordering`) VALUES 
						( '".helper_easysdi::getUniqueId()."', 'O', 'Order', 'Order', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_TYPE_O', 1),
						( '".helper_easysdi::getUniqueId()."', 'D', 'Devis', 'Devis', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_ORDER_TYPE_D', 2)
						";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="INSERT INTO `#__sdi_list_productstatus` ( `guid`, `code`, `name`, `description`, `created`, `updated`, `createdby`, `updatedby`, `label`, `ordering`) VALUES 
						( '".helper_easysdi::getUniqueId()."', 'AVAILABLE', 'Available', 'Available', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_PRODUCT_STATUS_AVAILABLE', 1),
						( '".helper_easysdi::getUniqueId()."', 'AWAIT', 'Await', 'Await', '".date('Y-m-d H:i:s')."', NULL, ".$user_id.", NULL, 'SHOP_PRODUCT_STATUS_AWAIT', 2)
						";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="INSERT INTO `#__sdi_list_treatmenttype` ( `guid`, `code`, `name`, `description`, `created`, `updated`, `createdby`, `updatedby`, `label`, `ordering`) VALUES 
						( '".helper_easysdi::getUniqueId()."', 'MANU', 'Manuel', 'Traitement manuel', '".date('Y-m-d H:i:s')."', NULL, $user_id, NULL, 'SHOP_TREATMENT_MANU', 1),
						( '".helper_easysdi::getUniqueId()."', 'AUTO', 'Automatique', 'Traitment automatique', '".date('Y-m-d H:i:s')."', NULL, $user_id, NULL, 'SHOP_TREATMENT_AUTO', 2)
						";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query = "CREATE TABLE IF NOT EXISTS `#__sdi_favorite` (
					`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
					`guid`  varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
					`code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
					`description`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`created`  datetime NOT NULL ,
					`createdby`  bigint(20) NOT NULL ,
					`updated`  datetime NULL DEFAULT NULL ,
					`updatedby`  bigint(20) NULL DEFAULT NULL ,
					`label`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
					`ordering`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out`  bigint(20) NOT NULL DEFAULT 0 ,
					`checked_out_time`  datetime NULL DEFAULT NULL ,
					`metadata_id`  bigint(20) NOT NULL ,
					`account_id`  bigint(20) NOT NULL ,
					`enable_notification`  tinyint(1) NULL DEFAULT NULL ,
					PRIMARY KEY (`id`),
					INDEX `favorite_ibfk_1` USING BTREE (`metadata_id`) ,
					INDEX `favorite_ibfk_2` USING BTREE (`account_id`) 
					)
					ENGINE=InnoDB
					DEFAULT CHARACTER SET=utf8
					";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	 }
	if($version == "1.0")
	{
		// Update component version
		$version="2.0.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='SHOP'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "2.0.0"){
		//Base Map
		$query="ALTER TABLE  `#__sdi_basemap` ADD minresol varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE  `#__sdi_basemap` ADD maxresol varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE  `#__sdi_basemap` ADD restrictedresol varchar (500)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Base map content
		$query="ALTER TABLE  `#__sdi_basemapcontent` ADD matrixset varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_basemapcontent` ADD matrixids varchar (1000)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_basemapcontent` ADD style varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		//Product preview
		$query="ALTER TABLE  `#__sdi_product` ADD viewextent varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD viewurltype varchar (10)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD viewmatrixset varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD viewmatrix varchar (1000)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD viewstyle varchar (100)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE  `#__sdi_product` ADD pathfile varchar (500)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_accessibility` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20),
				  `name` varchar(50),
				  `description` varchar(100),
				  `created` datetime,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20),
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="INSERT INTO `#__sdi_list_accessibility` (`guid`, `code`, `name`, `label`, `description`, `created`, `createdby`) VALUES
					('".helper_easysdi::getUniqueId()."', 'all', 'all', 'SHOP_PRODUCT_ACCESSIBILITY_PUBLIC', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ofRoot', 'ofRoot', 'SHOP_PRODUCT_ACCESSIBILITY_SUPPLIER', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."'),
					('".helper_easysdi::getUniqueId()."', 'ofManager', 'ofManager', 'SHOP_PRODUCT_ACCESSIBILITY_MANAGER', NULL, '".date('Y-m-d H:i:s')."', '".$user_id."')";
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD viewaccessibility_id bigint (20)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE `#__sdi_product` ADD CONSTRAINT `#__sdi_product_pfk_va` FOREIGN KEY (`viewaccessibility_id`) REFERENCES `#__sdi_list_accessibility` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE  `#__sdi_product` ADD loadaccessibility_id bigint (20)";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		$query="ALTER TABLE `#__sdi_product` ADD CONSTRAINT `#__sdi_product_pfk_dl` FOREIGN KEY (`loadaccessibility_id`) REFERENCES `#__sdi_list_accessibility` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="CREATE TABLE IF NOT EXISTS `#__sdi_product_account` (
				  `id` bigint(20) NOT NULL AUTO_INCREMENT,
				  `guid` varchar(36) NOT NULL,
				  `code` varchar(20) NOT NULL,
				  `name` varchar(50) ,
				  `description` varchar(100),
				  `created` datetime NOT NULL,
				  `updated` datetime,
				  `createdby` bigint(20),
				  `updatedby` bigint(20),
				  `label` varchar(50),
				  `ordering` bigint(20),
				  `account_id` bigint(20) NOT NULL,
				  `product_id` bigint(20) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `guid` (`guid`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
		$query="ALTER TABLE `#__sdi_product_account` ADD CONSTRAINT `#__sdi_product_pfk_acc` FOREIGN KEY (`account_id`) REFERENCES `#__sdi_account` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="ALTER TABLE `#__sdi_product_account` ADD CONSTRAINT `#__sdi_product_pfk_pd` FOREIGN KEY (`product_id`) REFERENCES `#__sdi_product` (`id`);";
		$db->setQuery( $query);	
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
		}
		
		$query="SELECT id FROM #__sdi_list_module WHERE code = 'SHOP'";
		$db->setQuery( $query);		
		$module_id = $db->loadResult();
		$query = "INSERT  INTO #__sdi_configuration (guid, code, name, description, created, createdby,  value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'SHOP_CONFIGURATION_MAX_FILE_SIZE', 'SHOP_CONFIGURATION_MAX_FILE_SIZE', 'SHOP', '".date('Y-m-d H:i:s')."', '".$user_id."',  '32', '".$module_id."')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
			
		$version="2.0.1";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='SHOP'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	 
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_shop' ";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
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