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
jimport("joomla.utilities.date");

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'model'.DS.'list.easysdi.class.php');

global $mainframe;
$language=&JFactory::getLanguage();
$language->load('com_easysdi_shop', JPATH_ADMINISTRATOR);
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');
$order_id = JRequest::getVar('order_id');
$myFavoritesFirst = JRequest::getVar('myFavoritesFirst');
$simpleSearchCriteria = JRequest::getVar('simpleSearchCriteria');
$limitstart= JRequest::getVar('limitstart');
$limit= JRequest::getVar('limit');
$furnisher_id= JRequest::getVar('furnisher_id');
$freetextcriteria = JRequest::getVar('freetextcriteria');

$cid = JRequest::getVar ('cid');
if (!is_array( $cid )) {
	$cid = array($cid);
}

switch($task){
	/*****************************************************************************************************************************
	 * Shop
	 *****************************************************************************************************************************/
	default :
 	case "shop":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
					
		SITE_shop::order();
		break;
		
	case "loadListForPerim":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'perimList.easysdi.html.php');
		SITE_listPerim::show();
	break;
	
	case "redirectFromListForPerim":
		$Itemid = JRequest::getVar('Itemid');
		$option = JRequest::getVar('option');
		$perimeter_id = JRequest::getVar('perimeter_id');
		$step = JRequest::getVar('step');
		
		$perimeterContent = JRequest::getVar('perimeterContent');
		$mainframe->setUserState('perimeterContent',$perimeterContent);
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'perimList.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&view=shop&step=$step&perimeter_id=$perimeter_id&Itemid=$Itemid"), false));
	break;
		
	case "downloadAvailableProduct":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_shop::downloadAvailableProduct($cid[0]);
		break;
	
	case "doDownloadAvailableProduct":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_shop::doDownloadAvailableProduct($cid[0]);
		break;

	/*****************************************************************************************************************************
	 * Proxy
	 *****************************************************************************************************************************/
	case "proxy":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');			
			
		SITE_Proxy::proxy();
		break;
	
	/*****************************************************************************************************************************
	 * Product
	 *****************************************************************************************************************************/
	case "deleteProduct":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
							
				SITE_shop::deleteProduct();

	/*****************************************************************************************************************************
	 * Order
	 *****************************************************************************************************************************/
	case "order":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
			
		SITE_shop::order();
		break;
		
	case "orderReport":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		
		SITE_cpanel::orderReport($cid[0], true, false);
		break;
		
	case "orderReportForProvider":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		
		SITE_cpanel::orderReport($cid[0], true, true);
		break;
		
	case "showSummaryForAccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.account.site.easysdi.html.php');
		
		SITE_cpanel::showSummaryForAccount();
		break;
		
	case "downloadProduct":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::downloadProduct($order_id );
		break;
		
	case "changeOrderToSend":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::sendOrder(order_id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "orderDraft":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_cpanel::orderDraft($order_id);
		break;
		
	case "archiveOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::archiveOrder($order_id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "copyOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::copyOrder($order_id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "suppressOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::suppressOrder();
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;	
		
	case "listProductsForPartnerId":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::listProductsForPartnerId();
		break;	
		
	case "saveOrdersForProvider":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::saveOrdersForProvider($order_id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrdersForProvider&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "processOrder":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');	
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_cpanel::processOrder();
		break;

	case "listOrdersForProvider":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');		
		
		SITE_cpanel::listOrdersForProvider();
		break;

	case "listOrders":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::listOrders();
		break;
		
	case "sendOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_shop::saveOrder("SENT");
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;

	case "saveOrder":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'shop.site.easysdi.html.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_shop::saveOrder("SAVED");
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit"), false));
		break;

	/*****************************************************************************************************************************
	 * Product
	 *****************************************************************************************************************************/
	case "deleteProductFile":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_product::deleteProductFile($option);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=editProduct&id=".JRequest::getVar('id')."&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "showMetadata"	:
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		displayManager::showMetadata();
		break;
			
	case "listProduct":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_product::listProduct();
		break;
	
	case "editProduct":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		if (JRequest::getVar('id',-1) !=-1 ){
			SITE_product::editProduct($option);
		}else{
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit"), false));
		}
		break;
	
	case "cancelEditProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_product::cancelProduct($option);
		break;
		
	case "saveProduct":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productaccount.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productperimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'productproperty.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		
		SITE_product::saveProduct(true, $option);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "newProduct":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'basemap.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'perimeter.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');	
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_product::editProduct($option,true);
		break;
		
	case "suppressProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_product::suppressProduct($cid,$option);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit"), false));
		break;
		
	case "downloadFinalProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');	
		
		SITE_product::downloadFinalProduct();
		break;
	
	case "previewProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'previewproduct.site.easysdi.html.php');
		
		HTML_preview::previewProduct($metadata_id= JRequest::getVar('metadata_id'));
		break;
	/*****************************************************************************************************************************
	 * Favorite
	 *****************************************************************************************************************************/
	case "addMetadataNotification":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');

		SITE_favorite::metadataNotification(1);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id"."&Itemid=".JRequest::getVar('Itemid')), false));
		break;

	case "removeMetadataNotification":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_favorite::metadataNotification(0);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id"."&Itemid=".JRequest::getVar('Itemid')), false));
		break;
			
	case "addFavorite":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_favorite::favoriteProduct(1);
//		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id"."&Itemid=".JRequest::getVar('Itemid')), false));
		break;

	case "removeFavorite":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		SITE_favorite::favoriteProduct(0);
//		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id"."&Itemid=".JRequest::getVar('Itemid')), false));
		break;

	case "manageFavoriteProduct":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id"."&Itemid=".JRequest::getVar('Itemid')), false));
		break;

	case "manageFavorite":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
						
		SITE_favorite::manageFavoriteProduct();
		break;

	case "listAllProductExceptFavorite":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'favorite.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');			
			
		SITE_favorite::searchProducts();
		break;

}

?>