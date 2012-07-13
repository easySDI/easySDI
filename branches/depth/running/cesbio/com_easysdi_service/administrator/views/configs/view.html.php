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
		$this->params = JComponentHelper::getParams('com_easysdi_core');
		$this->addToolbar();
		
		// Check if config file is set.
		$file = $this->params->get('proxyconfigurationfile');
		if (!isset($file) ) {
			JError::raiseWarning(null, JText::_('COM_EASYSDI_SERVICE_CONFIG_FILE_ERROR_NOT_SET'));
			return ;
		}
		
		$this->xml = simplexml_load_file($file);
		// Check if config file can be loaded.
		if (!isset($this->xml) || !$this->xml) {
			JError::raiseWarning(null, JText::sprintf('COM_EASYSDI_SERVICE_CONFIG_FILE_ERROR_LOADING',$file));
			return ;
		}
		
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
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_CONFIGS'), 'module.png');
		
        if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('config.add','JTOOLBAR_NEW');
		}
	    if ($canDo->get('core.edit')) {
		    JToolBarHelper::editList('config.edit','JTOOLBAR_EDIT');
	    }
        if( $canDo->get('core.delete')) {
		    JToolBarHelper::deleteList('', 'config.delete','JTOOLBAR_DELETE');
		} 
		JToolBarHelper::divider();
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_core');
	}
}
