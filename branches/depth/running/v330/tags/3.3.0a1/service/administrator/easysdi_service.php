<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

if(!defined('DS')) {
	define( 'DS', DIRECTORY_SEPARATOR );
}


// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_service')) {
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Easysdi_service');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
