<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_monitor
 * @copyright	
 * @license		
 * @author		
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_monitor')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT.'/helpers/easysdi_monitor.php';

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Easysdi_monitor');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
