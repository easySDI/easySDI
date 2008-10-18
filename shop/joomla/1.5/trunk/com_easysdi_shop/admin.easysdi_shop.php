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

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');


switch($task){
	case "orderdownproperties":
		ADMIN_properties::goDown($cid,$option);
		
		break;
	case "orderupproperties":
		ADMIN_properties::goUp($cid,$option);
		
		break;
	case "unpublish":
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
	case "saveBasemapContent":		
		ADMIN_basemap::saveBasemapContent(true,$option);				
		break;
	case "deleteBasemapContent":		
		ADMIN_basemap::deleteBasemapContent($cid,$option);				
		break;	
		
	case "editBasemapContent":
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent($cid[0],$option);
		break;
		
	case "newBasemapContent":
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent(0,$option);
		
		break;
		
	case "cancelBasemapContent":
	case "listBasemapContent":
		TOOLBAR_basemap::_LISTBASEMAPCONTENT($cid[0]);
		ADMIN_basemap::listBasemapContent($cid[0],$option);
		
		break;
	
	case "saveBasemap":		
		ADMIN_basemap::saveBasemap(true,$option);				
		break;
	case "deleteBasemap":		
		ADMIN_basemap::deleteBasemap($cid,$option);				
		break;	
		
	case "editBasemap":
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap($cid[0],$option);
		break;
		
	case "newBasemap":
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap(0,$option);
		
		break;

	
	case "cancelBasemap":
		$mainframe->redirect("index.php?option=$option&task=listBasemap" );
		break;
	case "listBasemap":
		TOOLBAR_basemap::_LISTBASEMAP();
		ADMIN_basemap::listBasemap($option);
		
		break;
		
	case "savePropertiesValues":		
		ADMIN_properties::savePropertiesValues(true,$option);				
		break;
	case "deletePropertiesValues":		
		ADMIN_properties::deletePropertiesValues($cid,$option);				
		break;	
		
	case "editPropertiesValues":
		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues($cid[0],$option);
		break;
		
	case "newPropertiesValues":
		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues(0,$option);
		
		break;
		
	case "cancelPropertiesValues":
		$cid[0] = JRequest::getVar('properties_id');
	case "listPropertiesValues":
		TOOLBAR_properties::_LISTPROPERTIESVALUES();
		ADMIN_properties::listPropertiesValues($cid[0],$option);		
		break;
	
			
	case "saveProperties":		
		ADMIN_properties::saveProperties(true,$option);				
		break;
	case "deleteProperties":		
		ADMIN_properties::deleteProperties($cid,$option);				
		break;	
		
	case "editProperties":
		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties($cid[0],$option);
		break;
		
	case "newProperties":
		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties(0,$option);
		
		break;
		
	case "cancelProperties":
	case "listProperties":
		TOOLBAR_properties::_LISTPROPERTIES();
		ADMIN_properties::listProperties($option);
		
		break;
	
	case "savePerimeter":		
		ADMIN_perimeter::savePerimeter(true,$option);				
		break;
	case "deletePerimeter":		
		ADMIN_perimeter::deletePerimeter($cid,$option);				
		break;	
		
	case "editPerimeter":
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter($cid[0],$option);
		break;
		
	case "newPerimeter":
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter(0,$option);
		
		break;
		
	case "cancelPerimeter":
	case "listPerimeter":
		TOOLBAR_perimeter::_LISTPERIMETER();
		ADMIN_perimeter::listPerimeter($option);
		
		break;
	
	case "saveProduct":		
		ADMIN_product::saveProduct(true,$option);				
		break;
	case "deleteProduct":		
		ADMIN_product::deleteProduct($cid,$option);				
		break;	
		
	case "editProduct":
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct($cid[0],$option);
		break;
		
	case "newProduct":
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct(0,$option);
		
		break;
		
	case "cancelProduct":
	case "listProduct":
		TOOLBAR_product::_LISTPROUCT();
		ADMIN_product::listProduct($option);
		
		break;
	default:
		$mainframe->enqueueMessage($task,"INFO");		
		break;
}

?>