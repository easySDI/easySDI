<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// No direct access
defined('_JEXEC') or die;

class Easysdi_mapController extends JController
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
		require_once JPATH_COMPONENT.'/helpers/easysdi_map.php';

		// Load the submenu.
		Easysdi_mapHelper::addSubmenu(JRequest::getCmd('view', 'mapcontexts'));

		$view		= JRequest::getCmd('view', 'mapcontexts');
        JRequest::setVar('view', $view);

        // Check for edit form.
        if ($view == 'mapcontext' && $layout == 'edit' && !$this->checkEditId('com_easysdi_map.edit.mapcontext', $id)) {
        	// Somehow the person just went to the form - we don't allow that.
        	$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
        	$this->setMessage($this->getError(), 'error');
        	$this->setRedirect(JRoute::_('index.php?option=com_easysdi_service&view=physicalservices', false));
        
        	return false;
        }
        
		parent::display();

		return $this;
	}
}
