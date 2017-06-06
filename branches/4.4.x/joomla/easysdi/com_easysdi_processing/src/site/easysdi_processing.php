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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

// Set the component css/js
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_processing/assets/css/main.css?v=' . sdiFactory::getSdiFullVersion());


// Require helper file
JLoader::register('Easysdi_processingHelper',           JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing.php');
JLoader::register('Easysdi_processingParamsHelper',     JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing_params.php');
JLoader::register('Easysdi_processingStatusHelper',     JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing_status.php');



// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Cadastre
$controller = JControllerLegacy::getInstance('Easysdi_processing');

// Perform the request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>