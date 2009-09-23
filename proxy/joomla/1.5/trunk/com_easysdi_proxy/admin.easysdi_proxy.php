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
jimport("joomla.html.pagination");
jimport("joomla.html.pane");
jimport("joomla.database.table");
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'plugin.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'section.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

JHTML::_('stylesheet', 'com_easysdi_proxy.css', 'administrator/components/com_easysdi_proxy/templates/css/');

$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

global $mainframe;


/*Replaced by storage of the configFilePath into table easysdi_config
 */
/*$componentConfigFilePath = JPATH_COMPONENT_ADMINISTRATOR.DS.'config'.DS.'com_easysdi.xml';

$xmlConfig = simplexml_load_file($componentConfigFilePath);
if ($xmlConfig === false){
	$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE EASY SDI CONFIGURATION FILE' ),'error');
}


$configFilePath = $xmlConfig->proxy->configFilePath;
$xml = simplexml_load_file($configFilePath);
*/
$configFilePath = config_easysdi::getValue("PROXY_CONFIG");
$xml = simplexml_load_file(config_easysdi::getValue("PROXY_CONFIG"));

if ($xml === false){
	$mainframe->enqueueMessage(JText::_(  'EASYSDI_PLEASE VERIFY THE CONFIGURATION FILE PATH' ),'error');
}

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'proxy.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'proxy.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'proxy.admin.easysdi.php');

switch($task){
	case 'componentConfig':
		TOOLBAR_proxy::editComponentConfig();
		HTML_proxy::configComponent($xmlConfig);
		break;
	case 'saveComponentConfig':
		ADMIN_PROXY::saveComponentConfig($xmlConfig,$componentConfigFilePath);
		HTML_proxy::ctrlPanel();
		break;
	case 'copyPolicy':
		ADMIN_PROXY::copyPolicy ($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'addPolicy':
		TOOLBAR_proxy::editPolicy();
		HTML_proxy::editPolicy($xml,true);
		break;
	case 'orderuppolicy':
		ADMIN_PROXY::orderupPolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'orderdownpolicy':
		ADMIN_PROXY::orderdownPolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'editPolicyList':
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'editConfig':
		TOOLBAR_proxy::editConfig();
		HTML_proxy::editConfig($xml);
		break;
	case 'editPolicy':
		TOOLBAR_proxy::editPolicy();
		HTML_proxy::editPolicy($xml);
		break;
	case 'deletePolicy':
		$configId = JRequest::getVar("configId");
		$policyId = JRequest::getVar("policyId");		
		ADMIN_PROXY::deletePolicy($xml,$configId,$policyId);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'addConfig':
		TOOLBAR_proxy::editConfig();
		HTML_proxy::editConfig($xml,true);
		break;
	case 'deleteConfig':
		$configId = JRequest::getVar("configId");
		ADMIN_PROXY::deleteConfig($xml,$configFilePath,$configId);
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'savePolicy':
		ADMIN_PROXY::savePolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'cancelPolicy':
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'saveConfig':
		ADMIN_PROXY::saveConfig($xml,$configFilePath);
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'cancel':	
	case 'showConfigList':
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'cpanel':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
	case 'helpQueryTemplate' :
		$filter_type = JRequest::getVar('filter_type');
		HTML_proxy::helpQueryTemplate($filter_type);
		break;
	case 'helpQueryWMSTemplate' :
		HTML_proxy::helpQueryWMSTemplate();
		break;
	case 'helpAttributeFilter' :
		HTML_proxy::helpAttributeFilter();
		break;
	case 'helpImageSize' :
		HTML_proxy::helpImageSize();
		break;
	case 'cancelConfigList':
	case 'cancelComponentConfig':
	default:
		HTML_proxy::ctrlPanel();
		break;
}


?>