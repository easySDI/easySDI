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

JHTML::_('stylesheet', 'easysdi_shop.css', 'administrator/components/com_easysdi_shop/templates/css/');
JHTML::_('stylesheet', 'easysdi.css', 'templates/easysdi/css/');
JHTML::_('stylesheet', 'MultiSelect.css', 'administrator/components/com_easysdi_shop/templates/css/');


global $mainframe;
$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch($task){

	case "proxy":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		SITE_proxy::proxy();	
		break;
	
	case "orderReport":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		SITE_cpanel::orderReport($cid[0], false,false);
		break;
	case "listOrders":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_cpanel::_LISTORDERS();
		ADMIN_cpanel::listOrders();
		break;
	/*case "editOrder":
		TOOLBAR_cpanel::_EDITORDERS();	
		ADMIN_cpanel::editOrder($cid[0],$option);
		break;*/
	case "deleteOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_cpanel::deleteOrder($cid,$option);
		//$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "saveOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_cpanel::saveOrder($option);
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "cancelOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
			
	case "saveMDTABS":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDTabs($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
		break;
	case "cancelMDTabs":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
		break;
	case "newMetadataTab":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITMETADATATAB();	
		ADMIN_metadata::editMDTabs(0,$option);
		break;
	case "editMetadataTab":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITMETADATATAB();	
		ADMIN_metadata::editMDTabs($cid[0],$option);
		break;
		
	case "listMetadataTabs":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTMDTABS();
		ADMIN_metadata::listMetadataTabs($option);
		break;
	case "orderupMetadataTabs":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goUpMetadataTabs($cid,$option);
	break;
	case "orderdownMetadataTabs":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goDownMetadataTabs($cid,$option);
	break;
	case "saveOrderMetadataTabs":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveOrderMetadataTabs($cid, $option);
	break;
	case "orderMetadataTabs":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::orderMetadataTabs($cid, $option);
	break;
		
	case "deleteMetadataStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::deleteMDStandard($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	
	case "deleteMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::deleteMDStandardClasses($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
		
	case "ctrlPanelShop":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		HTML_ctrlpanel::ctrlPanelShop($option);
		break;
	case "ctrlPanelBaseMap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listBasemap" );
		break;				

	case "ctrlPanelMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		HTML_ctrlpanel::ctrlPanelMetadata($option);
		break;

	case "ctrlPanelLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listLocation" );
		break;
		
	case "ctrlPanelPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
		break;
	case "ctrlPanelProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		
		break;

	case "ctrlPanelProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listProperties" );					
		break;
			
	case "editMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITSTANDARDCLASSES();	
		ADMIN_metadata::editStandardClasses($cid[0],$option);
	break;
	case "newMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITSTANDARDCLASSES();
		ADMIN_metadata::editStandardClasses(0,$option);
	break;
	
	case "saveMDStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDStandardClasses($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
	case "cancelMDStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
	case "listMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTSTANDARDCLASSES();;
		ADMIN_metadata::listStandardClasses($option);
	break;
	case "orderupMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goUpMetadataStandardClasses($cid,$option);
	break;
	case "orderdownMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goDownMetadataStandardClasses($cid,$option);
	break;
	case "saveOrderMetadataStandardClasses":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveOrderMetadataStandardClasses($cid, $option);
	break;
	
	case "editMetadataStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITSTANDARD();		
		ADMIN_metadata::editStandard($cid[0],$option);
	break;
	case "newMetadataStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITSTANDARD();
		ADMIN_metadata::editStandard(0,$option);
	break;
	
	case "saveMDStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDStandard($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	case "cancelMDStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	case "listMetadataStandard":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTSTANDARD();;
		ADMIN_metadata::listStandard($option);
	break;
	
	
	
	
	
	
	case "editMetadataExt":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITEXT();;		
		ADMIN_metadata::editExt($cid[0],$option);
	break;
	case "newMetadataExt":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITEXT();
		ADMIN_metadata::editExt(0,$option);
	break;
	
	case "saveMDExt":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDExt($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
		break;
	case "cancelMDExt":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
		break;
	case "listMetadataExt":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTEXT();
		ADMIN_metadata::listExt($option);
	break;
	
	
	
	
	
	
	
	case "editMetadataLocfreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITLOCFREETEXT();		
		ADMIN_metadata::editLocfreetext($cid[0],$option);
	break;
	case "newMetadataLocfreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITLOCFREETEXT();
		ADMIN_metadata::editLocfreetext(0,$option);
	break;
	
	case "saveMDLocfreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDLocfreetext($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
		break;
	case "cancelMDLocfreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
		break;
	case "listMetadataLocfreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTLOCFREETEXT();
		ADMIN_metadata::listLocfreetext($option);
	break;
	
	case "deleteMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::deleteMetadataClass($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
		
	case "editMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITCLASS();
		ADMIN_metadata::editClass($cid[0],$option);
	break;
	case "newMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITClass();
		ADMIN_metadata::editClass(0,$option);
	break;
	
	case "saveMDClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDClass($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
	case "cancelMDClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
	case "listMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTClass();
		ADMIN_metadata::listClass($option);
	break;
	case "orderupMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goUpMetadataClass($cid,$option);
		
	break;
	case "orderdownMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::goDownMetadataClass($cid,$option);		
	break;
	case "saveOrderMetadataClass":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveOrderMetadataClass($cid, $option);
	break;
	
	case "editMetadataFreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITFREETEXT();
		ADMIN_metadata::editFreetext($cid[0],$option);
	break;
	case "newMetadataFreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_EDITFREETEXT();
		ADMIN_metadata::editFreetext(0,$option);
	break;
	
	case "saveMDFreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDFreetext($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
		break;
	case "cancelMDFreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
		break;
	case "listMetadataFreetext":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTFREETEXT();
		ADMIN_metadata::listFreetext($option);
	break;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	case "editMetadataList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTEDIT();
		ADMIN_metadata::editList($cid[0],$option);
	break;
	
	case "newMetadataList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTEDIT();
		ADMIN_metadata::editList(0,$option);
	break;
	
	case "saveMDList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::saveMDList($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
		break;
	case "cancelMDList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
		break;		
	case "listMetadataList":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTLIST();
		ADMIN_metadata::listList($option);
	break;

	
	
	
	case "deleteMetadataList":
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_metadata::deleteMetadataList($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );	
		break;
	case "deleteMetadataListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$list_id = JRequest::getVar ('list_id', 0 );
		ADMIN_metadata::deleteMetadataListContent($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
	break;	
	case "editMetadataListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$list_id = JRequest::getVar ('list_id', 0 );
		TOOLBAR_metadata::_LISTEDITCONTENT();
		ADMIN_metadata::editListContent($cid[0],$option,$list_id);
	break;
	
	case "newMetadataListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$list_id = JRequest::getVar ('list_id', 0 );
		TOOLBAR_metadata::_LISTEDITCONTENT();
		ADMIN_metadata::editListContent(0,$option,$list_id);
	break;
	
	case "saveMDListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$list_id = JRequest::getVar ('list_id', 0 );
		ADMIN_metadata::saveMDListContent($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
		break;
	case "cancelMDListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$list_id = JRequest::getVar ('list_id', 0 );
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
		break;		
	case "listMetadataListContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTLISTCONTENT();
		ADMIN_metadata::listListContent($cid[0],$option);
	break;
	
	
	
	
	
	
	case "listMetadataDate":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_metadata::_LISTDATE();
		ADMIN_metadata::listDate($option);
	break;
	
	case "orderdownPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::goDown($cid,$option);
		
		break;
	case "orderupPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::goUp($cid,$option);
		
		break;
	case "saveOrderPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::saveOrderPropertiesValues($cid, $properties_id, $option);
		
		break;
	case "unpublish":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
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
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
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
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_basemap::saveBasemapContent(true,$option);				
		break;
	case "deleteBasemapContent":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_basemap::deleteBasemapContent($cid,$option);				
		break;	
		
	case "editBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent($cid[0],$option);
		break;
		
	case "newBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_EDITBASEMAPCONTENT();
		ADMIN_basemap::editBasemapContent(0,$option);
		
		break;
		
	case "cancelBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
	case "orderupbasemapcontent":
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		
		ADMIN_basemap::orderUpBasemapContent($cid[0],JRequest::getVar('basemap_def_id'));
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
	case "orderdownbasemapcontent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_basemap::orderDownBasemapContent($cid[0],JRequest::getVar('basemap_def_id'));
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
		
		
	case "listBasemapContent":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_LISTBASEMAPCONTENT($cid[0]);
		ADMIN_basemap::listBasemapContent($cid[0],$option);
		
		break;
	
	case "saveBasemap":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_basemap::saveBasemap(true,$option);				
		break;
	case "deleteBasemap":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_basemap::deleteBasemap($cid,$option);				
		break;	
		
	case "editBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap($cid[0],$option);
		break;
		
	case "newBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_EDITBASEMAP();
		ADMIN_basemap::editBasemap(0,$option);
		
		break;
	
	case "cancelBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->redirect("index.php?option=$option&task=listBasemap" );
		break;
	case "listBasemap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_basemap::_LISTBASEMAP();
		ADMIN_basemap::listBasemap($option);
		
		break;
		
	case "savePropertiesValues":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::savePropertiesValues(true,$option);				
		break;
	case "deletePropertiesValues":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::deletePropertiesValues($cid,$option);				
		break;	
		
	case "editPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues($cid[0],$option);
		break;
		
	case "newPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_EDITPROPERTIESVALUES();
		ADMIN_properties::editPropertiesValues(0,$option);
		
		break;
		
	case "cancelPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$cid[0] = JRequest::getVar('properties_id');
	case "listPropertiesValues":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_LISTPROPERTIESVALUES();
		ADMIN_properties::listPropertiesValues($cid[0],$option);		
		break;
	case "saveProperties":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::saveProperties(true,$option);				
		break;
	case "deleteProperties":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::deleteProperties($cid,$option);				
		break;	
		
	case "editProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties($cid[0],$option);
		break;
		
	case "newProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_EDITPROPERTIES();
		ADMIN_properties::editProperties(0,$option);
		
		break;
		
	case "cancelProperties":
	case "listProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_properties::_LISTPROPERTIES();
		ADMIN_properties::listProperties($option);
		
		break;
	case "orderupProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::goUpProperties($cid,$option);
	break;
	case "orderdownProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::goDownProperties($cid,$option);
	break;
	case "saveOrderProperties":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_properties::saveOrderProperties($cid,$option);
	break;
		
		
		
case "saveLocation":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_location::saveLocation(true,$option);				
		break;
	
	case "copyLocation":	
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_location::copyLocation($cid,$option);
		break;
		
	case "deleteLocation":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_location::deleteLocation($cid,$option);				
		break;	
		
	case "editLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation($cid[0],$option);
		break;
		
	case "newLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation(0,$option);
		
		break;
		
	case "cancelLocation":
	case "listLocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_location::_LISTLOCATION();
		ADMIN_location::listLocation($option);
		
		break;
		
		
		
		
		
		
		
		
		
	case "savePerimeter":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::savePerimeter(true,$option);				
		break;
	
	case "copyPerimeter":	
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::copyPerimeter($cid,$option);
		break;
		
	case "deletePerimeter":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::deletePerimeter($cid,$option);				
		break;	
		
	case "editPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter($cid[0],$option);
		break;
		
	case "newPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_perimeter::_EDITPERIMETER();
		ADMIN_perimeter::editPerimeter(0,$option);
		
		break;
		
	case "cancelPerimeter":
	case "listPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_perimeter::_LISTPERIMETER();
		ADMIN_perimeter::listPerimeter($option);
		
		break;
	case "orderupPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::goupPerimeter($cid, $option);
		
		break;
	case "orderdownPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::godownPerimeter($cid, $option);
		
		break;
	case "saveOrderPerimeter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_perimeter::saveOrderPerimeter($cid, $option);
		
		break;
		
	case "saveProductMetadata":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_product::saveProductMetadata($option);
		$mainframe->redirect("index.php?option=$option&task=listProduct");				
		break;
		
	case "saveProduct":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_product::saveProduct($option);				
		break;
	case "deleteProduct":		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		ADMIN_product::deleteProduct($cid,$option);				
		break;	

	case "editProductMetadata2":	
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_EDITPRODUCTMETADATA();
		ADMIN_product::editProductMetadata2($cid[0],$option);
		break;
	
	
		
		
	case "editMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		TOOLBAR_metadata::_EDITMETADATA();
		ADMIN_metadata::editMetadata($cid[0],$option);
		break;
	case "saveMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		
		ADMIN_metadata::saveMetadata($option);
		break;
	
	case "cancelMetadata":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_LISTPRODUCT();
		ADMIN_product::listProduct($option);		
		break;
		break;
		
	case "editProductMetadata":	
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_EDITPRODUCTMETADATA();
		ADMIN_product::editProductMetadata($cid[0],$option);
		break;
		
	case "editProduct":			
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct($cid[0],$option);
		break;
		
	case "newProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct(0,$option);		
		break;
		
		
	case "cancelProductMetadata":
	case "cancelProduct":
	case "listProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		TOOLBAR_product::_LISTPRODUCT();
		ADMIN_product::listProduct($option);		
		break;
	default:
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'product.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'perimeter.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'location.admin.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'basemap.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.toolbar.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.easysdi.class.php');
		
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop FrontEnd 
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'proxy.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		
		//Core FrontEnd
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		$mainframe->enqueueMessage($task,"INFO");
		HTML_ctrlpanel::ctrlPanelShop($option);		
		break;
}

?>