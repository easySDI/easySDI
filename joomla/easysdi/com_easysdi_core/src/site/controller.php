<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class Easysdi_coreController extends JControllerLegacy
{

    public function display($cachable = false, $urlparams = array())
	{
            $app = JFactory::getApplication();
            if($app->input->get('resource', '', 'STRING'))
                $app->setUserState('com_easysdi_core.edit.applicationresource.id', $app->input->get('resource', '', 'STRING'));
            parent::display($cachable, $urlparams);
                    
        }
}