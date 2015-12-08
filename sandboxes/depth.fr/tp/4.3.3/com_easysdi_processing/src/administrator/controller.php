<?php
/*------------------------------------------------------------------------
# controller.php - Easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2015. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of Cadastre component
 */
class Easysdi_processingController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false) {
                require_once JPATH_COMPONENT . '/helpers/easysdi_processing.php';

                $view = JFactory::getApplication()->input->getCmd('view', 'processings');
                JFactory::getApplication()->input->set('view', $view);

                parent::display($cachable, $urlparams);

                return $this;

		// set default view if not set
		/*Request::setVar('view', JRequest::getCmd('view', 'Easysdi_processing'));

		// call parent behavior
		parent::display($cachable);

		// set view
		$view = strtolower(JRequest::getVar('view'));*/

	}
}
?>