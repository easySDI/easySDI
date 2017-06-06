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