<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewConfig extends JView
{

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->id = JRequest::getVar('id',null);
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_CONFIG'), 'service.png');
		JToolBarHelper::save('config.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_service&view=configs');
	}
}
