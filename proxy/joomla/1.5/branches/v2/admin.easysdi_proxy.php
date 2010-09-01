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

$language =& JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_catalog');
$language->load('com_easysdi_proxy');

$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
$JId = JRequest::getVar('JId', '');
if (!is_array( $cid )) {
	$cid = array(0);
}

$document = &JFactory::getDocument();

global $mainframe;


$configFilePath = config_easysdi::getValue("PROXY_CONFIG");
$xml = simplexml_load_file(config_easysdi::getValue("PROXY_CONFIG", null,LIBXML_COMPACT));

if ($xml === false){
	$mainframe->enqueueMessage(JText::_(  'EASYSDI_PLEASE VERIFY THE CONFIGURATION FILE PATH' ),'error');
}

switch($task){
	case 'componentConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::editComponentConfig();
		HTML_proxy::configComponent($xmlConfig);
		break;
	case 'saveComponentConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::saveComponentConfig($xmlConfig,$componentConfigFilePath);
		HTML_proxy::ctrlPanel();
		break;
	case 'copyPolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::copyPolicy ($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'addPolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::editPolicy();
		ADMIN_proxy::editPolicy($xml,true);
		break;
	case 'orderuppolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::orderupPolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'orderdownpolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::orderdownPolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'editPolicyList':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'editConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		//$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_proxy/js/config.js' );
		
		TOOLBAR_proxy::editConfig();
		ADMIN_proxy::editConfig($xml);
		break;
	case 'editPolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_proxy/js/policy.js' );

		TOOLBAR_proxy::editPolicy();
		ADMIN_proxy::editPolicy($xml);
		break;
	case 'deletePolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		$configId = JRequest::getVar("configId");
		$policyId = JRequest::getVar("policyId");		
		ADMIN_PROXY::deletePolicy($xml,$configId,$policyId);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'addConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::editConfig();
		ADMIN_proxy::editConfig($xml,true);
		break;
	case 'deleteConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		$configId = JRequest::getVar("configId");
		ADMIN_PROXY::deleteConfig($xml,$configFilePath,$configId);
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'savePolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::savePolicy($xml);
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'cancelPolicy':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::editPolicyList();
		HTML_proxy::showPoliciesList($xml);
		break;
	case 'saveConfig':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		ADMIN_PROXY::saveConfig($xml,$configFilePath);
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'cancel':	
	case 'showConfigList':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		TOOLBAR_proxy::configList();
		HTML_proxy::showConfigList($xml);
		break;
	case 'cpanel':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
	
	case 'helpQueryTemplate' :
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		$filter_type = JRequest::getVar('filter_type');
		HTML_proxy::helpQueryTemplate($filter_type);
		break;
	case 'helpQueryWMSTemplate' :
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		HTML_proxy::helpQueryWMSTemplate();
		break;
	case 'helpAttributeFilter' :
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		HTML_proxy::helpAttributeFilter();
		break;
	case 'helpImageSize' :
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		HTML_proxy::helpImageSize();
		break;
	case 'cancelConfigList':
	case 'cancelComponentConfig':
	default:
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'proxy.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'proxy.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'proxy.admin.easysdi.php');
		
		HTML_proxy::ctrlPanel();
		break;
}


?>