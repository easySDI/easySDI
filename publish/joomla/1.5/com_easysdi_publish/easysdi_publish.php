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

defined('_JEXEC') or die('Restricted access');

jimport("joomla.html.pagination");
jimport("joomla.html.pane");

//require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
require_once( JPATH_COMPONENT.DS.'lang'.DS.'lang.php' );
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

//External classes
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

//Site & Admin classes
require_once(JPATH_COMPONENT.DS.'core'.DS.'publish.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'publish.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'finddata.publish.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'buildlayer.publish.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'publish.classes.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'helper.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'publish.epsg.html.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');

//browser detection
include('browser_detection.php');
require_once(JPATH_COMPONENT.DS.'browser_detection.php');


//Localization files
$language=&JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_publish');

//get an instance of the currently logged in joomla user
$joomlaUser = JFactory::getUser();
$database =& JFactory::getDBO();

//Check for the rights of the connected user
$database->setQuery( "SELECT r.code FROM #__sdi_list_role r, #__sdi_account p,
										 #__sdi_actor a WHERE r.id = a.role_id AND a.account_id = p.id AND p.user_id=".$joomlaUser->id );
$rows = $database->loadObjectList() ;
$userRights = array("GEOSERVICE_DATA_MANA" => false, "GEOSERVICE_MANAGER" => false);
foreach($rows as $elem)
{
	if($elem->code == "GEOSERVICE_DATA_MANA")
		$userRights['GEOSERVICE_DATA_MANA'] = true;
	if($elem->code == "GEOSERVICE_MANAGER")
		$userRights['GEOSERVICE_MANAGER'] = true;
}

//restrict access if user doesn't have rights
if(!$userRights["GEOSERVICE_DATA_MANA"] && !$userRights["GEOSERVICE_MANAGER"])
{
	echo JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER");
	return;
}

//Global js script files
JHTML::script('validation.js', 'components/com_easysdi_publish/js/');
JHTML::script('wps.js', 'components/com_easysdi_publish/js/');
//JHTML::script('dwProgressBar.js', 'components/com_easysdi_publish/js/');
//if(JRequest::getVar('tabIndex') != 2)
//	JHTML::script('OpenLayers.js', 'http://www.openlayers.org/api/');
//JHTML::script('proj4js.js', './administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/');

//see template
//JHTML::stylesheet('dwPbar.css','components/com_easysdi_publish/css/');

//read requested URL
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');

//the WPS address
$wpsAddress = config_easysdi::getValue("WPS_PUBLISHER");
		
//Get the server list from the WPS
$wpsConfig = $wpsAddress."/services/config";
//Get the config
$query = "select * from #__sdi_publish_config";
$database->setQuery( $query);
$config = $database->loadObjectList();
if($database->getErrorNum()){
		$mainframe->enqueueMessage(JText::_("ERROR_EXECUTING_QUERY"),"ERROR");
		return;
}


/**
 * Handle view publish
 */
switch($task){
	
	case "proxy":
		SITE_proxy::fetch($_GET['proxy_url'],true,getCurrentUser($wpsConfig));
	break;
	
	case "gettingStarted":
	  
		SITE_publish::gettingStarted($userRights, $wpsAddress, getCurrentUser($wpsConfig));
		break;
	
	case "createFeatureSource":
		SITE_publish::createFeatureSource($wpsAddress, getCurrentUser($wpsConfig), $config[0]);
		break;
	
	case "editFeatureSource":
		SITE_publish::editFeatureSource($wpsAddress, getCurrentUser($wpsConfig), $config[0]);
		break;
		
	case "deleteFeatureSource":
		$database->setQuery( "SELECT id FROM #__sdi_publish_featuresource where featureGUID='".JRequest::getVar('featureSourceGuid')."'");
		$id = $database->loadResult();
		SITE_publish::deleteFeatureSource($id);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=0");		
		break;
			
	case "saveFeatureSource":
		SITE_publish::saveFeatureSource();
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted");		
		break;
	case "createLayer":
		SITE_publish::createLayer($wpsAddress, getCurrentUser($wpsConfig), $config[0]);
		break;
	
	case "editLayer":
		SITE_publish::editLayer($wpsAddress, getCurrentUser($wpsConfig), $config[0]);
		break;
		
	case "deleteLayer":
		$database->setQuery( "SELECT id FROM #__sdi_publish_layer where layerGuid='".JRequest::getVar('layerGuid')."'");
		$id = $database->loadResult();
		SITE_publish::deleteLayer($id);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1");		
		break;
	
	case "saveLayer":
		$isCopy = JRequest::getVar('copyLayer',0);
		SITE_publish::saveLayer($isCopy);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1");		
		break;
	
	case "showFsStats":
		SITE_publish::showFsStats($wpsAddress);
		break;
	
	case "showEpsgList":
		SITE_publish::showEpsgList();
		break;
	
	case "showFormatList":
		SITE_publish::showFormatList();
		break;
	
	default :	
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted");		
		//$mainframe->enqueueMessage(JText::_("EASYSDI_ACTION_NOT_ALLOWED"), "INFO");
		break;
}


function getCurrentUser($wpsConfig){
	global $mainframe;
	$joomlaUser = JFactory::getUser();
	$database =& JFactory::getDBO();
	//Retrieve diffusion server list from wps
	$url = $wpsConfig."?operation=listPublicationServers";
	$doc = SITE_proxy::fetch($url, false);
	$xml = simplexml_load_string($doc);
	
	if($xml == null){                    
		$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_ERROR_CONNECTING_TO_WPS"), "ERROR");
		$mainframe->redirect("index.php");
		return;
	}

	//retrieve general info of the logged user and config
	$query = "select u.easysdi_user_id, u.publish_user_max_layers, u.publish_user_total_space, u.publish_user_diff_server_id, u.publish_user_diff_server_id from #__sdi_publish_user u, #__sdi_account p where u.easysdi_user_id=p.id AND p.user_id=".$joomlaUser->id;
	$database->setQuery($query);
	
	$currentUser = $database->loadObjectList();
		
	//get the diffusor of the current user and update the currentUser object
	$sid = $currentUser[0]->publish_user_diff_server_id;
	//echo "<pre>xml:";  print_r($xml);  echo "</pre>";
	//exit;
	$diffusor = $xml->xpath("//server[@id=$sid]");
	$diffusor = $diffusor[0];
	$currentUser[0]->diffusion_server_name = (string)$diffusor->name;
	$currentUser[0]->diffusion_username = (string)$diffusor->username;
	$currentUser[0]->diffusion_password = (string)$diffusor->password;
	
	//User exist in easysdi and has rights, check if he has been configurated in EasySDI Publish
	if(count($currentUser)<1)
	{                    
		$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_USER_DOESNT_EXIST"), "ERROR");
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=showError");
		return;
	}
		
	return $currentUser[0];
}

/*
function testConnection($wpsConfig){

  $url = $wpsConfig."?operation=listPublicationServers";
		
	//Test connection to the server
	preg_match("~([a-z]*://)?([^:^/]*)(:([0-9]{1,5}))?(/.*)?~i", $url, $parts);
	$protocol = $parts[1];
	$server = $parts[2];
	$port = $parts[4];
	$path = $parts[5];
	
	if ($port == "") {
	     if (strtolower($protocol) == "https://") {
	          $port = "443";
	     } else {
	          $port = "80";
	     }
	}
	if ($path == "") { $path = "/"; }	
	if (!$sock = fsockopen(((strtolower($protocol) == "https://")?"ssl://":"").$server, $port, $errno, $errstr, 5))
	{
		   return false;
	}else{
	     return true;
  }
}
*/


 ?>	