<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

// Execute the task.
$controller	= JControllerLegacy::getInstance('Easysdi_shop');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
