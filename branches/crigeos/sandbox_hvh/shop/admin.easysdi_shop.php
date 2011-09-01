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
jimport("joomla.utilities.date");

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'plugin.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'section.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'model'.DS.'list.easysdi.class.php');

JHTML::_('stylesheet', 'common_easysdi_admin.css', 'administrator/components/com_easysdi_core/templates/css/');
JHTML::_('stylesheet', 'easysdi_shop.css', 'administrator/components/com_easysdi_shop/templates/css/');
JHTML::_('stylesheet', 'easysdi.css', 'templates/easysdi/css/');

$language =& JFactory::getLanguage();
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);
$language->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

global $mainframe;
$task = JRequest::getVar('task');
$option = JRequest::getVar('option');
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch($task){

	case "proxy":
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'proxy.php');
		
		SITE_proxy::proxy();	
		break;
	
	/*****************************************************************************************************************************
	 * Order
	 *****************************************************************************************************************************/
	case "orderReport":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		ADMIN_cpanel::orderReport($cid[0]);
		break;
		
	case "listOrders":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		TOOLBAR_cpanel::_LISTORDERS();
		ADMIN_cpanel::listOrders();
		break;

	case "deleteOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		ADMIN_cpanel::deleteOrder($cid,$option);
		break;
	
	/*****************************************************************************************************************************
	 * BasemapContent
	 *****************************************************************************************************************************/
	case "saveBasemapContent":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::saveBasemapContent(true,$option);				
		break;

	case "deleteBasemapContent":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::deleteBasemapContent($cid,$option);				
		break;	
		
	case "editBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent($cid[0],$option);
		break;
		
	case "newBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent(0,$option);
		break;
		
	case "cancelBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::cancelBasemapContent($option);		
		break;
		
	case "orderupbasemapcontent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::orderUpBasemapContent($option,$cid);
		break;
		
	case "orderdownbasemapcontent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::orderDownBasemapContent($option,$cid);
		break;
		
	case "listBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		TOOLBAR_basemap::_LISTBASEMAPCONTENT($cid[0]);
		ADMIN_basemap::listBasemapContent($cid[0],$option);
		break;
	
	/*****************************************************************************************************************************
	 * Basemap
	 *****************************************************************************************************************************/
	case "saveBasemap":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');

		ADMIN_basemap::saveBasemap(true,$option);				
		break;
		
	case "deleteBasemap":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::deleteBasemap($cid,$option);				
		break;	
		
	case "editBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
	//	JHTML::script('submit.admin.easysdi.js', 'administrator/components/com_easysdi_shop/js/');
				
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap($cid[0],$option);
		break;
		
	case "newBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap(0,$option);		
		break;
	
	case "cancelBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		ADMIN_basemap::cancelBasemap($option);		
		break;
	case "listBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		
		TOOLBAR_basemap::_LISTBASEMAP();
		ADMIN_basemap::listBasemap($option);		
		break;
	
	/*****************************************************************************************************************************
	 * Properties
	 *****************************************************************************************************************************/	
	case "orderdownPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::goDownPropertyValue($cid,$option);		
		break;
		
	case "orderupPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::goUpPropertyValue($cid,$option);
		break;
		
	case "saveOrderPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::saveOrderPropertiesValues($cid, JRequest::getVar('property_id'), $option);
		break;
		
	case "savePropertiesValues":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::savePropertiesValues(true,$option);				
		break;
		
	case "deletePropertiesValues":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::deletePropertiesValues($cid,$option);				
		break;	
		
	case "editPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues($cid[0],$option);
		break;
		
	case "newPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');

		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues(0,$option);
		break;
		
	case "cancelPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		$cid[0] = JRequest::getVar('property_id');
		
		ADMIN_properties::cancelPropertyValue($option, $cid[0]);		
		break;
		
	case "listPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		TOOLBAR_properties::_LISTPROPERTIESVALUES();
		ADMIN_properties::listPropertiesValues($cid[0],$option);		
		break;
		
	case "saveProperties":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		
		ADMIN_properties::saveProperties(true,$option);				
		break;
		
	case "deleteProperties":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');

		ADMIN_properties::deleteProperties($cid,$option);				
		break;	
		
	case "editProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties($cid[0],$option);
		break;
		
	case "newProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');

		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties(0,$option);
		break;
		
	case "cancelProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::cancelProperty($option);
		break;
		
	case "listProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');

		TOOLBAR_properties::_LISTPROPERTIES();
		ADMIN_properties::listProperties($option);		
		break;
		
	case "orderupProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::goUpProperties($cid,$option);
		break;
		
	case "orderdownProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::goDownProperties($cid,$option);
		break;
		
	case "saveOrderProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		ADMIN_properties::saveOrderProperties($cid,$option);
		break;
		
	case "unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		switch (JRequest::getVar('publishedobject','')){
			case "product":
				ADMIN_product::publish($cid,false);	
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				break;
			case "properties":
				ADMIN_properties::publish($cid,false);	
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
				break;
		}				
		break;
		
	case "publish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
		
		switch (JRequest::getVar('publishedobject','')){
			case "properties":
				ADMIN_properties::publish($cid,true);	
				$mainframe->redirect("index.php?option=$option&task=listProperties" );
				break;
			case "product":
				ADMIN_product::publish($cid,true);	
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				break;			
		}				
		break;	
				
	/*****************************************************************************************************************************
	 * Location
	 *****************************************************************************************************************************/
	case "saveLocation":	
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		
		ADMIN_location::saveLocation(true,$option);				
		break;
	
	case "copyLocation":	
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		
		ADMIN_location::copyLocation($cid,$option);
		break;
		
	case "deleteLocation":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		
		ADMIN_location::deleteLocation($cid,$option);				
		break;	
		
	case "editLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation($cid[0],$option);
		break;
		
	case "newLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation(0,$option);		
		break;
		
	case "cancelLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		
		ADMIN_location::cancelLocation($option);		
		break;
		
	case "listLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'location.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'location.easysdi.class.php');
		
		TOOLBAR_location::_LISTLOCATION();
		ADMIN_location::listLocation($option);		
		break;

	/*****************************************************************************************************************************
	 * Perimeter
	 *****************************************************************************************************************************/
	case "savePerimeter":		
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		ADMIN_perimeter::savePerimeter(true,$option);				
		break;
	
	case "copyPerimeter":	
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');

		ADMIN_perimeter::copyPerimeter($cid,$option);
		break;
		
	case "deletePerimeter":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
				
		ADMIN_perimeter::deletePerimeter($cid,$option);				
		break;	
		
	case "editPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter($cid[0],$option);
		break;
		
	case "newPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter(0,$option);		
		break;
	
	case "cancelPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		ADMIN_perimeter::cancelPerimeter($option);		
		break;
		
	case "listPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		TOOLBAR_perimeter::_LISTPERIMETER();
		ADMIN_perimeter::listPerimeter($option);		
		break;
		
	case "orderupPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		ADMIN_perimeter::goupPerimeter($cid, $option);		
		break;
		
	case "orderdownPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		ADMIN_perimeter::godownPerimeter($cid, $option);		
		break;
		
	case "saveOrderPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'perimeter.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		
		ADMIN_perimeter::saveOrderPerimeter($cid, $option);		
		break;
		

	/*****************************************************************************************************************************
	 * Product
	 *****************************************************************************************************************************/
	case "deleteProductFile":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::deleteProductFile($cid[0],$option);
		ADMIN_product::editProduct($cid[0],$option);
		break;
	case "downloadProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		ADMIN_product::downloadProduct();
		break;
		
	case "saveProduct":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productaccount.easysdi.class.php');	
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productperimeter.easysdi.class.php');		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productproperty.easysdi.class.php');
			
		ADMIN_product::saveProduct(true, $option);				
		break;
		
	case "deleteProduct":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		ADMIN_product::deleteProduct($cid,$option);				
		break;	

	case "editProduct":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
		
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct($cid[0],$option);
		break;
		
	case "newProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'submit.admin.easysdi.php');
				
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct(0,$option);		
		break;
				
	case "cancelProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		ADMIN_product::cancelProduct($option);		
		break;
		
	case "listProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');

		TOOLBAR_product::_LISTPRODUCT();
		ADMIN_product::listProduct($option);		
		break;
	
			
	case "ctrlPanelShop":
	default:
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
}

?>