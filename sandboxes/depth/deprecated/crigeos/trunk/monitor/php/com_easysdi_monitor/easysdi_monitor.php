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


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
$view = JRequest::getVar( 'view' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

//Add base sub menus
/*
if($view == "jobs"){
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_JOBS'), 'index.php?option=com_easysdi_monitor&view=jobs', true);
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_REPORTS'), 'index.php?option=com_easysdi_monitor&view=reports');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_ALERTS'), 'index.php?option=com_easysdi_monitor&view=alerts');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_MAINTENANCE'), 'index.php?option=com_easysdi_monitor&view=maintenance');
}else if($view == "reports"){
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_JOBS'), 'index.php?option=com_easysdi_monitor&view=jobs');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_REPORTS'), 'index.php?option=com_easysdi_monitor&view=reports', true);
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_ALERTS'), 'index.php?option=com_easysdi_monitor&view=alerts');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_MAINTENANCE'), 'index.php?option=com_easysdi_monitor&view=maintenance');
}else if($view == "alerts"){
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_JOBS'), 'index.php?option=com_easysdi_monitor&view=jobs');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_REPORTS'), 'index.php?option=com_easysdi_monitor&view=reports');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_ALERTS'), 'index.php?option=com_easysdi_monitor&view=alerts', true);
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_MAINTENANCE'), 'index.php?option=com_easysdi_monitor&view=maintenance');
}else if($view == "maintenance"){
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_JOBS'), 'index.php?option=com_easysdi_monitor&view=jobs');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_REPORTS'), 'index.php?option=com_easysdi_monitor&view=reports');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_ALERTS'), 'index.php?option=com_easysdi_monitor&view=alerts');
	JSubMenuHelper::addEntry(JText::_('EASYSDI_MONITOR_SUBMENU_MAINTENANCE'), 'index.php?option=com_easysdi_monitor&view=maintenance', true);
}
*/

$monitorUrl = config_easysdi::getValue("MONITOR_URL");

if($monitorUrl == ""){
	$mainframe->enqueueMessage(JTEXT::_("EASYSDI_MONITOR_URL_UNDEFINED","ERROR"));
	$mainframe->redirect("index.php?option=com_easysdi_core&task=listConfig");
}

// Create the controller
$classname	= 'MonitorController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
