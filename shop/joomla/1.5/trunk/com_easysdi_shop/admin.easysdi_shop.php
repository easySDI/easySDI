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

global $mainframe;
$task = JRequest::getVar('task');
$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch($task){

case "proxy":
		SITE_proxy::proxy();	
		break;
	
	case "orderReport":
		SITE_cpanel::orderReport($cid[0], false,false);
		break;
	case "listOrders":
		TOOLBAR_cpanel::_LISTORDERS();
		ADMIN_cpanel::listOrders();
		break;
	/*case "editOrder":
		TOOLBAR_cpanel::_EDITORDERS();	
		ADMIN_cpanel::editOrder($cid[0],$option);
		break;*/
	case "deleteOrder":
		ADMIN_cpanel::deleteOrder($cid,$option);
		//$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "saveOrder":
		ADMIN_cpanel::saveOrder($option);
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
	case "cancelOrder":
		$mainframe->redirect("index.php?option=$option&task=listOrders" );
		break;
			
	case "saveMDTABS":
		ADMIN_metadata::saveMDTabs($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
		break;
	case "cancelMDTabs":		
		$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
		break;
	case "newMetadataTab":
		TOOLBAR_metadata::_EDITMETADATATAB();	
		ADMIN_metadata::editMDTabs(0,$option);
		break;
	case "editMetadataTab":
		TOOLBAR_metadata::_EDITMETADATATAB();	
		ADMIN_metadata::editMDTabs($cid[0],$option);
		break;
		
	case "listMetadataTabs":
		TOOLBAR_metadata::_LISTMDTABS();
		ADMIN_metadata::listMetadataTabs($option);
		break;
	case "orderupMetadataTabs":
		ADMIN_metadata::goUpMetadataTabs($cid,$option);
	break;
	case "orderdownMetadataTabs":
		ADMIN_metadata::goDownMetadataTabs($cid,$option);
	break;
	case "saveOrderMetadataTabs":
		ADMIN_metadata::saveOrderMetadataTabs($cid, $option);
	break;
	case "orderMetadataTabs":
		ADMIN_metadata::orderMetadataTabs($cid, $option);
	break;
		
	case "deleteMetadataStandard":
		ADMIN_metadata::deleteMDStandard($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	
	case "deleteMetadataStandardClasses":
		ADMIN_metadata::deleteMDStandardClasses($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
		
	case "ctrlPanelShop":
		HTML_ctrlpanel::ctrlPanelShop($option);
		break;
	case "ctrlPanelBaseMap":
		$mainframe->redirect("index.php?option=$option&task=listBasemap" );
		break;				

	case "ctrlPanelMetadata":
		HTML_ctrlpanel::ctrlPanelMetadata($option);
		break;

	case "ctrlPanelLocation":
		$mainframe->redirect("index.php?option=$option&task=listLocation" );
		break;
		
	case "ctrlPanelPerimeter":
		$mainframe->redirect("index.php?option=$option&task=listPerimeter" );
		break;
	case "ctrlPanelProduct":
		$mainframe->redirect("index.php?option=$option&task=listProduct" );
		
		break;

	case "ctrlPanelProperties":
		$mainframe->redirect("index.php?option=$option&task=listProperties" );					
		break;
			
	case "editMetadataStandardClasses":
		TOOLBAR_metadata::_EDITSTANDARDCLASSES();	
		ADMIN_metadata::editStandardClasses($cid[0],$option);
	break;
	case "newMetadataStandardClasses":
		TOOLBAR_metadata::_EDITSTANDARDCLASSES();
		ADMIN_metadata::editStandardClasses(0,$option);
	break;
	
	case "saveMDStandardClasses":
		ADMIN_metadata::saveMDStandardClasses($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
	case "cancelMDStandardClasses":
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
		break;
	case "listMetadataStandardClasses":
		TOOLBAR_metadata::_LISTSTANDARDCLASSES();;
		ADMIN_metadata::listStandardClasses($option);
	break;
	case "orderupMetadataStandardClasses":
		ADMIN_metadata::goUpMetadataStandardClasses($cid,$option);
	break;
	case "orderdownMetadataStandardClasses":
		ADMIN_metadata::goDownMetadataStandardClasses($cid,$option);
	break;
	case "saveOrderMetadataStandardClasses":
		ADMIN_metadata::saveOrderMetadataStandardClasses($cid, $option);
	break;
	
	case "editMetadataStandard":
		TOOLBAR_metadata::_EDITSTANDARD();		
		ADMIN_metadata::editStandard($cid[0],$option);
	break;
	case "newMetadataStandard":
		TOOLBAR_metadata::_EDITSTANDARD();
		ADMIN_metadata::editStandard(0,$option);
	break;
	
	case "saveMDStandard":
		ADMIN_metadata::saveMDStandard($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	case "cancelMDStandard":
		$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
		break;
	case "listMetadataStandard":
		TOOLBAR_metadata::_LISTSTANDARD();;
		ADMIN_metadata::listStandard($option);
	break;
	
	
	
	
	
	
	case "editMetadataExt":
		TOOLBAR_metadata::_EDITEXT();;		
		ADMIN_metadata::editExt($cid[0],$option);
	break;
	case "newMetadataExt":
		TOOLBAR_metadata::_EDITEXT();
		ADMIN_metadata::editExt(0,$option);
	break;
	
	case "saveMDExt":
		ADMIN_metadata::saveMDExt($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
		break;
	case "cancelMDExt":
		$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
		break;
	case "listMetadataExt":
		TOOLBAR_metadata::_LISTEXT();
		ADMIN_metadata::listExt($option);
	break;
	
	
	
	
	
	
	
	case "editMetadataLocfreetext":
		TOOLBAR_metadata::_EDITLOCFREETEXT();		
		ADMIN_metadata::editLocfreetext($cid[0],$option);
	break;
	case "newMetadataLocfreetext":
		TOOLBAR_metadata::_EDITLOCFREETEXT();
		ADMIN_metadata::editLocfreetext(0,$option);
	break;
	
	case "saveMDLocfreetext":
		ADMIN_metadata::saveMDLocfreetext($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
		break;
	case "cancelMDLocfreetext":
		$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
		break;
	case "listMetadataLocfreetext":
		TOOLBAR_metadata::_LISTLOCFREETEXT();
		ADMIN_metadata::listLocfreetext($option);
	break;
	
	case "deleteMetadataClass":
		ADMIN_metadata::deleteMetadataClass($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
		
	case "editMetadataClass":
		TOOLBAR_metadata::_EDITCLASS();
		ADMIN_metadata::editClass($cid[0],$option);
	break;
	case "newMetadataClass":
		TOOLBAR_metadata::_EDITClass();
		ADMIN_metadata::editClass(0,$option);
	break;
	
	case "saveMDClass":
		ADMIN_metadata::saveMDClass($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
	case "cancelMDClass":
		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
		break;
	case "listMetadataClass":
		TOOLBAR_metadata::_LISTClass();
		ADMIN_metadata::listClass($option);
	break;
	case "orderupMetadataClass":
		ADMIN_metadata::goUpMetadataClass($cid,$option);
		
	break;
	case "orderdownMetadataClass":
		ADMIN_metadata::goDownMetadataClass($cid,$option);		
	break;
	case "saveOrderMetadataClass":
		ADMIN_metadata::saveOrderMetadataClass($cid, $option);
	break;
	
	case "editMetadataFreetext":
		TOOLBAR_metadata::_EDITFREETEXT();
		ADMIN_metadata::editFreetext($cid[0],$option);
	break;
	case "newMetadataFreetext":
		TOOLBAR_metadata::_EDITFREETEXT();
		ADMIN_metadata::editFreetext(0,$option);
	break;
	
	case "saveMDFreetext":
		ADMIN_metadata::saveMDFreetext($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
		break;
	case "cancelMDFreetext":
		$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
		break;
	case "listMetadataFreetext":
		TOOLBAR_metadata::_LISTFREETEXT();
		ADMIN_metadata::listFreetext($option);
	break;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	case "editMetadataList":
		TOOLBAR_metadata::_LISTEDIT();
		ADMIN_metadata::editList($cid[0],$option);
	break;
	
	case "newMetadataList":
		TOOLBAR_metadata::_LISTEDIT();
		ADMIN_metadata::editList(0,$option);
	break;
	
	case "saveMDList":
		ADMIN_metadata::saveMDList($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
		break;
	case "cancelMDList":
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
		break;		
	case "listMetadataList":
		TOOLBAR_metadata::_LISTLIST();
		ADMIN_metadata::listList($option);
	break;

	
	
	
	case "deleteMetadataList":
		
		ADMIN_metadata::deleteMetadataList($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataList" );	
		break;
	case "deleteMetadataListContent":
		$list_id = JRequest::getVar ('list_id', 0 );
		ADMIN_metadata::deleteMetadataListContent($cid,$option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
	break;	
	case "editMetadataListContent":
		$list_id = JRequest::getVar ('list_id', 0 );
		TOOLBAR_metadata::_LISTEDITCONTENT();
		ADMIN_metadata::editListContent($cid[0],$option,$list_id);
	break;
	
	case "newMetadataListContent":
		$list_id = JRequest::getVar ('list_id', 0 );
		TOOLBAR_metadata::_LISTEDITCONTENT();
		ADMIN_metadata::editListContent(0,$option,$list_id);
	break;
	
	case "saveMDListContent":
		$list_id = JRequest::getVar ('list_id', 0 );
		ADMIN_metadata::saveMDListContent($option);
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
		break;
	case "cancelMDListContent":
		$list_id = JRequest::getVar ('list_id', 0 );
		$mainframe->redirect("index.php?option=$option&task=listMetadataListContent&cid[]=$list_id" );
		break;		
	case "listMetadataListContent":
		TOOLBAR_metadata::_LISTLISTCONTENT();
		ADMIN_metadata::listListContent($cid[0],$option);
	break;
	
	
	
	
	
	
	case "listMetadataDate":
		TOOLBAR_metadata::_LISTDATE();
		ADMIN_metadata::listDate($option);
	break;
	
	case "orderdownPropertiesValues":
		ADMIN_properties::goDown($cid,$option);
		
		break;
	case "orderupPropertiesValues":
		ADMIN_properties::goUp($cid,$option);
		
		break;
	case "saveOrderPropertiesValues":
		ADMIN_properties::saveOrderPropertiesValues($cid, $properties_id, $option);
		
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
		
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
	case "orderupbasemapcontent":
	
		ADMIN_basemap::orderUpBasemapContent($cid[0],JRequest::getVar('basemap_def_id'));
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
	case "orderdownbasemapcontent":
		ADMIN_basemap::orderDownBasemapContent($cid[0],JRequest::getVar('basemap_def_id'));
		$mainframe->redirect("index.php?option=$option&task=listBasemapContent&cid[]=".JRequest::getVar('basemap_def_id') );
		break;
		
		
		
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
	case "orderupProperties":
		ADMIN_properties::goUpProperties($cid,$option);
	break;
	case "orderdownProperties":
		ADMIN_properties::goDownProperties($cid,$option);
	break;
	case "saveOrderProperties":
		ADMIN_properties::saveOrderProperties($cid,$option);
	break;
		
		
		
case "saveLocation":		
		ADMIN_location::saveLocation(true,$option);				
		break;
	
	case "copyLocation":	
		ADMIN_location::copyLocation($cid,$option);
		break;
		
	case "deleteLocation":		
		ADMIN_location::deleteLocation($cid,$option);				
		break;	
		
	case "editLocation":
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation($cid[0],$option);
		break;
		
	case "newLocation":
		TOOLBAR_location::_EDITLOCATION();
		ADMIN_location::editLocation(0,$option);
		
		break;
		
	case "cancelLocation":
	case "listLocation":
		TOOLBAR_location::_LISTLOCATION();
		ADMIN_location::listLocation($option);
		
		break;
		
		
		
		
		
		
		
		
		
	case "savePerimeter":		
		ADMIN_perimeter::savePerimeter(true,$option);				
		break;
	
	case "copyPerimeter":	
		ADMIN_perimeter::copyPerimeter($cid,$option);
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
	case "orderupPerimeter":
		ADMIN_perimeter::goupPerimeter($cid, $option);
		
		break;
	case "orderdownPerimeter":
		ADMIN_perimeter::godownPerimeter($cid, $option);
		
		break;
	case "saveOrderPerimeter":
		ADMIN_perimeter::saveOrderPerimeter($cid, $option);
		
		break;
		
	case "saveProductMetadata":		
		ADMIN_product::saveProductMetadata($option);
		$mainframe->redirect("index.php?option=$option&task=listProduct");				
		break;
		
	case "saveProduct":		
		ADMIN_product::saveProduct($option);				
		break;
	case "deleteProduct":		
		ADMIN_product::deleteProduct($cid,$option);				
		break;	

	case "editProductMetadata2":	
		TOOLBAR_product::_EDITPRODUCTMETADATA();
		ADMIN_product::editProductMetadata2($cid[0],$option);
		break;
	
		
	case "editProductMetadata":	
		TOOLBAR_product::_EDITPRODUCTMETADATA();
		ADMIN_product::editProductMetadata($cid[0],$option);
		break;
		
	case "editProduct":			
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct($cid[0],$option);
		break;
		
	case "newProduct":
		TOOLBAR_product::_EDITPRODUCT();
		ADMIN_product::editProduct(0,$option);		
		break;
		
		
	case "cancelProductMetadata":
	case "cancelProduct":
	case "listProduct":
		TOOLBAR_product::_LISTPRODUCT();
		ADMIN_product::listProduct($option);		
		break;
	default:
		$mainframe->enqueueMessage($task,"INFO");
		HTML_ctrlpanel::ctrlPanelShop($option);		
		break;
}

?>