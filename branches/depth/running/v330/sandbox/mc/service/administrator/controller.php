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

		$view		= JFactory::getApplication()->input->getCmd('view', 'physicalservices');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	public function negotiation ()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		Easysdi_serviceHelper::negotiation(JRequest::get( 'get' ));
	}
	
	public function wmtsWebservice () {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'WmtsWebservice.php';
		WmtsWebservice::request($_GET);
	}
	
	public function wfsWebservice () {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'WfsWebservice.php';
		WfsWebservice::request($_GET);
	}
	
	public function wmsWebservice () {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'WmsWebservice.php';
		WmsWebservice::request($_GET);
	}
}
