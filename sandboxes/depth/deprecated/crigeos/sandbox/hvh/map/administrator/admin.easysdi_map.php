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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resources.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resources.admin.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resources.admin.easysdi.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');

JHTML::_('stylesheet', 'common_easysdi_admin.css', 'administrator/components/com_easysdi_core/templates/css/');
JHTML::_('stylesheet', 'easysdi.css', 'templates/easysdi/css/');

global $mainframe;

$language =& JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_map');

$task = JRequest::getVar('task');
$option = JRequest::getVar('option');

switch($task)
{
	default:
	case 'ctrlPanel':
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
	case 'simplesearchCtrlPanel':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlPanel'.DS.'simplesearch.ctrlpanel.admin.easysdi_map.html.php');
		HTML_simplesearchctrlpanel::simplesearchCtrlPanel($option);
		break;
	case 'searchlayerCtrlPanel':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlPanel'.DS.'searchlayer.ctrlpanel.admin.easysdi_map.html.php');
		HTML_searchlayerctrlpanel::searchlayerCtrlPanel($option);
		break;
	case 'rootCore':
		$mainframe->redirect("index.php?option=com_easysdi_core");
		break;

	/**
	 * Projection
	 */
	case 'projection':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'projection.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'projection.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		TOOLBAR_projection::_LIST();
		ADMIN_projection::listProjection($option);
		break;
	case 'editProjection':
	case 'newProjection':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'projection.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'projection.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'projection.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		TOOLBAR_projection::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_projection::editProjection($cid[0], $option);
		break;
	case 'deleteProjection':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'projection.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'projection.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'projection.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_projection::deleteProjection($cid, $option);
		break;
	case 'saveProjection':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'projection.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'projection.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'projection.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		ADMIN_projection::saveProjection($option);
		break;
	case "cancelProjection":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'projection.class.easysdi_map.php');
		ADMIN_projection::cancelProjection($option);		
		break;
	/**
	 * Overlay
	 */
	case 'overlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAY();
		ADMIN_overlay::listOverlay($option);
		break;
	case 'editOverlay':
	case 'newOverlay';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_EDITOVERLAY();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::editOverlay($cid[0], $option);
		break;
	case 'saveOverlay';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		ADMIN_overlay::saveOverlay($option);
		break;
	case 'deleteOverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::deleteOverlay($cid,$option);
		break;
	case 'orderupoverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::orderUpOverlay($option,$cid[0]);
		break;
	case 'orderdownoverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::orderDownOverlay($option,$cid[0]);
		break;
	case 'saveOrderOverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		ADMIN_overlay::saveOrderOverlay($option,$cid, $order);
		break;
	case "cancelOverlay":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		ADMIN_overlay::cancelOverlay($option);		
		break;
		
	case "overlay_publish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAY();
		ADMIN_overlay::changeContent(1);
		
		break;
	case "overlay_unpublish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAY();
		ADMIN_overlay::changeContent(0);
		break;
	/**
	 * Overlay Group
	 */
	case 'overlayGroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAYGROUP();
		ADMIN_overlay::listOverlayGroup($option);
		break;
	case 'editOverlayGroup' :
	case 'newOverlayGroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_EDITOVERLAYGROUP();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::editOverlayGroup($cid[0], $option);
		break;
	case 'deleteOverlayGroup';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::deleteOverlayGroup($cid, $option);
		break;
	case 'saveOverlayGroup';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		ADMIN_overlay::saveOverlayGroup($option);
		break;
	case 'orderupoverlaygroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::orderUpOverlayGroup($option,$cid[0]);
		break;
	case 'orderdownoverlaygroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::orderDownOverlayGroup($option,$cid[0]);
		break;
	case 'saveOrderOverlayGroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		ADMIN_overlay::saveOrderOverlayGroup($option,$cid, $order);
		break;
	case "cancelOverlayGroup":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		ADMIN_overlay::cancelOverlayGroup($option);		
		break;
	case "overlaygroup_publish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAYGROUP();
		ADMIN_overlay::changeGroupContent(1);
		
		break;
	case "overlaygroup_unpublish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		TOOLBAR_overlay::_LISTOVERLAYGROUP();
		ADMIN_overlay::changeGroupContent(0);
		break;	
		
		
	/**
	 * Result Grid
	 */
	case 'resultGrid':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resultgrid.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resultgrid.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		TOOLBAR_resultgrid::_LISTRESULTGRID();
		ADMIN_resultgrid::listResultGrid($option);
		break;
	case 'editResultGrid' :
	case 'newResultGrid':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resultgrid.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'resultgrid.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resultgrid.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		TOOLBAR_resultgrid::_EDITRESULTGRID();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_resultgrid::editResultGrid($cid[0], $option);
		break;
	case 'deleteResultGrid';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resultgrid.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'resultgrid.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resultgrid.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_resultgrid::deleteResultGrid($cid, $option);
		break;
	case 'saveResultGrid';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resultgrid.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'resultgrid.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resultgrid.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		ADMIN_resultgrid::saveResultGrid($option);
		break;
	case "cancelResultGrid":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'resultgrid.class.easysdi_map.php');
		ADMIN_resultgrid::cancelResultGrid($option);		
		break;
	/**
	 * Simple search type
	 */
	case 'simpleSearch':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		TOOLBAR_simplesearch::_LISTSIMPLESEARCH();
		ADMIN_simplesearch::listSimpleSearch($option);
		break;
	case 'editSimpleSearch' :
	case 'newSimpleSearch':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		TOOLBAR_simplesearch::_EDITSIMPLESEARCH();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_simplesearch::editSimpleSearch($cid[0], $option);
		break;
	case 'deleteSimpleSearch';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_simplesearch::deleteSimpleSearch($cid, $option);
		break;
	case 'saveSimpleSearch';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		ADMIN_simplesearch::saveSimpleSearch($option);
		break;
	case "cancelSimpleSearch":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		ADMIN_simplesearch::cancelSimpleSearch($option);		
		break;
	/**
	 * Simple search additional filters
	 */
	case 'additionalFilter':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		TOOLBAR_addfilter::_LISTADDFILTER();
		ADMIN_simplesearch::listAdditionalFilter($option);
		break;
	case 'editAdditionalFilter':
	case 'newAdditionalFilter':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		TOOLBAR_addfilter::_EDITADDFILTER();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_simplesearch::editAdditionalFilter($cid[0], $option);
		break;
	case 'deleteAdditionalFilter';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_simplesearch::deleteAdditionalFilter($cid, $option);
		break;
	case 'saveAdditionalFilter';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		ADMIN_simplesearch::saveAdditionalFilter($option);
		break;
	case "cancelAdditionalFilter":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		ADMIN_simplesearch::cancelAdditionalFilter($option);		
		break;
	/**
	 * Search layer : main result grid
	 */
	case 'searchLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchlayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchlayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		TOOLBAR_searchlayer::_LIST();
		ADMIN_searchlayer::listSearchlayer($option);
		break;
	case 'editSearchLayer':
	case 'newSearchLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchlayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchlayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchlayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		TOOLBAR_searchlayer::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_searchlayer::editSearchlayer($cid[0], $option);
		break;
	case 'deleteSearchLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchlayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchlayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchlayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_searchlayer::deleteSearchlayer($cid, $option);
		break;
	case 'saveSearchLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchlayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchlayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchlayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		ADMIN_searchlayer::saveSearchlayer($option);
		break;
	case "cancelSearchLayer":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchlayer.class.easysdi_map.php');
		ADMIN_searchlayer::cancelSearchLayer($option);		
		break;
	/**
	 * Precision
	 */
	case 'precision':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'precision.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'precision.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		TOOLBAR_precision::_LIST();
		ADMIN_precision::listPrecision($option);
		break;
	case 'editPrecision' :
	case 'newPrecision':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'precision.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'precision.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'precision.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		TOOLBAR_precision::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_precision::editPrecision($cid[0], $option);
		break;
	case 'deletePrecision':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'precision.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'precision.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'precision.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_precision::deletePrecision($cid, $option);
		break;
	case 'savePrecision':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'precision.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'precision.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'precision.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		ADMIN_precision::savePrecision($option);
		break;
	case "cancelPrecision":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'precision.class.easysdi_map.php');
		ADMIN_precision::cancelPrecision($option);		
		break;
	/**
	 * Feature type
	 */
	case 'featureType':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'featuretype.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'featuretype.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		TOOLBAR_featuretype::_LIST();
		ADMIN_featuretype::listFeatureType($option);
		break;
	case 'editFeatureType' :
	case 'newFeatureType':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'featuretype.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'featuretype.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'featuretype.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		TOOLBAR_featuretype::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_featuretype::editFeatureType($cid[0], $option);
		break;
	case 'deleteFeatureType':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'featuretype.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'featuretype.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'featuretype.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_featuretype::deleteFeatureType($cid, $option);
		break;
	case 'saveFeatureType':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'featuretype.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'featuretype.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'featuretype.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		ADMIN_featuretype::saveFeatureType($option);
		break;
	case "cancelFeatureType":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'featuretype.class.easysdi_map.php');
		ADMIN_featuretype::cancelFeatureType($option);		
		break;
	/**
	 * Profile
	 */
	case 'profile':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi_map.php');
		TOOLBAR_profile::_LIST();
		ADMIN_profile::listProfile($option);
		break;
	case 'editProfile':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi_map.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'accountprofile.easysdi.class.php');
		TOOLBAR_profile::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_profile::editProfile($cid[0], $option);
		break;
	case 'saveProfile':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'profile.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'profile.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'profile.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'profile.admin.easysdi_map.php');
		ADMIN_profile::saveProfile($option);
		$mainframe->redirect("index.php?option=$option&task=profile");
		break;
	/**
	 * Comment feature type
	 */
	case 'comment':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'comment.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'comment.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		TOOLBAR_comment::_LIST();
		ADMIN_comment::listComment($option);
		break;
	case 'editComment' :
	case 'newComment':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'comment.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'comment.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'comment.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		TOOLBAR_comment::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_comment::editComment($cid[0], $option);
		break;
	case 'deleteComment':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'comment.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'comment.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'comment.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_comment::deleteComment($cid, $option);
		break;
	case 'saveComment':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'comment.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'comment.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'comment.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		ADMIN_comment::saveComment($option);
		break;
	case "cancelComment":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'comment.class.easysdi_map.php');
		ADMIN_comment::cancelComment($option);		
		break;
	/**
	 * Base layer
	 */
	case 'baseMap':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baseMap::_EDIT();
		ADMIN_baselayer::editbaseMap($option);
		break;
	case 'saveBaseMap':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		ADMIN_baselayer::saveBaseMap($option);
		break;
	case "cancelBaseMap":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		ADMIN_baselayer::cancelBaseMap($option);		
		break;
	case 'baseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baselayer::_LIST();
		ADMIN_baselayer::listBaseLayer($option);
		break;
		
	case "baselayer_publish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baselayer::_LIST();
		ADMIN_baselayer::changeContent(1);
		
		break;
	case "baselayer_unpublish":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baselayer::_LIST();
		ADMIN_baselayer::changeContent(0);
		break;
	case 'editBaseLayer' :
	case 'newBaseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baselayer::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_baselayer::editBaseLayer($cid[0], $option);
		break;
	case 'deleteBaseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_baselayer::deleteBaseLayer($cid, $option);
		break;
	case 'saveBaseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		ADMIN_baselayer::saveBaseLayer($option);
		break;
	case 'orderupbasemaplayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_baselayer::orderUpBasemapLayer($option,$cid[0]);
		break;
	case 'orderdownbasemaplayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_baselayer::orderDownBasemapLayer($option,$cid[0]);
		break;
	case 'saveOrderBaseMapLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		ADMIN_baselayer::saveOrderBaseMapLayer($option,$cid, $order);
		break;
	case "cancelBaseLayer":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		ADMIN_baselayer::cancelBaseLayer($option);		
		break;
	/**
	 * Geolocation
	 */
	case 'geolocation':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'geolocation.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'geolocation.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		TOOLBAR_geolocation::_LIST();
		ADMIN_geolocation::listGeolocation($option);
		break;
	case 'editGeolocation' :
	case 'newGeolocation':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'geolocation.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'geolocation.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'geolocation.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		TOOLBAR_geolocation::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_geolocation::editGeolocation($cid[0], $option);
		break;
	case 'deleteGeolocation':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'geolocation.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'geolocation.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'geolocation.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_geolocation::deleteGeolocation($cid, $option);
		break;
	case 'saveGeolocation':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'geolocation.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'geolocation.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'geolocation.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		ADMIN_geolocation::saveGeolocation($option);
		break;
	case "cancelGeolocation":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'geolocation.class.easysdi_map.php');
		ADMIN_geolocation::cancelGeolocation($option);		
		break;
	/**
	 * Display options
	 */
	case 'display':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'display.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'display.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'display.admin.easysdi_map.php');
		TOOLBAR_display::_LIST();
		ADMIN_display::listDisplay($option);
		break;
	case 'saveDisplay';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'display.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'display.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'display.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'display.admin.easysdi_map.php');
		ADMIN_display::saveDisplay($option);
		break;
	/**
	 * Annotation styles
	 */
	case 'annotationStyle':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'annotationstyle.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'annotationstyle.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		TOOLBAR_annotationstyle::_LIST();
		ADMIN_annotationstyle::listAnnotationStyle($option);
		break;
	case 'editAnnotationStyle' :
	case 'newAnnotationStyle':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'annotationstyle.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'annotationstyle.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'annotationstyle.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		TOOLBAR_annotationstyle::_EDIT();
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_annotationstyle::editAnnotationStyle($cid[0], $option);
		break;
	case 'deleteAnnotationStyle':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'annotationstyle.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'annotationstyle.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'annotationstyle.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_annotationstyle::deleteAnnotationStyle($cid, $option);
		break;
	case 'saveAnnotationStyle':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'annotationstyle.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'annotationstyle.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'annotationstyle.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		ADMIN_annotationstyle::saveAnnotationStyle($option);
		break;
	case "cancelAnnotationStyle":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'annotationstyle.class.easysdi_map.php');
		ADMIN_annotationstyle::cancelAnnotationStyle($option);		
		break;
	/**
	 * Resource
	 */
	case "editResource":
		TOOLBAR_resources::_EDIT();
		ADMIN_resources::editResource($option);
		break;
	case "saveResource":
		ADMIN_resources::saveResource($option);
		TOOLBAR_resources::_DEFAULT();
		$mainframe->redirect("index.php?option=$option");
		break;
	
}
?>