<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_service')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_service.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Easysdi_service');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
