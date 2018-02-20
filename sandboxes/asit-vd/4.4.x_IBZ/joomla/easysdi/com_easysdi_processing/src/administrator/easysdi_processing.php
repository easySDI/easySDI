<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

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

//get sdiFactory
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

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