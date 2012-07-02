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
class Easysdi_serviceViewConfigs extends JView
{

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$params = JComponentHelper::getParams('com_easysdi_core');
		
		$this->xml = simplexml_load_file($params->get('proxyconfigurationfile'));
				
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

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
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		
		$canDo	= Easysdi_serviceHelper::getActions();
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_CONFIGS'), 'proxy.png');
		
        if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('config.add','JTOOLBAR_NEW');
		}
	    if ($canDo->get('core.edit')) {
		    JToolBarHelper::editList('config.edit','JTOOLBAR_EDIT');
	    }
		        
        if( $canDo->get('core.delete')) {
		    JToolBarHelper::deleteList('', 'config.delete','JTOOLBAR_EMPTY_TRASH');
		    JToolBarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
		    JToolBarHelper::trash('config.trash','JTOOLBAR_TRASH');
		    JToolBarHelper::divider();
		}
        
		JToolBarHelper::divider();
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_core');
	}
}
