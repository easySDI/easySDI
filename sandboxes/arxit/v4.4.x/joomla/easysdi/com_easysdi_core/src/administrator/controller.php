<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// No direct access
defined('_JEXEC') or die;

class Easysdi_coreController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
		//Check if others EasySDI components are installed and saved results in UserState
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		
		$view		= JFactory::getApplication()->input->getCmd('view', 'easysdi');
		$layout		= JFactory::getApplication()->input->getCmd('layout', 'edit');
		$id		= JFactory::getApplication()->input->getInt('id');
                
                JFactory::getApplication()->input->set('view', $view);
	
		parent::display($cachable, $urlparams);

		return $this;
	}
}
