<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

if(!defined('DS')) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_map')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_map.php';

// Include dependancies
jimport('joomla.application.component.controller');
$controller	= JController::getInstance('Easysdi_map');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
