<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

class Easysdi_serviceController extends JControllerLegacy
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
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';

		// Load the submenu.
		Easysdi_serviceHelper::addSubmenu(JRequest::getCmd('view', 'physicalservices'));
		
// 		$layout 	= JFactory::getApplication()->input->getCmd('layout', 'edit');
// 		$id			= JFactory::getApplication()->input->getInt('id');
		$view		= JFactory::getApplication()->input->getCmd('view', 'physicalservices');
		JFactory::getApplication()->input->set('view', $view);
	
        //TODO correct with physicalservice and/or virtualservice
        // Check for edit form.
//         if ($view == 'physicalservice' && $layout == 'edit' && !$this->checkEditId('com_easysdi_service.edit.physicalservice', $id)) {
//         	// Somehow the person just went to the form - we don't allow that.
//         	$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
//         	$this->setMessage($this->getError(), 'error');
//         	$this->setRedirect(JRoute::_('index.php?option=com_easysdi_service&view=physicalservices', false));
        
//         	return false;
//         }

		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	public function negotiation ()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		Easysdi_serviceHelper::negotiation(JRequest::get( 'get' ));
	}
}
