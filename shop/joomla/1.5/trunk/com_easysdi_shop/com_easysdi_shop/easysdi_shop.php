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

//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');

$language=&JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_shop');

?>
<?php
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');
$order_id = JRequest::getVar('order_id');

$myFavoritesFirst = JRequest::getVar('myFavoritesFirst');
$simpleSearchCriteria = JRequest::getVar('simpleSearchCriteria');
$limitstart= JRequest::getVar('limitstart');
if($simpleSearchCriteria=="")
{
	$simpleSearchCriteria = "lastAddedMD";
}
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
	case "shop":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::order();
		break;
			
	case "proxy":

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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_Proxy::proxy();
		break;
		/*
		 * Favorites
		 */
	case "addMetadataNotification":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_favorite::metadataNotification(1);
		/*$mainframe->redirect("index.php?option=$option&task=listFavoriteProduct" );*/
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
		break;

	case "removeMetadataNotification":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_favorite::metadataNotification(0);
		/*$mainframe->redirect("index.php?option=$option&task=listFavoriteProduct" );*/
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
		break;
			
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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

		/*case "listFavoriteProduct":
		 SITE_favorite::listFavoriteProduct();
		 break;

		 case "deleteFavoriteProduct":
		 SITE_favorite::deleteFavoriteProduct($cid);
		 $mainframe->redirect("index.php?option=$option&task=listFavoriteProduct" );
		 break;*/

		/*case "addFavoriteProduct":
		 SITE_favorite::addFavoriteProduct($cid);
		 $mainframe->redirect("index.php?option=$option&task=listAllProductExceptFavorite" );
		 break;
		 */

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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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

		/*
		 * Order
		 */
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
	case "order":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::order();
		break;
	case "orderReport":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::orderReport($cid[0], true, false);
		break;
	case "orderReportForProvider":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		//SITE_cpanel::orderReportForProvider($cid[0]);
		SITE_cpanel::orderReport($cid[0], true, true);
		break;
	case "downloadProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::downloadProduct();
		break;
	case "changeOrderToSend":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::sendOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "orderDraft":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::orderDraft($order_id);
		break;
	case "archiveOrder":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::archiveOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "saveOrdersForProvider":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::saveOrdersForProvider();
		$mainframe->redirect("index.php?option=$option&task=listOrdersForProvider" );
		break;
	case "processOrder":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::processOrder();
		break;

	case "listOrdersForProvider":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::listOrdersForProvider();
		break;


	case "listOrders":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
		SITE_cpanel::listOrders();
		break;
	case "sendOrder":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::saveOrder("SENT");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;

	case "saveOrder":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::saveOrder("SAVED");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
		/*
		 * Properties
		 */
	case "cancelProperties":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
		break;
	case "saveProperties":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::saveProperties($option);
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
		break;
	case "editProperties":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::editProperties($cid[0],$option);
		break;
	case "newProperties":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::editProperties(0,$option);
		break;
	case "listProperties":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::listProperties($option);
		break;
	case "listPropertiesValues":
	case "listPropertiesValue":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::listPropertiesValues($cid[0],$option);
		break;
	case "cancelPropertiesValues":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );

	case "savePropertiesValues":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::savePropertiesValues($option);

		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
		break;
	case "deletePropertiesValues":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::deletePropertiesValues($cid,$option);
		break;
	case "editPropertiesValues":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::editPropertiesValues($cid[0],$option);
		break;
	case "newPropertiesValues":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_properties::editPropertiesValues(0,$option);
		break;

		/*
		 * Metadata
		 */
	case "listProductMetadata":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
		SITE_product::listProductMetadata();
		break;
	case "saveProductMetadata":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_product::saveProductMetadata();
		//$mainframe->redirect("index.php?option=$option&task=listProductMetadata" );
		break;
	case "cancelEditProductMetadata" :
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->redirect("index.php?option=$option&task=listProductMetadata" );
		break;
	case "editMetadata":
		
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		$id = JRequest::getVar('id');
		
		SITE_metadata::editMetadata($id,$option);
		break;
	case "saveMetadata":
		//Shop BackEnd
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');
			
		//Shop FrontEnd
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
		
		ADMIN_metadata::saveMetadata($option);
		break;
	case "editMetadata2":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		SITE_product::editMetadata2();
		break;
	case "showMetadata":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		displayManager::showMetadata();
		break;
		/*
		 * Product
		 */
	case "cancelEditProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		break;
	case "saveProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_product::saveProduct(true, $option);
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		break;
	case "newProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_product::editProduct(true);
		break;
	case "editProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		if (JRequest::getVar('id',-1) !=-1 ){
			SITE_product::editProduct();
		}else{
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
		}
		break;
	case "suppressProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_product::suppressProduct($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		break;
	case "listProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
		SITE_product::listProduct();
		break;
	case "importProduct":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		HTML_shop::importProduct();
		break;




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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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


	case "listMetadataClasses":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::listStandardClasses($option);
		break;
	case "newStandardClass":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::editStandardClasses(0,$option);
		break;
	case	"editStandardClass":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::editStandardClasses($cid[0],$option);
		break;
	case "newStandard":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::editStandard(0,$option);
		break;
	case "saveStandard":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::saveMDStandard($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );
		break;
	case "cancelStandardClass":
	case "cancelStandard":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );
		break;
	case "saveStandardClass":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::saveMDStandardClasses($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );

		break;

	case "validateMetadata":
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		SITE_metadata::validateMetadata();
		break;


	default :
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
		require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
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
			
			
		$mainframe->enqueueMessage(JText::_("EASYSDI_ACTION_NOT_ALLOWED"), "INFO");

		break;
}

?>