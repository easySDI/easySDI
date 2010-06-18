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
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');
/*require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'preview.site.easysdi.html.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');
*/
$language=&JFactory::getLanguage();
$language->load('com_easysdi_catalog', JPATH_ADMINISTRATOR);
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);

?>
<?php
$option = JRequest::getVar('option');
/*if (array_key_exists('task', $_GET))
	$task = $_GET['task'];
else*/
	$task = JRequest::getVar('task');

$cid = JRequest::getVar ('cid', array(0) );
if (!is_array( $cid )) {
	$cid = array(0);
}

$view = JRequest::getVar('view');

if ($view)
{
	switch($view){
		case "catalog":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'catalog.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'catalog.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'geoMetadata.php');
			$task="listCatalogContent";
	}
}
	
/*if ($view)
{
	switch($view){
		default:
		case "catalog":
			SITE_catalog::listCatalogContent();
			break;	
	}
}
else
{*/
	switch($task){
		// Metadata
		case "listMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			SITE_metadata::listMetadata($option);
			break;
		// Metadata
		case "showMetadata"	:
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
			displayManager::showMetadata();
			break;
		case "askForEditMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'namespace.admin.easysdi.php');
			ADMIN_metadata::askForEditMetadata($cid[0],$option);
			break;
		case "editMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'metadata.toolbar.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			//TOOLBAR_metadata::_EDIT();
				
			//echo $cid[0];
			//break;	
			SITE_metadata::editMetadata($cid[0],$option);
			break;
		case "saveMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			ADMIN_metadata::saveMetadata($option);
			//TOOLBAR_object::_DEFAULT();
			//ADMIN_object::listObject($option);
			break;
		case "cancelMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			SITE_metadata::cancelMetadata($option);
			SITE_metadata::listMetadata($option);
			break;
			
		case "previewXMLMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			ADMIN_metadata::previewXMLMetadata($option);
			break;
		
		case "validateMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			ADMIN_metadata::validateMetadata($option);
			break;
		
		case "invalidateMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			ADMIN_metadata::invalidateMetadata($option);
			break;
			
		case "validateForPublishMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			ADMIN_metadata::validateForPublishMetadata($option);
			break;
		
		case "publishMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			ADMIN_metadata::publishMetadata($option);
			break;
	
		case "importXMLMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'namespace.admin.easysdi.php');
			ADMIN_metadata::importXMLMetadata($option);
			break;
		
		case "importCSWMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'namespace.admin.easysdi.php');
			
			ADMIN_metadata::importCSWMetadata($option);
			break;
		
		case "replicateMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'namespace.admin.easysdi.php');
			
			ADMIN_metadata::replicateMetadata($option);
			break;
			
		case "resetMetadata":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php'); 
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'attributetype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'namespace.admin.easysdi.php');
			
			ADMIN_metadata::resetMetadata($option);
			break;
			
		case "getContact":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			ADMIN_metadata::getContact($option);
			break;
			
		case "getObject":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			ADMIN_metadata::getObject($option);
			break;
		
		case "assignMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'historyassign.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			ADMIN_metadata::assignMetadata($option);
			break;
	
		case "historyAssignMetadata":
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'metadata.site.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			SITE_metadata::historyAssignMetadata($cid[0],$option);
			break;
			
		case "listObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			SITE_object::listObject($option);
			break;
			
		case "newObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			SITE_object::editObject(0,$option);
			break;
			
		case "editObject":			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
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
			
			SITE_object::saveObject($option);				
			SITE_object::listObject($option);
			break;
		
		case "deleteObject":		
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			SITE_object::deleteObject($cid,$option);				
			SITE_object::listObject($option);
			break;
	
		case "cancelObject":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			SITE_object::cancelObject($option);
			SITE_object::listObject($option);
			break;
		
		case "archiveObject":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			
			ADMIN_object::archiveObject($cid,$option);				
			SITE_object::listObject($option);
			break;

		case "viewObjectLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			ADMIN_object::viewObjectLink($cid[0],$option);
			break;
		case "manageObjectLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'metadata.admin.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			ADMIN_object::manageObjectLink($cid[0],$option);
			break;
	
		case "getObjectForLink":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'object.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			ADMIN_object::getObjectForLink($option);
			break;
		
		case "versionaliseObject":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objecttype.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'objectversion.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'metadata.site.easysdi.html.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'js'.DS.'catalog.js.php');
			ADMIN_objectversion::newObjectVersion($cid[0], $option);
			break;
		
		case "saveObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'metadata.easysdi.class.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'objectversion.admin.easysdi.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'metadata.admin.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			ADMIN_objectversion::saveObjectVersion($option);
			SITE_object::listObject($option);
			break;
			
		case "cancelObjectVersion":
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'view'.DS.'object.site.easysdi.html.php');
			require_once(JPATH_COMPONENT.DS.'core'.DS.'controller'.DS.'object.site.easysdi.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
			
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
			
			SITE_object::listObject($option);
			break;
			
		default:

		case "listCatalogContent":			
			SITE_catalog::listCatalogContent();
			break;
		case "previewProduct":
			
			HTML_preview::previewProduct($metadata_id= JRequest::getVar('metadata_id'));
			break;
					
	}
//}
 ?>