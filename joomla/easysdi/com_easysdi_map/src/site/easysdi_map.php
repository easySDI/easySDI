<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_map
 * @copyright	
 * @license		
 * @author		
 */
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);
$lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);

// Execute the task.
$controller = JControllerLegacy::getInstance('Easysdi_map');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
