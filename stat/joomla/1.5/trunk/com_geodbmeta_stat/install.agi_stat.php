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


function com_install(){

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
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Proxy could not be installed. Please install core component first.","ERROR");
		/**
		 * Delete components
		 */
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_proxy'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;		
	}
	
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

	$user =& JFactory::getUser();
	$user_id = $user->get('id');
	
	$query="CREATE TABLE IF NOT EXISTS `#__sdi_list_module` (
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `guid` varchar(36) NOT NULL,
			  `code` varchar(20),
			  `name` varchar(50) NOT NULL,
			  `description` varchar(100),
			  `created` datetime NOT NULL,
			  `updated` datetime,
			  `createdby` bigint(20) NOT NULL,
			  `updatedby` bigint(20),
			  `label` varchar(50),
			  `ordering` bigint(20),
			  `value` varchar(100),
			  `currentversion` bigint(20),
			  `lastversion` bigint(20),
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `guid` (`guid`),
			  UNIQUE KEY `code` (`code`)
			)";
	 		
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	
	$version = '0';
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'AGI_STAT'";
	$db->setQuery( $query);
	$version = $db->loadResult();
				
	if (!$version)
	{	
		$version= '0.1';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'AGI_STAT', 'com_agi_stat', 'com_agi_stat', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_agi_stat', 'com_agi_stat', '".$version."')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		$query = "SELECT id FROM `#__sdi_list_module` where `code` = 'AGI_STAT'";
		$db->setQuery( $query);
		$id = $db->loadResult();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'AGI_STAT_PANEL', 'AGI Stat Panel', 'AGI Stat Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$id."', 'com_agi_stat/core/view/sub.ctrlpanel.admin.agi.html.php', '5')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}

		$query="CREATE  TABLE IF NOT EXISTS `#__agi_stat_attribute` (
				  `id` BIGINT NOT NULL AUTO_INCREMENT ,
				  `guid` VARCHAR(36) NOT NULL ,
				  `created` DATETIME NOT NULL ,
				  `updated` DATETIME NOT NULL ,
				  `attribute_name` VARCHAR(100) NOT NULL ,
				  `date` DATE NOT NULL ,
				  `count` INT NOT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = InnoDB;";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}

		$query="CREATE  TABLE IF NOT EXISTS `#__agi_stat_metadata` (
				  `id` BIGINT NOT NULL AUTO_INCREMENT ,
				  `guid` VARCHAR(36) NOT NULL ,
				  `created` DATETIME NOT NULL ,
				  `updated` DATETIME NOT NULL ,
				  `metadata_id` VARCHAR(36) NOT NULL ,
				  `date` DATE NOT NULL ,
				  `count` INT NOT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = InnoDB;";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}


		$query="CREATE  TABLE IF NOT EXISTS `#__agi_stat_performance` (
				  `id` BIGINT NOT NULL AUTO_INCREMENT ,
				  `guid` VARCHAR(36) NOT NULL ,
				  `created` DATETIME NOT NULL ,
				  `updated` DATETIME NOT NULL ,
				  `service` VARCHAR(100) NOT NULL ,
				  `operation` VARCHAR(50) NOT NULL ,
				  `date` DATE NOT NULL ,
				  `min_time` FLOAT NOT NULL ,
				  `max_time` FLOAT NOT NULL ,
				  `average_time` FLOAT NOT NULL ,
				  PRIMARY KEY (`id`) )
				ENGINE = InnoDB;";
		$db->setQuery( $query);
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}
	
	$query = "DELETE FROM #__components where `option`= 'com_agi_stat' ";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
			
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('AGI Statistic','option=com_agi_stat&task=statistic','AGI Statistic','com_agi_stat','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation AGI Statistic component for EasySdi is installed and ready to be used.","INFO");
	return true;
}


?>