<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);

// Execute the task.
$controller = JControllerLegacy::getInstance('Easysdi_map');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
