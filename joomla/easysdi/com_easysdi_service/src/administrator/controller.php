<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
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
		
		require_once JPATH_COMPONENT.'/helpers/easysdi_service.php';

		$view		= JFactory::getApplication()->input->getCmd('view', 'physicalservices');
		JFactory::getApplication()->input->set('view', $view);
	
		parent::display($cachable, $urlparams);
		return $this;
	}
	
	public function negotiation ()
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_service.php';
		Easysdi_serviceHelper::negotiation(JRequest::get( 'get' ));
                // TODO replace by  $jinput = JFactory::getApplication()->input; 
                // Easysdi_serviceHelper::negotiation($jinput);
                // $jinput->get('service');$jinput->get('resurl');$jinput->get('resuser');
	}
	
	/**
	 * Method to redirect to EasySDI home page (driven by easysdi_com_core)
	 *
	 * @since EasySDI 3.3.0
	 */
	public function easySDIHome ()
	{
           	$this->setRedirect('index.php?option=com_easysdi_core');
	}
	
	public function wmtsWebservice () {
		require_once JPATH_COMPONENT.'/helpers/WmtsWebservice.php';
		WmtsWebservice::request($_GET);
	}
	
	public function wfsWebservice () {
		require_once JPATH_COMPONENT.'/helpers/WfsWebservice.php';
		WfsWebservice::request($_GET);
	}
	
	public function wmsWebservice () {
		require_once JPATH_COMPONENT.'/helpers/WmsWebservice.php';
		WmsWebservice::request($_GET);
	}
}
