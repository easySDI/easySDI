<?php
/**
 * @version     3.0.0
  * @package     com_easysdi_user
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
		
		//Check if others easysdi components are installed
		$app 				= JFactory::getApplication();
		$this->buttons = array();
		
		//com_easysdi_user
		if($app->getUserState( 'com_easysdi_user-installed')){
			array_push($this->buttons,array(
											'link' 		=> JRoute::_('index.php?option=com_easysdi_user'),
											'image'		=> '../../../templates/bluestork/images/header/icon-48-user.png',
											'text'		=> JText::_('COM_EASYSDI_CORE_ICON_SDI_USER'),
											'access'	=> array(	'core.manage'		 , 'com_easysdi_user')
											));
		}
		
		//com_easysdi_service
		if($app->getUserState( 'com_easysdi_service-installed')){
			array_push($this->buttons,array(
											'link' 		=> JRoute::_('index.php?option=com_easysdi_service'),
											'image' 	=> '../../../templates/bluestork/images/header/icon-48-links.png',
											'text' 		=> JText::_('COM_EASYSDI_CORE_ICON_SDI_SERVICE'),
											'access' 	=> array(	'core.manage'		 , 'com_easysdi_service')
			));
		}
					
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
		JToolBarHelper::title(JText::_('COM_EASYSDI_CORE_TITLE'), 'easysdi.png');
		
		$canDo	= Easysdi_coreHelper::getActions();
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_easysdi_core');
		}
	}
}