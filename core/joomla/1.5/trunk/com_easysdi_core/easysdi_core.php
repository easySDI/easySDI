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
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.usertree.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');


$language=&JFactory::getLanguage();
$language->load('com_easysdi_account');
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);

$task = JRequest::getVar('task');
$view = JRequest::getVar('view');
$db =& JFactory::getDBO();
$option = JRequest::getVar('option');

switch($task){	
	case "createBlockUser":
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'helper.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::createBlockUser();
		break;
	case "createAccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::createAccount(0);
		break;
	case "createAffiliate":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::editAffiliateAccount(0);
		break;
	case "editAffiliateById":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		$affiliate_id = JRequest::getVar('affiliate_id');
		SITE_account::editAffiliateAccount($affiliate_id);
		break;
	case "listAffiliateAccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::listAccount($option);
		break;
	case "saveAffiliateAccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'helper.php');
		
		SITE_account::saveAffiliateaccount();
		break;
	case "editAffiliateaccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::editAffiliateaccount();
		break;	
	case "saveAccount":
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'user'.DS.'helper.php');
		
		SITE_account::saveAccount();				
		break;	
	case "editAccount":	
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'js'.DS.'core.admin.easysdi.php');
		
		SITE_account::editaccount();
		break;
	
	case "showMetadata"	:
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		displayManager::showMetadata();
		break;
	case "showAnyMetadata"	:
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::showAnyMetadata();
		break;
	case "showAbstractMetadata" :
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::showAbstractMetadata();
		break;
	case "showCompleteMetadata"	:
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::showCompleteMetadata();
		break;
	case "showDiffusionMetadata"	:
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::showDiffusionMetadata();
		break;	
	case "exportXml":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::exportXml();
		break;
	case "exportPdf":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
                require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
		
		displayManager::exportPdf();
		break;
	case "reportPdfError":
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		displayManager::reportPdfError();
		break;
	case "showaccount":
	default:
		require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'account.site.easysdi.php');
		require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'account.site.easysdi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		
		SITE_account::showaccount();
		break;	
				
}



 ?>