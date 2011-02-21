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
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Monitor could not be installed. Please install core component first.","ERROR");
		// Delete component
		$db =& JFactory::getDBO();
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor'";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		return false;
	}
	
	// Gets the component version
	$query = "SELECT currentversion FROM `#__sdi_list_module` where `code` = 'MONITOR'";
	$db->setQuery( $query);
	$version = $db->loadResult();
	if (!$version)
	{
		$version= '0.1';
		$query="INSERT INTO #__sdi_list_module (guid, code, name, description, created, createdby, label, value, currentversion) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'MONITOR', 'com_easysdi_monitor', 'com_easysdi_monitor', '".date('Y-m-d H:i:s')."', '".$user_id."', 'com_sdi_monitor', 'com_sdi_monitor', '".$version."')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
		$module_id = $db->insertid();
		
		$query="INSERT INTO #__sdi_module_panel (guid, code, name, description, created, createdby,module_id, view_path,ordering) 
										VALUES ('".helper_easysdi::getUniqueId()."', 'MONITOR_PANEL', 'Monitor Panel', 'Monitor Panel', '".date('Y-m-d H:i:s')."', '".$user_id."', '".$module_id."', 'com_easysdi_monitor/views/main/sub.ctrlpanel.admin.easysdi.html.php', '4')";
		$db->setQuery( $query);		
		if (!$db->query()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	        
		//Insert configuration keys
		$query = "INSERT  INTO #__sdi_configuration (guid, code, name, description, created, createdby,  value, module_id) VALUES
				('".helper_easysdi::getUniqueId()."', 'MONITOR_URL', 'MONITOR_URL', 'MONITOR', '".date('Y-m-d H:i:s')."', '".$user_id."',  'http://admin:admin@localhost:8080/Monitor', '".$module_id."')";
		$db->setQuery( $query);
		if (!$db->query())
		{	
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			return false;
		}
	}
	if($version == "0.1")
	{
		// Update component version
		$version="2.0.0";
		$query="UPDATE #__sdi_list_module SET currentversion ='".$version."' WHERE code='MONITOR'"; 
		$db->setQuery( $query);	
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}
	
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
	$db->setQuery($query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}

	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('EasySDI - Monitor','option=com_easysdi_monitor&view=main','Easysdi Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	$mainframe->enqueueMessage("Congratulation Monitor for EasySDI is installed and ready to be used. Enjoy EasySdi Monitor!
	 Do not forget to check/change the MONITOR_URL key depending on your servlet container location.","INFO");

	 /*
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	if ($id)
	{
	}
	else
	{
		$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
		return false;
	}

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
			$db->setQuery( $query);
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");	
				return false;	
			}
	
	//Entry in the EasySDI menu
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
	values($id,'Monitor','','option=com_easysdi_monitor&view=main','Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	
	$query =  "insert into #__components (name,link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Monitor','option=com_easysdi_monitor&view=main','Monitor','com_easysdi_monitor','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query()) 
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;		
	}
	
	$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'" ;		
	$db->setQuery( $query);
	$id = $db->loadResult();	
	if ($id)
	{
 		$mainframe->enqueueMessage("EASYSDI menu is already existing. Usually this menu is created during the installation of this component. Maybe something has gone wrong during the previous uninstall !","INFO"); 	 	
	}
	else
	{
		//Insert the EasySdi Main Menu
		$query = "DELETE FROM #__components where `option`= 'com_easysdi_monitor' ";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		
		$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
			values('EasySDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','js/ThemeOffice/component.png','')";
		$db->setQuery( $query);
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
		}
		$query =  "SELECT ID FROM #__components WHERE name ='EasySDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	
	}
	
        */

}


?>