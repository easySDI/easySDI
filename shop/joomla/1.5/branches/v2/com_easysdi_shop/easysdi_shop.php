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

$language=&JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_shop');
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
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

/**
 * Handle view shop
 */
if ($view == 'shop' && $task != 'deleteProduct')
{
	$task = $view;
}

switch($task){
	/*****************************************************************************************************************************
	 * Shop
	 *****************************************************************************************************************************/
	case "shop":
		//Core BackEnd
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
					
		//Shop BackEnd
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'cpanel.admin.easysdi.html.php');
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'cpanel.admin.easysdi.php');
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
					
		//Shop FrontEnd
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'properties.site.easysdi.html.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
	//	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
	///	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'properties.site.easysdi.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
						
		HTML_shop::order();
		break;

	/*****************************************************************************************************************************
	 * Proxy
	 *****************************************************************************************************************************/
	case "proxy":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');			
			
		SITE_Proxy::proxy();
		break;


	/*****************************************************************************************************************************
	 * Favorite
	 *****************************************************************************************************************************/
	case "addFavorite":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		SITE_favorite::favoriteProduct(1);
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id" );
		break;

	case "removeFavorite":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		SITE_favorite::favoriteProduct(0);
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id" );
		break;

	case "manageFavoriteProduct":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart&limit=$limit&furnisher_id=$furnisher_id" );
		break;

	case "manageFavorite":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		SITE_favorite::manageFavoriteProduct();
		break;

	case "listAllProductExceptFavorite":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		SITE_favorite::searchProducts();
		break;

	/*****************************************************************************************************************************
	 * Product
	 *****************************************************************************************************************************/
	case "deleteProduct":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		HTML_shop::deleteProduct();

	/*****************************************************************************************************************************
	 * Order
	 *****************************************************************************************************************************/
	case "order":
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
	
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
			
		HTML_shop::order();
		break;
		
	case "orderReport":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		
		SITE_cpanel::orderReport($cid[0], true, false);
		break;
		
	case "orderReportForProvider":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		
		SITE_cpanel::orderReport($cid[0], true, true);
		break;
		
	case "showSummaryForPartner":
		//Core BackEnd
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
//					
		//Shop BackEnd
//		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.partner.site.easysdi.html.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');	
		
		SITE_cpanel::showSummaryForPartner();
		break;
		
	case "downloadProduct":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::downloadProduct($order_id );
		break;
		
	case "changeOrderToSend":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::sendOrder(order_id);
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "orderDraft":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
			
		SITE_cpanel::orderDraft($order_id);
		break;
		
	case "archiveOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::archiveOrder($order_id);
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "copyOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::copyOrder($order_id);
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "suppressOrder":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::suppressOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;	
		
	case "saveOrdersForProvider":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::saveOrdersForProvider($order_id);
		$mainframe->redirect("index.php?option=$option&task=listOrdersForProvider&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "processOrder":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');	
		
		SITE_cpanel::processOrder();
		break;

	case "listOrdersForProvider":
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
					
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');		
		
		SITE_cpanel::listOrdersForProvider();
		break;

	case "listOrders":
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
					
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
		
		SITE_cpanel::listOrders();
		break;
		
	case "sendOrder":
		//Core BackEnd
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
					
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
			
		HTML_shop::saveOrder("SENT");
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;

	case "saveOrder":
		//Core BackEnd
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
					
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'cpanel.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'cpanel.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
						
		HTML_shop::saveOrder("SAVED");
		$mainframe->redirect("index.php?option=$option&task=listOrders&limitstart=$limitstart&limit=$limit" );
		break;

	/*****************************************************************************************************************************
	 * Product
	 *****************************************************************************************************************************/
	case "listProduct":
		//Core FrontEnd
		//require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
//		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
//		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
//		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'cpanel.admin.easysdi.html.php');
//		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'cpanel.admin.easysdi.php');
//		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'properties.site.easysdi.html.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'favorite.site.easysdi.html.php');
		
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'properties.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'favorite.site.easysdi.php');
//		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'proxy.php');
			
		SITE_product::listProduct();
		break;
	
	case "editProduct":
		//Core FrontEnd
		//require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
			
		if (JRequest::getVar('id',-1) !=-1 ){
			SITE_product::editProduct();
		}else{
			$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
		}
		break;
	
	case "cancelEditProduct":
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "saveProduct":
		//Core FrontEnd
		//require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');
		
		SITE_product::saveProduct(true, $option);
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
		break;
		
	case "newProduct":
		//Core FrontEnd
		//require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');			
			
		SITE_product::editProduct(true);
		break;
		
	case "suppressProduct":
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
		
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'product.site.easysdi.php');		
			
		SITE_product::suppressProduct($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listProduct&limitstart=$limitstart&limit=$limit" );
		break;
	
		
	/*****************************************************************************************************************************
	 * Export
	 *****************************************************************************************************************************/	
	case "exportPdf":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		HTML_shop::exportPdf();
		break;
	case "exportXml":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
			
		//Core FrontEnd
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
			
		//Core BackEnd
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'cpanel.admin.easysdi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		/*Déplacé au niveau de chaque case qui le requiert réellement
			car provoque un bug au niveau de l'affichage des périmètres WMS dans openlayers*/
		//require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');
			
			
		HTML_shop::exportXml();
		break;

	default :
		$mainframe->redirect("index.php");
		break;
}

?>