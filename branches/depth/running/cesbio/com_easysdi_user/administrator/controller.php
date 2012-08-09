<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// No direct access
defined('_JEXEC') or die;

class Easysdi_coreController extends JController
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
		$app 				=& JFactory::getApplication();
		$db 				= JFactory::getDbo();
		$db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE name = "com_easysdi_shop"');
		$app->setUserState( 'com_easysdi_shop-installed' ,$db->loadResult() == 0 ? false : true);
		$db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE name = "com_easysdi_service"');
		$app->setUserState( 'com_easysdi_service-installed' ,$db->loadResult() == 0 ? false : true);
		
		$view		= JRequest::getCmd('view', 'easysdi');
		$layout 	= JRequest::getCmd('layout', 'edit');
		$id			= JRequest::getInt('id');
        JRequest::setVar('view', $view);
        
        // Check for edit form.
        if ($view == 'user' && $layout == 'edit' && !$this->checkEditId('com_easysdi_core.edit.user', $id)) {
        	// Somehow the person just went to the form - we don't allow that.
        	$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
        	$this->setMessage($this->getError(), 'error');
        	$this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=users', false));
        
        	return false;
        }

		parent::display();

		return $this;
	}
	
	public function negotiation ()
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
		Easysdi_coreHelper::negotiation(JRequest::get( 'get' ));
	}
}
