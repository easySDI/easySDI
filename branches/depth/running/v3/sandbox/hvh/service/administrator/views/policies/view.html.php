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
class Easysdi_serviceViewPolicies extends JView
{

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$params 			= JComponentHelper::getParams('com_easysdi_service');
		$this->config 		= JRequest::getVar('config',null);
		$this->connector 	= JRequest::getVar('connector', null);
		
		if (!isset($this->config)) {
			JError::raiseWarning(null,JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_LOAD_ERROR'));
			return;
		}
		
		// Check if config file is set.
		$file = $params->get('proxyconfigurationfile');
		if (!isset($file) ) {
			JError::raiseWarning(null, JText::_('COM_EASYSDI_SERVICE_VIRTUALSERVICE_FILE_ERROR_NOT_SET'));
			return ;
		}
		
		$this->xml = simplexml_load_file($file);
		// Check if config file can be loaded.
		if (!isset($this->xml) || !$this->xml) {
			JError::raiseWarning(null, JText::_('COM_EASYSDI_SERVICE_VIRTUALSERVICE_FILE_ERROR_LOADING'));
			return ;
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
		$canDo	= Easysdi_serviceHelper::getActions();
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_POLICIES').' ['.$this->config.']', 'article.png');
		
        if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('policy.add','JTOOLBAR_NEW');
			JToolBarHelper::custom('policy.copy','new-style','','COM_EASYSDI_SERVICE_TOOLBAR_COPY_POLICY');
		}
	    if ($canDo->get('core.edit')) {
		    JToolBarHelper::editList('policy.edit','JTOOLBAR_EDIT');
	    }
        if( $canDo->get('core.delete')) {
		    JToolBarHelper::deleteList('', 'policy.delete','JTOOLBAR_DELETE');
		} 
		JToolBarHelper::divider();
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_service&view=virtualservices');
	}
}
