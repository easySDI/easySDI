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

	 
	/**
	 * Menu creation
	 */
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
	
	$mainframe->enqueueMessage("Congratulation Monitor for EasySDI is installed and ready to be used.	Enjoy EasySdi Monitor!
	 Do not forget to setup the MONITOR_URL key into component->EasySDI->configuration. For example set it to http://localhost:8083/Monitor (depending on your servlet container location)","INFO");

}


?>