<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// No direct access
defined('_JEXEC') or die;

class Easysdi_serviceController extends JController
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
		require_once JPATH_COMPONENT.'/helpers/easysdi_service.php';

		// Load the submenu.
		Easysdi_serviceHelper::addSubmenu(JRequest::getCmd('view', 'services'));

		$view		= JRequest::getCmd('view', 'services');
        JRequest::setVar('view', $view);

		parent::display();

		return $this;
	}
	
	public function negotiation ()
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_service.php';
		Easysdi_coreHelper::negotiation(JRequest::get( 'get' ));
	}
}
