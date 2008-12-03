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







$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

global $mainframe;

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'partner.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'partner.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'partner.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'partner.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'mailing.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'mailing.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'mailing.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'mailing.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'config.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'config.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'config.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'config.admin.easysdi.php');


switch($task){

	case "cancelConfig":
	case "listConfig":
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
	case "editConfig":		
		TOOLBAR_config::_EDIT();
		ADMIN_config::editConfig($cid[0], $option );
		break;
	case "newConfig":
		TOOLBAR_config::_EDIT();
		ADMIN_config::editConfig($cid[0], $option );
		break;
	case "saveConfig":		
		ADMIN_config::saveConfig($option );
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
	case "deleteConfig":		
		ADMIN_config::removeConfig($cid,$option );
		TOOLBAR_config::_DEFAULT();
		ADMIN_config::listConfig( $option );
		break;
		
		
	case "exportPartner":
		ADMIN_partner::exportPartner( $cid, $option );
		break;
	
	case "cancelPartner":
		ADMIN_partner::cancelPartner( false, $option );
		TOOLBAR_partner::_DEFAULT();
		ADMIN_partner::listPartner( $option );
		
		break;	
	
	case "editAffiliatePartner":
		TOOLBAR_partner::_EDIT();
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		ADMIN_partner::editAffiliatePartner( $cid[0], $option );
		break;
	
	case "newAffiliatePartner":
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_partner::_EDIT();
		ADMIN_partner::editAffiliatePartner( 0, $option );
		break;
	
	case "removePartner":
		ADMIN_partner::removePartner( $cid, $option );
		break;
	
	case "editRootPartner":
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_partner::_EDIT();
		ADMIN_partner::editRootPartner( $cid[0], $option );
		break;
	
	case "savePartner":
		ADMIN_partner::savePartner( false, $option );
		TOOLBAR_partner::_DEFAULT();
		ADMIN_partner::listPartner( $option );
		break;
	
	case "newRootPartner":
		include (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		TOOLBAR_partner::_EDIT();
		ADMIN_partner::editRootPartner( 0, $option );
		break;
	
	case "listPartner":
		TOOLBAR_partner::_DEFAULT();
		ADMIN_partner::listPartner( $option );
		break;
		
	
}

?>