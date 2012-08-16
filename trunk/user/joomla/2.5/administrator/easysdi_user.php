<?php
/**
 * @version     3.0.0
  * @package     com_easysdi_user
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_user')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_user.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Easysdi_user');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
