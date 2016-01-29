<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_core')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Easysdi_core');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
