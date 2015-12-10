<?php
/*------------------------------------------------------------------------
# easysdi_processing.php - Easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2015. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Added for Joomla 3.0
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
};

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_easysdi_processing')){
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
};

// Load cms libraries
JLoader::registerPrefix('J', JPATH_PLATFORM . '/cms');
// Load joomla libraries without overwrite
JLoader::registerPrefix('J', JPATH_PLATFORM . '/joomla',false);

// require helper files
JLoader::register('Easysdi_processingHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'easysdi_processing.php');
JLoader::register('Easysdi_processingStatusHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'easysdi_processing_status.php');
JLoader::register('Easysdi_processingParamsHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'easysdi_processing_params.php');

// import joomla controller library
jimport('joomla.application.component.controller');


$controller = JControllerLegacy::getInstance('Easysdi_processing');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();

?>