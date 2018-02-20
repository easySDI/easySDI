<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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