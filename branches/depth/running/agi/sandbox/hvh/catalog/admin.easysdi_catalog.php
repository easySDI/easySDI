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

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');


JHTML::_('stylesheet', 'common_easysdi_admin.css', 'administrator/components/com_easysdi_core/templates/css/');

$language =& JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_catalog');

$task = JRequest::getVar('task');
		
if (array_key_exists('cid[]', $_GET))
	$cid = $_GET['cid[]'];
else
	$cid = JRequest::getVar ('cid', array(0) );

$JId = JRequest::getVar('JId', '');
if (!is_array( $cid )) {
	$cid = array(0);
}

global $mainframe;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');

//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

$option = JRequest::getVar('option');

switch($task){
	case "codevalue_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::changeContent(1);
		break;
	case "codevalue_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::changeContent(0);
		break;
	case "executequery":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'executequery.php');
		ADMIN_query::executeQuery();
		break;		
	case "listProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		TOOLBAR_profile::_DEFAULT();;
		ADMIN_profile::listProfile($option);
		break;
	case "newProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		TOOLBAR_profile::_EDIT();
		ADMIN_profile::editProfile(0,$option);
	break;
	case "editProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		TOOLBAR_profile::_EDIT();		
		ADMIN_profile::editProfile($cid[0],$option);
	break;
	case "removeProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::removeProfile($cid,$option);
		TOOLBAR_profile::_DEFAULT();;
		ADMIN_profile::listProfile($option);
		break;
	case "saveProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::saveProfile($option);
		TOOLBAR_profile::_DEFAULT();;
		ADMIN_profile::listProfile($option);
		break;
	case "applyProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::saveProfile($option);
		break;
	case "cancelProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		TOOLBAR_profile::_DEFAULT();;
		ADMIN_profile::listProfile($option);
		break;
	case "saveOrderProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::saveOrder($option);
		break;
	case "orderupProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::orderContent(-1, $option);
		break;
	case "orderdownProfile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi.php');
		ADMIN_profile::orderContent(1, $option);
		break;
		
	
	// Package
	case "listPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		TOOLBAR_package::_DEFAULT();;
		ADMIN_package::listPackage($option);
		break;
	case "newPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		TOOLBAR_package::_EDIT();
		ADMIN_package::editPackage(0,$option);
	break;
	case "editPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		TOOLBAR_package::_EDIT();		
		ADMIN_package::editPackage($cid[0],$option);
	break;
	case "removePackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::removePackage($cid,$option);
		TOOLBAR_package::_DEFAULT();
		ADMIN_package::listPackage($option);
		break;
	case "savePackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::savePackage($option);
		TOOLBAR_package::_DEFAULT();
		ADMIN_package::listPackage($option);
		break;
	case "applyPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::savePackage($option);
		break;
	case "cancelPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		TOOLBAR_package::_DEFAULT();
		ADMIN_package::listPackage($option);
		break;
	case "saveOrderPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::saveOrder($option);
		break;
	case "orderupPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::orderContent(-1, $option);
		break;
	case "orderdownPackage":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'package.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'package.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'package.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'package.admin.easysdi.php');
		ADMIN_package::orderContent(1, $option);
		break;
		
		
	// Classes
	case "listClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		TOOLBAR_class::_DEFAULT();
		ADMIN_class::listClass($option);
		break;
	case "newClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		TOOLBAR_class::_EDIT();
		ADMIN_class::editClass(0,$option);
	break;
	case "editClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		TOOLBAR_class::_EDIT();		
		ADMIN_class::editClass($cid[0],$option);
	break;
	case "removeClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::removeClass($cid,$option);
		TOOLBAR_class::_DEFAULT();
		ADMIN_class::listClass($option);
		break;
	case "saveClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::saveClass($option);
		TOOLBAR_class::_DEFAULT();
		ADMIN_class::listClass($option);
		break;
	case "applyClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::saveClass($option);
		break;
	case "cancelClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::cancelClass($option);
		TOOLBAR_class::_DEFAULT();
		ADMIN_class::listClass($option);
		break;
	case "saveOrderClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::saveOrder($option);
		break;
	case "orderupClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::orderContent(-1, $option);
		break;
	case "orderdownClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::orderContent(1, $option);
		break;
	case "class_issystem_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::changeState('isystem', 1);
		break;
	case "class_issystem_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::changeState('issystem', 0);
		break;
	case "class_isextensible_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::changeState('isextensible', 1);
		break;
	case "class_isextensible_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'class.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'class.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'class.admin.easysdi.php');
		ADMIN_class::changeState('isextensible', 0);
		break;	
			
	// Models
	case "listModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		TOOLBAR_model::_DEFAULT();
		ADMIN_model::listModel($option);
		break;
	case "newModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		TOOLBAR_model::_EDIT();
		ADMIN_model::editModel(0,$option);
	break;
	case "editModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		TOOLBAR_model::_EDIT();		
		ADMIN_model::editModel($cid[0],$option);
	break;
	case "removeModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		ADMIN_model::removeModel($cid[0],$option);
		TOOLBAR_model::_DEFAULT();
		ADMIN_model::listModel($option);
		break;
	case "saveModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		ADMIN_model::saveModel($option);
		TOOLBAR_model::_DEFAULT();
		ADMIN_model::listModel($option);
		break;
	case "cancelModel":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'model.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'model.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'model.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'model.admin.easysdi.php');
		TOOLBAR_model::_DEFAULT();
		ADMIN_model::listModel($option);
		break;
		
	// Attributes
	case "listAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		TOOLBAR_attribute::_DEFAULT();
		ADMIN_attribute::listAttribute($option);
		break;
	case "newAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		TOOLBAR_attribute::_EDIT();
		ADMIN_attribute::editAttribute(0,$option);
	break;
	case "editAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		TOOLBAR_attribute::_EDIT();		
		ADMIN_attribute::editAttribute($cid[0],$option);
	break;
	case "removeAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::removeAttribute($cid,$option);
		TOOLBAR_attribute::_DEFAULT();
		ADMIN_attribute::listAttribute($option);
		break;
	case "saveAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::saveAttribute($option);
		TOOLBAR_attribute::_DEFAULT();
		ADMIN_attribute::listAttribute($option);
		break;
	case "applyAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::saveAttribute($option);
		break;
	case "cancelAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::cancelAttribute($option);
		TOOLBAR_attribute::_DEFAULT();
		ADMIN_attribute::listAttribute($option);
		break;
	case "saveOrderAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::saveOrder($option);
		break;
	case "orderupAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::orderContent(-1, $option);
		break;
	case "orderdownAttribute":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::orderContent(1, $option);
		break;
	case "attribute_issystem_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::changeState('issystem', 1);
		break;
	case "attribute_issystem_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		ADMIN_attribute::changeState('issystem', 0);
		break;
		
	// CodeValue
	case "listCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		TOOLBAR_codevalue::_DEFAULT();
		ADMIN_codevalue::listCodeValue($option);
		break;
	case "newCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		
		TOOLBAR_codevalue::_EDIT();
		ADMIN_codevalue::editCodeValue(0,$option);
	break;
	case "editCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		
		TOOLBAR_codevalue::_EDIT();		
		
		ADMIN_codevalue::editCodeValue($cid[0], $option);
	break;
	case "removeCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::removeCodeValue($cid,$option);
		TOOLBAR_codevalue::_DEFAULT();
		ADMIN_codevalue::listCodeValue($option);
		break;
	case "saveCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		ADMIN_codevalue::saveCodeValue($option);
		TOOLBAR_codevalue::_DEFAULT();
		ADMIN_codevalue::listCodeValue($option);
		break;
	case "applyCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		ADMIN_codevalue::saveCodeValue($option);
		break;
	case "cancelCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		ADMIN_codevalue::cancelCodeValue($option);
		TOOLBAR_codevalue::_DEFAULT();
		ADMIN_codevalue::listCodeValue($option);
		break;
	case "saveOrderCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::saveOrder($option);
		break;
	case "orderupCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::orderContent(-1, $option);
		break;
	case "orderdownCodeValue":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'codevalue.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'codevalue.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'codevalue.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::orderContent(1, $option);
		break;
		
	// Relation
	case "listRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		TOOLBAR_relation::_DEFAULT();
		ADMIN_relation::listRelation($option);
		break;
	case "newRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_relation::_EDIT();
		ADMIN_relation::newRelation(0, $option);
	break;
	case "editRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_relation::_EDIT();
		ADMIN_relation::editRelation($cid[0],$option);
	break;
	case "removeRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		ADMIN_relation::removeRelation($cid,$option);
		TOOLBAR_relation::_DEFAULT();
		ADMIN_relation::listRelation($option);
		break;
	case "saveRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationcontext.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		ADMIN_relation::saveRelation($option);
		TOOLBAR_relation::_DEFAULT();
		ADMIN_relation::listRelation($option);
		break;
	case "applyRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'class.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationprofile.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationcontext.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		ADMIN_relation::saveRelation($option);
		break;
	case "cancelRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		ADMIN_relation::cancelRelation($option);
		TOOLBAR_relation::_DEFAULT();
		ADMIN_relation::listRelation($option);
		break;
	case "saveOrderRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationprofile.easysdi.class.php');
		ADMIN_relation::saveOrder($option);
		break;
	case "orderupRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationprofile.easysdi.class.php');
		ADMIN_relation::orderContent(-1, $option);
		break;
	case "orderdownRelation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationprofile.easysdi.class.php');
		ADMIN_relation::orderContent(1, $option);
		break;
	case "relation_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		ADMIN_relation::changeContent(1);
		break;
	case "relation_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'relation.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'relation.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'relation.admin.easysdi.php');
		ADMIN_relation::changeContent(0);
		break;
		
	// Namespace
	case "listMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		TOOLBAR_mdnamespace::_DEFAULT();
		ADMIN_mdnamespace::listMDNamespace($option);
		break;
	case "newMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		TOOLBAR_mdnamespace::_EDIT();		
		ADMIN_mdnamespace::editMDNamespace(0,$option);
	break;
	case "editMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		TOOLBAR_mdnamespace::_EDIT();		
		ADMIN_mdnamespace::editMDNamespace($cid[0],$option);
	break;
	case "saveMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::saveMDNamespace($option);
		TOOLBAR_mdnamespace::_DEFAULT();
		ADMIN_mdnamespace::listMDNamespace($option);
		break;
	case "applyMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::saveMDNamespace($option);
		break;
	case "removeMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		ADMIN_mdnamespace::removeMDNamespace($cid,$option);
		TOOLBAR_mdnamespace::_DEFAULT();
		ADMIN_mdnamespace::listMDNamespace($option);
		break;
	case "cancelMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		TOOLBAR_mdnamespace::_DEFAULT();
		ADMIN_mdnamespace::listMDNamespace($option);
		break;
	case "saveOrderMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::saveOrder($option);
		break;
	case "orderupMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::orderContent(-1, $option);
		break;
	case "orderdownMDNamespace":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::orderContent(1, $option);
		break;
	case "namespace_issystem_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::changeState('system', 1);
		break;
	case "namespace_issystem_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'mdnamespace.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'mdnamespace.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'mdnamespace.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_mdnamespace::changeState('system', 0);
		break;
		
	// Predefined boundaries
	case "getParentPerimeterList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::getParentPerimeterList(JRequest::getVar("category_id"));
		break;
	case "listBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		TOOLBAR_boundarycategory::_DEFAULT();
		ADMIN_boundary::listBoundaryCategory($option);
		break;
	case "newBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_boundarycategory::_EDIT();
		ADMIN_boundary::editBoundaryCategory(0,$option);
		break;
	case "editBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_boundarycategory::_EDIT();
		ADMIN_boundary::editBoundaryCategory($cid[0],$option);
		break;
	case "saveBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::saveBoundaryCategory($option);
		TOOLBAR_boundarycategory::_DEFAULT();
		ADMIN_boundary::listBoundaryCategory($option);
		break;
	case "applyBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::saveBoundaryCategory($option);
		break;
	case "removeBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::removeBoundaryCategory($cid,$option);
		TOOLBAR_boundary::_DEFAULT();
		ADMIN_boundary::listBoundaryCategory($option);
		break;
	case "cancelBoundaryCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::cancelBoundaryCategory($option);
		break;
	case "listBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		TOOLBAR_boundary::_DEFAULT();
		ADMIN_boundary::listBoundary($option);
		break;
	case "newBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_boundary::_EDIT();
		ADMIN_boundary::editBoundary(0,$option);
	break;
	case "editBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_boundary::_EDIT();		
		ADMIN_boundary::editBoundary($cid[0],$option);
	break;
	case "saveBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::saveBoundary($option);
		TOOLBAR_boundary::_DEFAULT();
		ADMIN_boundary::listBoundary($option);
		break;
	case "applyBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::saveBoundary($option);
		break;
	case "removeBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::removeBoundary($cid,$option);
		TOOLBAR_boundary::_DEFAULT();
		ADMIN_boundary::listBoundary($option);
		break;
	case "cancelBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::cancelBoundary($option);
		TOOLBAR_boundary::_DEFAULT();
		ADMIN_boundary::listBoundary($option);
		break;
	case "saveOrderBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::saveOrder($option);
		break;
	case "orderupBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::orderContent(-1, $option);
		break;
	case "orderdownBoundary":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'boundary.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'boundary.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'boundary.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'boundary.admin.easysdi.php');
		ADMIN_boundary::orderContent(1, $option);
		break;
	case "getBoundariesByCategory":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
		SERVICE_boundary::getBoundariesByCategoriesAlias(JRequest::getVar('category', null),JRequest::getVar('exclude', null));
		break;
	case "getBoundaries";
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
		SERVICE_boundary::getBoundariesByCategoriesAlias(null, JRequest::getVar('exclude', null));
		break;
	case "getBoundariesByCategoriesId";
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
		SERVICE_boundary::getBoundariesByCategoriesId(JRequest::getVar('category', null));
		break;
	case "getBoundariesByLabel";
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
		SERVICE_boundary::getBoundariesByLabel(JRequest::getVar('query', null), JRequest::getVar('category', null));
		break;
	// Import Reference
	case "listImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_importref::_DEFAULT();
		ADMIN_importref::listImportRef($option);
		break;
	case "newImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_importref::_EDIT();
		ADMIN_importref::editImportRef(0,$option);
	break;
	case "editImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_importref::_EDIT();		
		ADMIN_importref::editImportRef($cid[0],$option);
	break;
	case "saveImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::saveImportRef($option);
		TOOLBAR_importref::_DEFAULT();
		ADMIN_importref::listImportRef($option);
		break;
	case "applyImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::saveImportRef($option);
		break;
	case "removeImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::removeImportRef($cid,$option);
		TOOLBAR_importref::_DEFAULT();
		ADMIN_importref::listImportRef($option);
		break;
	case "cancelImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::cancelImportRef($option);
		TOOLBAR_importref::_DEFAULT();
		ADMIN_importref::listImportRef($option);
		break;
	case "saveOrderImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::saveOrder($option);
		break;
	case "orderupImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::orderContent(-1, $option);
		break;
	case "orderdownImportRef":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'importref.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'importref.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'importref.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'importref.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_importref::orderContent(1, $option);
		break;

	// ObjectType Link
	case "listObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		TOOLBAR_objecttypelink::_DEFAULT();
		ADMIN_objecttypelink::listObjectTypeLink($option);
		break;
	case "newObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_objecttypelink::_EDIT();
		ADMIN_objecttypelink::editObjectTypeLink(0,$option);
	break;
	case "editObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
		TOOLBAR_objecttypelink::_EDIT();		
		ADMIN_objecttypelink::editObjectTypeLink($cid[0],$option);
	break;
	case "saveObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::saveObjectTypeLink($option);
		TOOLBAR_objecttypelink::_DEFAULT();
		ADMIN_objecttypelink::listObjectTypeLink($option);
		break;
	case "applyObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::saveObjectTypeLink($option);
		break;
	case "removeObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::removeObjectTypeLink($cid,$option);
		TOOLBAR_objecttypelink::_DEFAULT();
		ADMIN_objecttypelink::listObjectTypeLink($option);
		break;
	case "cancelObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::cancelObjectTypeLink($option);
		TOOLBAR_objecttypelink::_DEFAULT();
		ADMIN_objecttypelink::listObjectTypeLink($option);
		break;
	case "saveOrderObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::saveOrder($option);
		break;
	case "orderupObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::orderContent(-1, $option);
		break;
	case "orderdownObjectTypeLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::orderContent(1, $option);
		break;
	case "objecttypelink_escalate_versioning_update_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::changeState('escalate_versioning_update', 1);
		break;
	case "objecttypelink_escalate_versioning_update_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::changeState('escalate_versioning_update', 0);
		break;
	case "objecttypelink_flowdown_versioning_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::changeState('flowdown_versioning', 1);
		break;
	case "objecttypelink_flowdown_versioning_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttypelink.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttypelink.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttypelink.admin.easysdi.php');
		ADMIN_objecttypelink::changeState('flowdown_versioning', 0);
		break;
		
	// Metadata
	case "askForEditMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_metadata::askForEditMetadata($cid[0],$option);
		break;
	case "editMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		TOOLBAR_metadata::_EDIT();
		ADMIN_metadata::editMetadata($cid[0],$option);
		break;
	case "saveMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::saveMetadata($option);
		break;
	case "applyMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		ADMIN_metadata::saveMetadata($option);
		break;
	case "cancelMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		ADMIN_metadata::cancelMetadata($option);
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;
	case "previewXMLMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		ADMIN_metadata::preview('XML');
		break;
	case "previewMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		ADMIN_metadata::preview('MD');
		break;
	case "validateMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::validateMetadata($option);
		break;
	case "updateMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::updateMetadata($option);
		break;

	case "invalidateMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::invalidateMetadata($option);
		break;
		
	case "validateForPublishMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::validateForPublishMetadata($option);
		break;
	
	case "publishMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		ADMIN_metadata::publishMetadata($option);
		break;

	case "importXMLMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		TOOLBAR_metadata::_EDIT();		
		ADMIN_metadata::importXMLMetadata($option);
		break;
	
	case "importCSWMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		TOOLBAR_metadata::_EDIT();		
		ADMIN_metadata::importCSWMetadata($option);
		break;
	
	case "replicateMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
        require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		TOOLBAR_metadata::_EDIT();		
		ADMIN_metadata::replicateMetadata($option);
		break;
		
	case "resetMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		TOOLBAR_metadata::_EDIT();		
		ADMIN_metadata::resetMetadata($option);
		break;
		
	case "synchronizeMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		ADMIN_metadata::synchronizeMetadata();
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
	//
	case "getContact":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		ADMIN_metadata::getContact($option);
		break;
		
	case "getObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		ADMIN_metadata::getObjectVersion($option);
		break;
		
	//Object type
	case "listObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		TOOLBAR_objecttype::_DEFAULT();
		ADMIN_objecttype::listObjectType( $option );
		break;
	
	case "newObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		TOOLBAR_objecttype::_EDIT();
		ADMIN_objecttype::editObjectType(0, $option);
		break;

	case "removeObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		ADMIN_objecttype::removeObjectType($cid, $option);
		TOOLBAR_objecttype::_DEFAULT();
		ADMIN_objecttype::listObjectType($option);
		break;
		
	case "editObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		TOOLBAR_objecttype::_EDIT();
		ADMIN_objecttype::editObjectType($cid[0], $option);
		break;
		
	case "saveObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		ADMIN_objecttype::saveObjectType($option);
		TOOLBAR_objecttype::_DEFAULT();
		ADMIN_objecttype::listObjectType($option);
		break;

	case "applyObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');

		ADMIN_objecttype::saveObjectType($option);
		break;
	
	case "cancelObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		TOOLBAR_objecttype::_DEFAULT();
		ADMIN_objecttype::listObjectType( $option );
		break;
	case "saveOrderObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::saveOrder($option);
		break;
	case "orderupObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::orderContent(-1, $option);
		break;
	case "orderdownObjectType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::orderContent(1, $option);
		break;
	case "objecttype_predefined_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::changeState('predefined', 1);
		break;
	case "objecttype_predefined_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::changeState('predefined', 0);
		break;
	case "objecttype_hasversioning_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::changeState('hasVersioning', 1);
		break;
	case "objecttype_hasversioning_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objecttype.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objecttype.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objecttype.admin.easysdi.php');
		ADMIN_objecttype::changeState('hasVersioning', 0);
		break;
		
	case "listObject":			
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;
		
	case "editObject":			
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		TOOLBAR_object::_EDIT();
		ADMIN_object::editObject($cid[0],$option);
		break;
		
	case "saveObject":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		
		ADMIN_object::saveObject($option);				
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;
	
	case "applyObject":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		ADMIN_object::saveObject($option);				
		break;
		
	case "deleteObject":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		ADMIN_object::deleteObject($cid,$option);				
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;

	case "newObject":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		TOOLBAR_object::_EDIT();
		ADMIN_object::editObject(0,$option);		
		break;
		
	case "cancelObject":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		ADMIN_object::cancelObject($option);
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;
	case "saveOrderObject":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		ADMIN_object::saveOrder($option);
		break;
	case "orderupObject":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		ADMIN_object::orderContent(-1, $option);
		break;
	case "orderdownObject":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		ADMIN_object::orderContent(1, $option);
		break;
			
		
	case "object_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		//TOOLBAR_object::_DEFAULT();
		//ADMIN_object::listObject($option);
		ADMIN_object::changeContent(1);
		break;
	case "object_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		//TOOLBAR_object::_DEFAULT();
		//ADMIN_object::listObject($option);
		ADMIN_object::changeContent(0);
		break;
	
	case "historyAssignMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		
		TOOLBAR_objectversion::_HISTORY();
		ADMIN_objectversion::historyAssignMetadata($cid[0],$option);
		break;

	case "backHistoryAssign":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
	
	case "viewObjectVersionLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		JHTML::_('behavior.modal');
		TOOLBAR_objectversion::_VIEW();
		ADMIN_objectversion::viewObjectVersionLink($cid[0],$option);
		break;
	case "manageObjectVersionLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_objectversion::_MANAGE();
		ADMIN_objectversion::manageObjectVersionLink($cid[0],$option);
		break;
	case "saveObjectVersionLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		
		ADMIN_objectversion::saveObjectVersionLink($option);
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
	case "backObjectVersionLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
		
	case "getObjectVersionForLink":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		ADMIN_objectversion::getObjectVersionForLink($option);
		break;
		
	case "newObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_objectversion::_EDIT();
		ADMIN_objectversion::editObjectVersion(0, $option);
		break;
	
	case "listObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
		
	case "editObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		TOOLBAR_objectversion::_EDIT();
		ADMIN_objectversion::editObjectVersion($cid[0], $option);
		break;
		
	case "deleteObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		ADMIN_objectversion::deleteObjectVersion($cid, $option);
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
		
	case "cancelObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_objectversion::cancelObjectVersion($option);
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
		
	case "saveObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_objectversion::saveObjectVersion($option);
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;
		
	case "applyObjectVersion":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_objectversion::saveObjectVersion($option);
		break;
		
	case "archiveObjectVersion":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'objectversion.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'objectversion.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_objectversion::archiveObjectVersion($cid,$option);				
		TOOLBAR_objectversion::_DEFAULT();
		ADMIN_objectversion::listObjectVersion($option);
		break;

	case 'backObjectVersion':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_objectversion::backObjectVersion($option);
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;

	case "listContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		TOOLBAR_context::_DEFAULT();
		ADMIN_context::listContext($option);
		break;
	case "newContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		TOOLBAR_context::_EDIT();
		ADMIN_context::editContext(0, $option);
		break;
	case "editContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		TOOLBAR_context::_EDIT();
		ADMIN_context::editContext($cid[0], $option);
		break;
	case "saveContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'contextobjecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		ADMIN_context::saveContext($option);
		TOOLBAR_context::_DEFAULT();
		ADMIN_context::listContext($option);
		break;
	case "applyContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'contextobjecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		ADMIN_context::saveContext($option);
		break;
	case "deleteContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'contextobjecttype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		ADMIN_context::deleteContext($cid, $option);
		TOOLBAR_context::_DEFAULT();
		ADMIN_context::listContext($option);
		break;
	case "cancelContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		ADMIN_context::cancelContext($option);
		TOOLBAR_context::_DEFAULT();
		ADMIN_context::listContext($option);
		break;
	case "saveOrderContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		ADMIN_context::saveOrder($option);
		break;
	case "orderupContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		ADMIN_context::orderContent(-1, $option);
		break;
	case "orderdownContext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		ADMIN_context::orderContent(1, $option);
		break;

		
	// External applications
	case "listApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		TOOLBAR_application::_DEFAULT();
		ADMIN_application::listApplication($option);
		break;
	case "newApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_application::_EDIT();
		ADMIN_application::editApplication(0,$option);
	break;
	case "editApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		TOOLBAR_application::_EDIT();		
		ADMIN_application::editApplication($cid[0],$option);
	break;
	case "saveApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::saveApplication($option);
		TOOLBAR_application::_DEFAULT();
		ADMIN_application::listApplication($option);
		break;
	case "applyApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::saveApplication($option);
		break;
	case "removeApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::removeApplication($cid,$option);
		TOOLBAR_application::_DEFAULT();
		ADMIN_application::listApplication($option);
		break;
	case "cancelApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::cancelApplication($option);
		TOOLBAR_application::_DEFAULT();
		ADMIN_application::listApplication($option);
		break;
	case "saveOrderApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::saveOrder($option);
		break;
	case "orderupApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::orderContent(-1, $option);
		break;
	case "orderdownApplication":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		ADMIN_application::orderContent(1, $option);
		break;
	case 'backApplication':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'application.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'application.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'object.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'object.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		ADMIN_application::backApplication($option);
		TOOLBAR_object::_DEFAULT();
		ADMIN_object::listObject($option);
		break;
			
	/* Search Criteria */
	case "listSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_searchcriteria::_DEFAULT();
		ADMIN_searchcriteria::listSearchCriteria($option);
		break;
	case "newSearchCriteria":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_searchcriteria::_EDIT();
		ADMIN_searchcriteria::editSearchCriteria(0, $option);
		break;
	case "editSearchCriteria":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		TOOLBAR_searchcriteria::_EDIT();
		ADMIN_searchcriteria::editSearchCriteria($cid[0], $option);
		break;
	case "saveSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::saveSearchCriteria($option);
		TOOLBAR_searchcriteria::_DEFAULT();
		ADMIN_searchcriteria::listSearchCriteria($option);
		break;
	case "applySearchCriteria":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::saveSearchCriteria($option);
		break;
	case "removeSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::removeSearchCriteria($cid, $option);
		TOOLBAR_searchcriteria::_DEFAULT();
		ADMIN_searchcriteria::listSearchCriteria($option);
		break;
	case "cancelSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::cancelSearchCriteria($option);
		TOOLBAR_searchcriteria::_DEFAULT();
		ADMIN_searchcriteria::listSearchCriteria($option);
		break;
	case "saveOrderSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::saveOrder($option);
		break;
	case "orderupSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::orderContent(-1, $option);
		break;
	case "orderdownSearchCriteria":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::orderContent(1, $option);
		break;
	case 'backSearchCriteria':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'context.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'context.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'context.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::backSearchCriteria($option);
		TOOLBAR_context::_DEFAULT();
		ADMIN_context::listContext($option);
		break;
	case "searchcriteria_simpletab_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::changeState('simpletab', 'advancedtab', 1);
		break;
	case "searchcriteria_simpletab_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::changeState('simpletab', 'advancedtab', 0);
		break;
	case "searchcriteria_advancedtab_publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::changeState('advancedtab', 'simpletab', 1);
		break;
	case "searchcriteria_advancedtab_unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::changeState('advancedtab', 'simpletab', 0);
		break;
	case "searchcriteria_tab_none":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::tabSet(0);
		break;
	case "searchcriteria_tab_simple":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::tabSet(1);
		break;
	case "searchcriteria_tab_advanced":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::tabSet(2);
		break;
	case "searchcriteria_tab_hidden":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchcriteria.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchcriteria.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchcriteria.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchcriteria.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		ADMIN_searchcriteria::tabSet(3);
		break;
	
	case 'cpanel':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
	
	case 'back':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attribute.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'attribute.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'attribute.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'attribute.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'codevalue.admin.easysdi.php');
		ADMIN_codevalue::back($option);
		TOOLBAR_attribute::_DEFAULT();
		ADMIN_attribute::listAttribute($option);
		break;
		
	case 'test':
		ADMIN_test::test($option);
		break;
	case 'listQueryReports':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery::_DEFAULT();
		ADMIN_xQuery::listQueryReports();
		break;
	case 'newXQueryReport':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery::_EDIT();
		ADMIN_xQuery::newXQueryReport();
		break;	
	case 'editXQueryReport':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery:: _EDIT();
		ADMIN_xQuery::editXQueryReport();
		break;	
	case 'saveXQueryReport':
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery::_DEFAULT();
		ADMIN_xQuery::saveXQueryReport();
		break;	
	case 'deleteXQueryReport':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery::_DEFAULT();
		ADMIN_xQuery::deleteXQueryReport();
		break;
	case 'cancelXQueryReport':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;	
	case 'assignXQueryReport':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		TOOLBAR_XQuery::_MANAGEUSERS();
		ADMIN_xQuery::assignXQueryReport();
		break;
		
	case 'saveXQueryUserReportAccess':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'xquery.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		ADMIN_xQuery::saveXQueryUserReportAccess();
		break;
		
	case 'listMyReports':
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
		ADMIN_xQuery::listMyReports();
		break;
		
	case 'processXQueryReport':
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
	//	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php'); //reuse buildcsw
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); // for same curl as search results
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
			
		ADMIN_xQuery::processXQueryReport();
		break;
	case 'provideXMLDataForXQueryReport':
		//	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php'); //reuse buildcsw
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); // for same curl as search results
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
			
		ADMIN_xQuery::provideXMLDataForXQueryReport();
		break;
	case "uploadFileAndGetLink":
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
			ADMIN_metadata::uploadFileAndGetLink($option);
			break;
	case "deleteUploadedFile":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
		ADMIN_metadata::deleteUploadedFile($option,JRequest::getVar ('file',null ));
		break;
	default:
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
		
}

?>