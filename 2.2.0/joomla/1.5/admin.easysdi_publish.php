<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

 /**
 * This is the main class (controler) for the admin part of EasySDI-Publish.
 * It switches the tasks and dispach the request to the according sub-class
 */

//Call inside Joomla or die.
defined('_JEXEC') or die('Restricted access');

//Import required Joomla buit-in controls
jimport("joomla.html.pagination");
jimport("joomla.html.pane");
jimport("joomla.database.table");

//Import required Joomla helper classes
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'plugin.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'section.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

//Switch tasks and dispatch the request to the sub-class
$task = JRequest::getVar('task');

//Reference on the mainframe, for redirecting or error output.
global $mainframe;

//Import css
//JHTML::_('stylesheet', 'easysdi_shop.css', 'administrator/components/com_easysdi_shop/templates/css/');
JHTML::_('stylesheet', 'easysdi.css', 'templates/easysdi/css/');
JHTML::_('stylesheet', 'common_easysdi_admin.css', 'administrator/components/com_easysdi_core/templates/css/');

//Load Publish needed classes
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrpanel.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'publish.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'publish.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'settings.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'upload.admin.publish.class.php');

$language=&JFactory::getLanguage();
$language->load('com_easysdi_publish');
$language->load('com_easysdi_core');

switch($task){

  case "cancelConfig":
		$mainframe->redirect("index.php?option=com_easysdi_core");
	break;

	case "editGlobalSettings":
		TOOLBAR_publish::_EDIT();
		ADMIN_publish::editGlobalSettings($option);
		break;
	
	case "deleteDiffusor":
		$diffusorId = JRequest::getVar("diffusor_id",0);
		$diffusorName = JRequest::getVar("diffusion_server_name");
		$error = ADMIN_publish::deleteDiffusor($diffusorId, $option);
		$tabIndex = JRequest::getVar("tabIndex",0);
		if($error == "")
			$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_DELETE_DIFFUSOR_SUCCESS").": ".$diffusorName	,"INFO");
		else
		  $mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_DELETE_DIFFUSOR_ERROR").": ".$diffusorName." reason:".$error	,"INFO");
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings&tabIndex=".$tabIndex );
		break;
	case "deleteCrs":
		$crsId = JRequest::getVar("crs_id",0);
		ADMIN_publish::deleteCrs($crsId, $option);
		$tabIndex = JRequest::getVar("tabIndex",0);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings&tabIndex=".$tabIndex );
		break;
	case "deleteScript":
		$scriptId = JRequest::getVar("man_script_id",0);
		$scriptName = JRequest::getVar("publish_script_name");
		$success = ADMIN_publish::deleteScript($scriptId, $option);
		$tabIndex = JRequest::getVar("tabIndex",0);
		if($success)
			$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_DELETE_SCRIPT_SUCCESS").": ".$scriptName	,"INFO");
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings&tabIndex=".$tabIndex );
		break;
	
	case "scriptManagement":
		//TOOLBAR_publish::_EDITGLOBALSETTINGS();	
		//ADMIN_publish::editGlobalSettings($option);
		break;
		
	case "saveConfig":
		//upload the file
		$return = null;
		//if there is a file to upload
		if (isset($_FILES['Filedata']) || is_uploaded_file($_FILES['Filedata']['tmp_name'])){
			$return = ADMIN_upload::uploadScript();
			if($return["status"] != 1){
				//$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_UPLOAD_ERROR"),"INFO");
			}
		}
		
		if (!isset($return["name"]))
			$return["name"] = "";	
		ADMIN_publish::saveConfig($option, $return["name"]);
		
		
		$tabIndex = JRequest::getVar("tabIndex",0);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings&tabIndex=".$tabIndex );
		break;
		
	case "cancelConfig":
		$mainframe->redirect("index.php" );
		break;
		
	case "uploadScript":
		$return = ADMIN_upload::uploadScript();
		//Array ( [status] => 1 [name] => Hiver.jpg [hash] => b44a59383b3123a747d139bd0e71d2df [src] => C:\wamp\www\Joomla\administrator\components\com_easysdi_publish\uploads\Hiver.jpg 
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings&tabIndex=3&status=".$return["status"]."&filename=".$return["name"]."&src=".base64_encode($return["src"]));
		break;
	default:
		//$mainframe->enqueueMessage($task,"INFO");
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=editGlobalSettings" );		
		break;
}

?>