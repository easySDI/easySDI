<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_monitor
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');


//$monitorUrl = config_easysdi::getValue("MONITOR_URL");

// Execute the task.
$controller	= JControllerLegacy::getInstance('Easysdi_monitor');
$controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
