<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('Easysdi_service');
$controller->execute(JRequest::getVar('task',''));
$controller->redirect();
