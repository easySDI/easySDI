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
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.html.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'catalog.site.easysdi.class.php');
require_once(JPATH_COMPONENT.DS.'core'.DS.'geoMetadata.php');

$language=&JFactory::getLanguage();
$language->load('com_easysdi_catalog');

?>
<?php
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');


switch($task){
	default:
	case "listCatalogContent":			
		SITE_catalog::listCatalogContent();
		break;		
}
 ?>