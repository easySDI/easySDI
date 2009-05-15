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

require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.class.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'properties.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'properties.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'metadata.site.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'metadata.easysdi.class.php');

require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php'); 

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');


require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'favorite.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'proxy.php');


$language=&JFactory::getLanguage();
$language->load('com_easysdi_core');
$language->load('com_easysdi_shop');

?>
<?php
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');

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
			HTML_shop::order();
			break;
			
	case "proxy":
	
	SITE_Proxy::proxy();
	break;
	/*
	 * Favorites
	 */
	case "addMetadataNotification":	
		SITE_favorite::metadataNotification(1);
		/*$mainframe->redirect("index.php?option=$option&task=listFavoriteProduct" );*/		
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
		break;
		
	case "removeMetadataNotification":		
		SITE_favorite::metadataNotification(0);
		/*$mainframe->redirect("index.php?option=$option&task=listFavoriteProduct" );*/
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
		break;	
			
	case "addFavorite":
		SITE_favorite::favoriteProduct(1);
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );		
		break;
		
	case "removeFavorite":
		SITE_favorite::favoriteProduct(0);
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );		
		break;	
		
	case "manageFavoriteProduct":
		$mainframe->redirect("index.php?option=$option&task=manageFavorite&myFavoritesFirst=$myFavoritesFirst&simpleSearchCriteria=$simpleSearchCriteria&freetextcriteria=$freetextcriteria&limitstart=$limitstart" );
		break;
		
	case "manageFavorite":
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
		SITE_favorite::searchProducts();					
		break;
	
	/*
	 * Order
	 */
	case "deleteProduct":
		HTML_shop::deleteProduct();
	case "order":
		HTML_shop::order();
		break;
	case "orderReport":
		SITE_cpanel::orderReport($cid[0]);
		break;

	case "downloadProduct":
		SITE_cpanel::downloadProduct();
		break;
	case "changeOrderToSend":
		SITE_cpanel::sendOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
		
	case "archiveOrder":
		SITE_cpanel::archiveOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "saveOrdersForProvider":
		SITE_cpanel::saveOrdersForProvider();
		$mainframe->redirect("index.php?option=$option&task=listOrdersForProvider" );
		break;	
	case "processOrder":
		SITE_cpanel::processOrder();
		break;
		
	case "listOrdersForProvider":
		SITE_cpanel::listOrdersForProvider();
		break;

		
	case "listOrders":
		SITE_cpanel::listOrders();
		break;
	case "sendOrder":
		HTML_shop::saveOrder("SENT");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
		
	case "saveOrder":
		HTML_shop::saveOrder("SAVED");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );		
		break;
	/*
	 * Properties
	 */
	case "cancelProperties":
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
		break;
	case "saveProperties":
		SITE_properties::saveProperties($option);
		$mainframe->redirect("index.php?option=$option&task=listProperties" );
		break;
	case "editProperties":
		SITE_properties::editProperties($cid[0],$option);
		break;
	case "newProperties":
		SITE_properties::editProperties(0,$option);
		break;
	case "listProperties":
		SITE_properties::listProperties($option);
		break;	
	case "listPropertiesValues":
	case "listPropertiesValue":		
		SITE_properties::listPropertiesValues($cid[0],$option);		
		break;
	case "cancelPropertiesValues":
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
		
	case "savePropertiesValues":		
		SITE_properties::savePropertiesValues($option);
		
		$properties_id = JRequest::getVar('properties_id');
		$mainframe->redirect("index.php?option=$option&task=listPropertiesValues&cid[]=".$properties_id );
		break;
	case "deletePropertiesValues":		
		SITE_properties::deletePropertiesValues($cid,$option);				
		break;	
	case "editPropertiesValues":		
		SITE_properties::editPropertiesValues($cid[0],$option);
		break;
	case "newPropertiesValues":		
		SITE_properties::editPropertiesValues(0,$option);
		break;
	
	/*
	 * Metadata
	 */	
	case "listProductMetadata":
		SITE_product::listProductMetadata();
		break;
	case "saveProductMetadata":
		SITE_product::saveProductMetadata();
		$mainframe->redirect("index.php?option=$option&task=listProductMetadata" );
		break;
	case "cancelEditProductMetadata" :
		$mainframe->redirect("index.php?option=$option&task=listProductMetadata" );
		break;
	case "editMetadata":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		SITE_product::editMetadata();
		break;
	case "editMetadata2":
		include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'common.easysdi.php');
		SITE_product::editMetadata2();
		break;
	case "showMetadata":
		displayManager::showMetadata();
		break;
	/*
	 * Product
	 */
	case "cancelEditProduct":
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		break;
	case "saveProduct":
		SITE_product::saveProduct($option);
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		break;
	case "newProduct":
		SITE_product::editProduct(true);
		break;
	case "editProduct":
		if (JRequest::getVar('id',-1) !=-1 ){
		SITE_product::editProduct();
		}else{
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
		}
		break;
	case "listProduct":
		SITE_product::listProduct();
		break;
	case "importProduct":
		HTML_shop::importProduct();
		break;
	
		
	
		
	case "exportPdf":
		HTML_shop::exportPdf();
		break;
	case "exportXml":
		HTML_shop::exportXml();
		break;
	
	
	case "listMetadataClasses":
		SITE_metadata::listStandardClasses($option);
		break;
	case "newStandardClass":
		SITE_metadata::editStandardClasses(0,$option);
		break;
	case	"editStandardClass":
		SITE_metadata::editStandardClasses($cid[0],$option);
		break;
	case "newStandard":
		SITE_metadata::editStandard(0,$option);
		break;	
	case "saveStandard":
		SITE_metadata::saveMDStandard($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );
		break;	
	case "cancelStandardClass":
	case "cancelStandard":
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );
		break;	
	case "saveStandardClass":
		SITE_metadata::saveMDStandardClasses($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClasses" );
		
	break;
		
	case "validateMetadata":
		SITE_metadata::validateMetadata();
		break;
		
		
	default :	
		$mainframe->enqueueMessage(JText::_("EASYSDI_ACTION_NOT_ALLOWED"), "INFO");

		break;
}

 ?>