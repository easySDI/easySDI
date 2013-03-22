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
			  `createdby` bigint(20),
			  `updatedby` bigint(20),
			  `label` varchar(50),
			  `ordering` bigint(20),
			  `value` varchar(100),
			  `currentversion` varchar(20),
			  `lastversion` varchar(20),
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `guid` (`guid`),
			  UNIQUE KEY `code` (`code`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
	 		
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	
	$version = '0';
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'PROXY'";
	$db->setQuery( $query);
	$version = $db->loadResult();
				
	//When there is no DB version, then we create the full db
	if (!$version)
	{	
		$version = "0.1";
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'PROXY', 'com_easysdi_proxy', 'com_easysdi_proxy', '".date('Y-m-d H:i:s')."', ".$user_id.", 'com_easysdi_proxy', 'com_easysdi_proxy', '".$version."')";
		$db->setQuery( $query);
		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$query = "SELECT id FROM `#__sdi_list_module` where `code` = 'PROXY'";
		$db->setQuery( $query);
		$id = $db->loadResult();
				
		/**
		 * Insert value for PROXY_CONFIG in configuration table
		 */
		$query = "insert into #__sdi_configuration (guid, code, name, description, created, createdby, label, value, module_id) 
											values('".helper_easysdi::getUniqueId()."', 'PROXY_CONFIG', 'PROXY_CONFIG', 'PROXY', '".date('Y-m-d H:i:s')."', '".$user_id."', null, '/home/configs/proxy/WEB-INF/conf/config.xml', '".$id."')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'PROXY_PANEL', 'Proxy Panel', 'Proxy Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$id."', 'com_easysdi_proxy/core/view/sub.ctrlpanel.admin.easysdi.html.php', '4')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		
		// Update component version
		$version="1.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PROXY'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		
	}
	if($version == "1.0")
	{
		// Update component version
		$version="2.1.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PROXY'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "2.1.0")
	{
		// Update component version
		$version="2.1.1";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PROXY'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "2.1.1")
	{
		// Update component version
		$version="2.1.3";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PROXY'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	if($version == "2.1.3")
	{
		// Update component version
		$version="2.1.4";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='PROXY'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_proxy' ";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
			
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Proxy','option=com_easysdi_proxy&task=showConfigList','Easysdi Proxy','com_easysdi_proxy','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation proxy manager for EasySdi is installed and ready to be used. Enjoy EasySdi Proxy!","INFO");
	return true;
}


?>