<?php
/**
 * @version     4.4.2
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('Easysdi_service');
$controller->execute(JRequest::getVar('task',''));
$controller->redirect();
