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

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'helper.php');

require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.usertree.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'partner.site.easysdi.html.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.breadcrumbs.builder.class.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');


$language=&JFactory::getLanguage();
$language->load('com_easysdi_partner');

$task = JRequest::getVar('task');
$view = JRequest::getVar('view');
$db =& JFactory::getDBO();
$affiliate_id = JRequest::getVar('affiliate_id');

switch($task){
	case "createBlockUser":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::createBlockUser();
		break;
	case "createUser":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::createUser(0);
		break;
	case "createAffiliate":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::editAffiliatePartner(0);
		break;
	case "editAffiliateById":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		$affiliate_id = JRequest::getVar('affiliate_id');
		SITE_partner::editAffiliatePartner($affiliate_id);
		break;
	case "deleteAffiliate":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::deleteAffiliate($affiliate_id);
		break;
	case "listAffiliatePartner":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::listPartner();
		break;
	case "saveAffiliatePartner":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::saveAffiliatePartner();
		break;
	case "editAffiliatePartner":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		//$user = JFactory::getUser();
		//$affiliate_id =  $user->id;
		SITE_partner::editAffiliatePartner();
		break;	
	case "savePartner":
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::savePartner();				
		break;	
	case "editPartner":				
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');
		SITE_partner::editPartner();
		break;
	case "reportPdfError"	:
		displayManager::reportPdfError();
		break;	
	case "showMetadata"	:
		displayManager::showMetadata();
		break;
	case "showAnyMetadata"	:
		displayManager::showAnyMetadata();
		break;
	case "showAbstractMetadata" :
		displayManager::showAbstractMetadata();
		break;
	case "showCompleteMetadata"	:
		displayManager::showCompleteMetadata();
		break;
	case "showDiffusionMetadata"	:
		displayManager::showDiffusionMetadata();
		break;	
	case "exportXml":
		displayManager::exportXml();
		break;
	case "exportPdf":
		displayManager::exportPdf();
		break;
		
	case "showPartner":
	default:
		require_once(JPATH_COMPONENT.DS.'js'.DS.'partner.site.easysdi.php');	
		SITE_partner::showPartner();
		break;	
				
}

 ?>