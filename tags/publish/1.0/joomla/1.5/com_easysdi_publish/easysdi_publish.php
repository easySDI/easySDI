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

require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
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
$database->setQuery( "SELECT r.role_code FROM #__easysdi_community_role r, #__easysdi_community_partner p,
										 #__easysdi_community_actor a WHERE r.role_id = a.role_id AND a.partner_id = p.partner_id AND p.user_id=".$joomlaUser->id );
$rows = $database->loadObjectList() ;
$userRights = array("GEOSERVICE_DATA_MANA" => false, "GEOSERVICE_MANAGER" => false);
foreach($rows as $elem)
{
	if($elem->role_code == "GEOSERVICE_DATA_MANA")
		$userRights['GEOSERVICE_DATA_MANA'] = true;
	if($elem->role_code == "GEOSERVICE_MANAGER")
		$userRights['GEOSERVICE_MANAGER'] = true;
}

//restrict to firefox for now
/*
$a_browser_data = browser_detection('full');
if ( $a_browser_data[0] !== 'moz' )
{
	echo "Your browser is not enough Firefoxy to use EasySDI Publish, please use Firefox or be patient...";
	return;
}
*/

//restrict access if user doesn't have rights
if(!$userRights["GEOSERVICE_DATA_MANA"] && !$userRights["GEOSERVICE_MANAGER"])
{
	echo JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER");
	return;
}

//Global js script files
JHTML::script('validation.js', 'components/com_easysdi_publish/js/');
JHTML::script('wps.js', 'components/com_easysdi_publish/js/');
JHTML::script('dwProgressBar.js', 'components/com_easysdi_publish/js/');
//JHTML::script('OpenStreetMap.js', 'http://www.openstreetmap.org/openlayers/');
if(JRequest::getVar('tabIndex') != 2)
	JHTML::script('OpenLayers.js', 'http://www.openlayers.org/api/');
JHTML::script('proj4js.js', './administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/');

//see template
//JHTML::stylesheet('openlayers.css','components/com_easysdi_publish/css/');
//JHTML::stylesheet('publish.css','components/com_easysdi_publish/css/');
//JHTML::stylesheet('fancy.css','components/com_easysdi_publish/css/');
JHTML::stylesheet('dwPbar.css','components/com_easysdi_publish/css/');

//read requested URL
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');

//the WPS address
$wpsAddress = config_easysdi::getValue("WPS_PUBLISHER");
		
//Get the server list from the WPS
$wpsConfig = $wpsAddress."/config";

//Get the config
$query = "select * from #__easysdi_publish_config";
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
	//case "manageFavoriteProduct":
	//	$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
	//	break;
	
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
		$database->setQuery( "SELECT id FROM #__easysdi_publish_featuresource where featureGUID='".JRequest::getVar('featureSourceGuid')."'");
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
		$database->setQuery( "SELECT id FROM #__easysdi_publish_layer where layerGuid='".JRequest::getVar('layerGuid')."'");
		$id = $database->loadResult();
		SITE_publish::deleteLayer($id);
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1");		
		break;
	
	case "saveLayer":
		SITE_publish::saveLayer();
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1");		
		break;
	
	case "showFsStats":
		SITE_publish::showFsStats();
		break;
	
	case "showEpsgList":
		SITE_publish::showEpsgList();
		break;
	
	case "showFormatList":
		SITE_publish::showFormatList();
		break;
		
	case "showError":
		break;
	
	default :	
		$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted");		
		//$mainframe->enqueueMessage(JText::_("EASYSDI_ACTION_NOT_ALLOWED"), "INFO");
		break;
}

function browser_info($agent=null) {
  // Declare known browsers to look for
  $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape',
    'konqueror', 'gecko');

  // Clean up agent and build regex that matches phrases for known browsers
  // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
  // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
  $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);
  $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';

  // Find all phrases (or return empty array if none found)
  if (!preg_match_all($pattern, $agent, $matches)) return array();

  // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
  // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
  // in the UA).  That's usually the most correct.
  $i = count($matches['browser'])-1;
  return array($matches['browser'][$i] => $matches['version'][$i]);
}

function getCurrentUser($wpsConfig){
	global $mainframe;
	$joomlaUser = JFactory::getUser();
	$database =& JFactory::getDBO();
	//Retrieve diffusion server list from wps
	$url = $wpsConfig."?operation=listPublicationServers";
	
	$xml = null;
	
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
	if (!$sock = @fsockopen(((strtolower($protocol) == "https://")?"ssl://":"").$server, $port, $errno, $errstr, 5))
	{
			$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_CANNOT_CONNECT_TO WPS"), "ERROR");
			$mainframe->redirect("index.php?option=com_easysdi_publish&task=showError");
	}else{
			$xml = simplexml_load_file($url);		
	}
	
	//retrieve general info of the logged user and config
	$query = "select u.easysdi_user_id, u.publish_user_max_layers, u.publish_user_total_space, u.publish_user_diff_server_id, u.publish_user_diff_server_id from #__easysdi_publish_user u, #__easysdi_community_partner p where u.easysdi_user_id=p.partner_id AND p.user_id=".$joomlaUser->id;
	$database->setQuery($query);
	$currentUser = $database->loadObjectList();
	
	//get the diffusor of the current user and update the currentUser object
	$sid = $currentUser[0]->publish_user_diff_server_id;
	//echo "<pre>";  print_r($xml);  echo "</pre>";
	$diffusor = $xml->xpath("//server[@id=$sid]");
	$diffusor = $diffusor[0];
	$currentUser[0]->diffusion_server_name = (string)$diffusor->name;
	
	//User exist in easysdi and has rights, check if he has been configurated in EasySDI Publish
	if(count($currentUser)<1)
	{                    
		$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_USER_DOESNT_EXIST"), "ERROR");
	  $mainframe->redirect("index.php?option=com_easysdi_publish&task=showError");
		return;
	}
	
	return $currentUser[0];
}
 ?>	