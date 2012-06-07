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

jimport('joomla.application.component.controllerform');

/**
 * User controller class.
 */
class Easysdi_coreControllerUser extends JControllerForm
{
//$params = JComponentHelper::getParams('com_easysdi_core');
//$this->params->get('serviceaccount');

    function __construct() {
    	
    	//Need to be add here even if it is in administrator/controller.php l.27
    	require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
        
    	$this->view_list = 'users';
        parent::__construct();

    }

}