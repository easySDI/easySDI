<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * View 
 */
class Easysdi_coreViewEasysdi extends JView
{
	protected $form;
	
	/**
	 * Display the view
	 */
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->form		= $this->get('Form');
		
		//Check if com_easysdi_service is installed
		$app 				=& JFactory::getApplication();
		$this->service 		= $app->getUserState( 'com_easysdi_service-installed');
		
		// Display the view
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_core.php';

		$canDo	= Easysdi_coreHelper::getActions();
		
		JToolBarHelper::title(JText::_('COM_EASYSDI_CORE_TITLE'), 'easysdi.png');
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_easysdi_core');
		}
	}
}