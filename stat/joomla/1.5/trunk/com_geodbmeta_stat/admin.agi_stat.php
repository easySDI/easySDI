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
jimport("joomla.utilities.date");

include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'component.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'content.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'plugin.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'menu.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'section.php');
include_once(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table'.DS.'user.php');

JHTML::_('stylesheet', 'com_easysdi_core.css', 'administrator/components/com_easysdi_core/templates/css/');

$language =& JFactory::getLanguage();
$language->load('com_agi_stat', JPATH_ADMINISTRATOR);

global $mainframe;
$option = JRequest::getVar('option');
$task = JRequest::getVar('task');

switch($task){

	case "statistic":
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'view'.DS.'statistic.admin.agi.html.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'controller'.DS.'statistic.admin.agi.php');
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'toolbar'.DS.'statistic.toolbar.agi.html.php');
		
		TOOLBAR_statistic::listStatistic();
		ADMIN_statistic::listStatistic($option);	
		break;
		
	case "ctrlPanelAGIStat":
	default:
		$mainframe->redirect("index.php?option=com_easysdi_core" );
		break;
}

?>