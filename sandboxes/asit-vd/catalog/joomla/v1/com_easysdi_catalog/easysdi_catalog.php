<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community 
 * For more information : www.easysdi.org h 
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
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'preview.site.easysdi.html.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'geoMetadata.php');

$language=&JFactory::getLanguage();
$language->load('com_easysdi_catalog');

?>
<?php
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');
$view = JRequest::getVar('view');

if ($view)
{
	switch($view){
		case "catalog":
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