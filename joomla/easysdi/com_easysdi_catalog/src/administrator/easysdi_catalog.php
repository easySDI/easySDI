<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_catalog')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_easysdi_core/models/fields');

$controller	= JControllerLegacy::getInstance('Easysdi_catalog');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
