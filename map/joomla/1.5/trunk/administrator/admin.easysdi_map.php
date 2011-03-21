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
	case 'overlayCtrlPanel':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlPanel'.DS.'overlay.ctrlpanel.admin.easysdi_map.html.php');
		HTML_overlayctrlpanel::overlayCtrlPanel($option);
		break;
	case 'rightCtrlPanel':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'ctrlPanel'.DS.'right.ctrlpanel.admin.easysdi_map.html.php');
		HTML_rightctrlpanel::rightCtrlPanel($option);
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
		$mainframe->redirect("index.php?option=$option&task=projection");
		break;
	case 'saveProjection':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'projection.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'projection.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'projection.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'projection.admin.easysdi_map.php');
		ADMIN_projection::saveProjection($option);
		$mainframe->redirect("index.php?option=$option&task=projection");
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
		$mainframe->redirect("index.php?option=$option&task=overlay" );
		break;
	case 'deleteOverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		ADMIN_overlay::deleteOverlay($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=overlay" );
		break;
	case 'orderupoverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_overlay::orderUpOverlay($cid[0], "#__sdi_overlay");
		$mainframe->redirect("index.php?option=$option&task=overlay&order_field=".$order_field );
		break;
	case 'orderdownoverlay':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_overlay::orderDownOverlay($cid[0], "#__sdi_overlay");
		$mainframe->redirect("index.php?option=$option&task=overlay&order_field=".$order_field );
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
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
		break;
	case 'saveOverlayGroup';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		ADMIN_overlay::saveOverlayGroup($option);
		$mainframe->redirect("index.php?option=$option&task=overlayGroup");
		break;
	case 'orderupoverlaygroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_overlay::orderUpOverlay($cid[0], "#__easysdi_overlay_group");
		$mainframe->redirect("index.php?option=$option&task=overlayGroup&order_field=".$order_field );
		break;
	case 'orderdownoverlaygroup':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'overlay.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'overlay.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'overlay.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'overlay.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_overlay::orderDownOverlay($cid[0],"#__easysdi_overlay_group");
		$mainframe->redirect("index.php?option=$option&task=overlayGroup&order_field=".$order_field );
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
		$mainframe->redirect("index.php?option=$option&task=resultGrid");
		break;
	case 'saveResultGrid';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'resultgrid.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'resultgrid.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'resultgrid.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'resultgrid.admin.easysdi_map.php');
		ADMIN_resultgrid::saveResultGrid($option);
		$mainframe->redirect("index.php?option=$option&task=resultGrid");
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
		$mainframe->redirect("index.php?option=$option&task=simpleSearch");
		break;
	case 'saveSimpleSearch';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		ADMIN_simplesearch::saveSimpleSearch($option);
		$mainframe->redirect("index.php?option=$option&task=simpleSearch");
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
		$mainframe->redirect("index.php?option=$option&task=additionalFilter");
		break;
	case 'saveAdditionalFilter';
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'simplesearch.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'simplesearch.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'simplesearch.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'simplesearch.admin.easysdi_map.php');
		ADMIN_simplesearch::saveAdditionalFilter($option);
		$mainframe->redirect("index.php?option=$option&task=additionalFilter");
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
		$mainframe->redirect("index.php?option=$option&task=searchLayer");
		break;
	case 'saveSearchLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'searchlayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'searchlayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'searchlayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'searchlayer.admin.easysdi_map.php');
		ADMIN_searchlayer::saveSearchlayer($option);
		$mainframe->redirect("index.php?option=$option&task=searchLayer");
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
		$mainframe->redirect("index.php?option=$option&task=precision");
		break;
	case 'savePrecision':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'precision.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'precision.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'precision.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'precision.admin.easysdi_map.php');
		ADMIN_precision::savePrecision($option);
		$mainframe->redirect("index.php?option=$option&task=precision");
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
		$mainframe->redirect("index.php?option=$option&task=featureType");
		break;
	case 'saveFeatureType':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'featuretype.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'featuretype.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'featuretype.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'featuretype.admin.easysdi_map.php');
		ADMIN_featuretype::saveFeatureType($option);
		$mainframe->redirect("index.php?option=$option&task=featureType");
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
	 * Service account
	 */
	case 'serviceAccount':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'serviceaccount.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'serviceaccount.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'serviceaccount.admin.easysdi_map.php');
		TOOLBAR_serviceaccount::_EDIT();
		$id = JRequest::getVar ('partner_id', '' );
		ADMIN_serviceaccount::editServiceAccount($id,$option);
		break;
	case 'saveServiceAccount':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'serviceaccount.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'serviceaccount.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'serviceaccount.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'serviceaccount.admin.easysdi_map.php');
		ADMIN_serviceaccount::saveServiceAccount($option);
		$mainframe->redirect("index.php?option=$option&task=rightCtrlPanel");
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
		$mainframe->redirect("index.php?option=$option&task=comment");
		break;
	case 'saveComment':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'comment.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'comment.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'comment.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'comment.admin.easysdi_map.php');
		ADMIN_comment::saveComment($option);
		$mainframe->redirect("index.php?option=$option&task=comment");
		break;
	/**
	 * Base layer
	 */
	case 'baseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		TOOLBAR_baselayer::_LIST();
		ADMIN_baselayer::listBaseLayer($option);
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
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
		break;
	case 'saveBaseLayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		ADMIN_baselayer::saveBaseLayer($option);
		$mainframe->redirect("index.php?option=$option&task=baseLayer");
		break;
	case 'orderupbasemaplayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_baselayer::orderUpBasemapLayer($cid[0]);
		$mainframe->redirect("index.php?option=$option&task=baseLayer&order_field=".$order_field );
		break;
	case 'orderdownbasemaplayer':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'baselayer.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'baselayer.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'baselayer.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'baselayer.admin.easysdi_map.php');
		$cid = JRequest::getVar ('cid', array(0) );
		$order_field = JRequest::getVar ('order_field','') ;
		ADMIN_baselayer::orderDownBasemapLayer($cid[0]);
		$mainframe->redirect("index.php?option=$option&task=baseLayer&order_field=".$order_field );
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
		$mainframe->redirect("index.php?option=$option&task=geolocation");
		break;
	case 'saveGeolocation':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'geolocation.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'geolocation.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'geolocation.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'geolocation.admin.easysdi_map.php');
		ADMIN_geolocation::saveGeolocation($option);
		$mainframe->redirect("index.php?option=$option&task=geolocation");
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
		$mainframe->redirect("index.php?option=$option&task=display");
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
		$mainframe->redirect("index.php?option=$option&task=annotationStyle");
		break;
	case 'saveAnnotationStyle':
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'annotationstyle.admin.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'annotationstyle.class.easysdi_map.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'annotationstyle.toolbar.easysdi_map.html.php');
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'annotationstyle.admin.easysdi_map.php');
		ADMIN_annotationstyle::saveAnnotationStyle($option);
		$mainframe->redirect("index.php?option=$option&task=annotationStyle");
		break;
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