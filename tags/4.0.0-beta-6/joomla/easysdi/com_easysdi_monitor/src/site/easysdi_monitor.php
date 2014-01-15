<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');


//$monitorUrl = config_easysdi::getValue("MONITOR_URL");

// Execute the task.
$controller	= JControllerLegacy::getInstance('Easysdi_monitor');
$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
