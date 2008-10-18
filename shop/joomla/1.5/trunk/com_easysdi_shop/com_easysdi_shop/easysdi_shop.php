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

require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'shop.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'easysdi.config.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'cpanel.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'product.site.easysdi.html.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');


$language=&JFactory::getLanguage();
$language->load('com_easysdi_shop');

?>
<?php
$task = JRequest::getVar('task');


switch($task){
	case "listProduct":
		SITE_product::listProduct();
		break;
	case "archiveOrder":
		SITE_cpanel::archiveOrder();
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "listOrders":
		SITE_cpanel::listOrders();
		break;
	case "importProduct":
		HTML_shop::importProduct();
		break;
	case "sendOrder":
		HTML_shop::saveOrder("SENT");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "saveOrder":
		HTML_shop::saveOrder("SAVED");
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		
		break;
	case "deleteProduct":
		HTML_shop::deleteProduct();
	default :	
	case "order":
		HTML_shop::order();
		break;
	case "showMetadata":
		HTML_shop::showMetadata();
		break;
	
	case "listGeoProduit":			
			HTML_shop::searchProducts();
	
		break;		
}
 ?>