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

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'sditable.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');

global $mainframe;
$language=&JFactory::getLanguage();
$language->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');

$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

switch($task){
		// Metadata
		case "listMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			SITE_metadata::listMetadata($option);
			break;
		// Metadata
		case "showMetadata"	:
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			include_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			displayManager::showMetadata();
			break;
		case "askForEditMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::askForEditMetadata($cid[0],$option);
			break;
		case "editMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			SITE_metadata::editMetadata($cid[0],$option);
			break;
		case "saveMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::saveMetadata($option);
			break;
		case "cancelMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_metadata::cancelMetadata($option);
			SITE_metadata::listMetadata($option);
			break;
			
		case "previewXMLMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			ADMIN_metadata::preview('XML');
// 			ADMIN_metadata::previewXMLMetadata($option);
			break;
			
		case "previewMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			ADMIN_metadata::preview('MD');
// 			ADMIN_metadata::previewMetadata($option);
			break;
		
		case "validateMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::validateMetadata($option);
			break;
		
		case "invalidateMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_metadata::invalidateMetadata($cid[0], $option);
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_metadata::listMetadata($option);
			break;
			
		case "validateForPublishMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::validateForPublishMetadata($option);
			break;
		
		case "publishMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::publishMetadata($option);
			break;
	
		case "updateMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::updateMetadata($option);
			break;
		
		case "importXMLMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_metadata::importXMLMetadata($option);
			break;
		
		case "importCSWMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_metadata::importCSWMetadata($option);
			break;
		
		case "replicateMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'stereotype'.DS.'classstereotypebuilder.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_metadata::replicateMetadata($option);
			break;
			
		case "resetMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_metadata::resetMetadata($option);
			break;
			
		case "getContact":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::getContact($option);
			break;
			
		case "getObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::getObjectVersion($option);
			break;
		
		case "assignMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'stereotype'.DS.'classstereotypesaver.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'historyassign.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_metadata::assignMetadata($option);
			break;
	
		case "historyAssignMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			SITE_metadata::historyAssignMetadata($cid[0],$option);
			break;
			
		case "archiveMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_metadata::archiveMetadata($cid,$option);				
			SITE_metadata::listMetadata($option);
			break;

		case "listObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_object::listObject($option);
			break;
			
		case "newObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_object::editObject(0,$option);
			break;
			
		case "editObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_object::editObject($cid[0],$option);
			break;
			
		case "saveObject":		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
						
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_object::saveObject($option);				
			SITE_object::listObject($option);
			break;
		
		case "deleteObject":		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttypelink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_object::deleteObject($cid,$option);				
			SITE_object::listObject($option);
			break;
	
		case "cancelObject":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_object::cancelObject($option);
			SITE_object::listObject($option);
			break;
		
		case "viewObjectVersionLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_objectversion::viewObjectVersionLink($cid[0],$option);
			break;
		case "manageObjectVersionLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_objectversion::manageObjectVersionLink($cid[0],$option);
			break;
		
		case "saveObjectVersionLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_objectversion::saveObjectVersionLink($option);
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;
			
		case "cancelObjectVersionLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;
			
		case "getObjectForLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_object::getObjectForLink($option);
			break;
		
		case "listObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;
			
		case "getObjectVersionForLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_objectversion::getObjectVersionForLink($option);
			break;
		
		case "newObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_objectversion::editObjectVersion(0, $option);
			break;
			
		case "editObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			ADMIN_objectversion::editObjectVersion($cid[0], $option);
			break;
		
		case "saveObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_objectversion::saveObjectVersion($option);
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;
			
		case "deleteObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversionlink.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_objectversion::deleteObjectVersion($cid, $option);
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;
			
		case "cancelObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			SITE_objectversion::cancelObjectVersion($option);
			SITE_object::listObject($option);
			break;

		case "backObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'objectversion.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			ADMIN_objectversion::backObjectVersion($option);
			$object_id = JRequest::getVar ('object_id');
			SITE_objectversion::listObjectVersion($object_id, $option);
			break;

			
		// External applications
		case "listApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			$object_id = JRequest::getVar ('object_id');
			SITE_application::listApplication($object_id, $option);
			break;
		case "newApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
			$object_id = JRequest::getVar ('object_id');
			SITE_application::editApplication($cid[0], $option);
		break;
		case "editApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
			$object_id = JRequest::getVar ('object_id');
			SITE_application::editApplication($cid[0], $option);
		break;
		case "saveApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			
			$object_id = JRequest::getVar ('object_id');
			SITE_application::saveApplication($option);
			SITE_application::listApplication($object_id, $option);
			break;
		case "deleteApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'application.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			$object_id = JRequest::getVar ('object_id');
			ADMIN_application::removeApplication($cid,$option);
			SITE_application::listApplication($object_id, $option);
			break;
		case "cancelApplication":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			$object_id = JRequest::getVar ('object_id');
			SITE_application::cancelApplication($cid, $option);
			
			SITE_application::listApplication($object_id, $option);
			break;
		case 'backApplication':
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'application.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'application.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'application.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			SITE_object::listObject($option);
			break;
		
		default:

		case "listCatalogContent":		
			include_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relation.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'relationcontext.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'context.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'catalog.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'geoMetadata.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			SITE_catalog::listCatalogContent();
			break;
			
		case "previewProduct":
			HTML_preview::previewProduct($metadata_id= JRequest::getVar('metadata_id'));
			break;

		case 'getReport':
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'reportEngine.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'lib'.DS.'mpdf'.DS.'mpdf.php');
			
			reportEngine::getReport();
			break;
		case 'testReport':
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'testreport.site.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			
			testreport::main();
			break;
		case 'testxQuery':
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'testMxQuery.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'testMxQuery.html.php');
			testMxQuery::test();
			break;
		case 'listMyReports':
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
			ADMIN_xQuery::listMyReports();
			break;
			
		case 'processXQueryReport':
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		//	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php'); //reuse buildcsw
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); // for same curl as search results
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
			
			ADMIN_xQuery::processXQueryReport();
			break;
			
		case 'provideXMLDataForXQueryReport':
		//	require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php'); //reuse buildcsw
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); // for same curl as search results
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'xquery.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'xquery.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'xquery.admin.easysdi.php');
			
			ADMIN_xQuery::provideXMLDataForXQueryReport();
			break;
		case 'processXQuery':
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'testMxQuery.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'testMxQuery.html.php');
			testMxQuery::process();
			break;
		case "uploadFileAndGetLink":
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
			ADMIN_metadata::uploadFileAndGetLink($option);
			break;
		case "deleteUploadedFile":
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'mdnamespace.admin.easysdi.php');
		
			ADMIN_metadata::deleteUploadedFile($option,JRequest::getVar ('file',null ));
			break;
		case "getBoundariesByCategory":
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
				SERVICE_boundary::getBoundariesByCategoriesAlias(JRequest::getVar('category', null),JRequest::getVar('exclude', null));
				break;
		case "getBoundaries";
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
			SERVICE_boundary::getBoundariesByCategoriesAlias(null, JRequest::getVar('exclude', null));
			break;
		case "getBoundariesByLabel";
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'service'.DS.'boundary.admin.easysdi.service.php');
			SERVICE_boundary::getBoundariesByLabel(JRequest::getVar('query', null), JRequest::getVar('category', null));
			break;
	}
//}
 ?>