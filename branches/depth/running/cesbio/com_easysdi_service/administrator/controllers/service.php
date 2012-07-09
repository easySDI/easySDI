<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Service controller class.
 */
class Easysdi_serviceControllerService extends JControllerForm
{

    function __construct() {
    	
    	//Need to be add here even if it is in administrator/controller.php 
    	require_once JPATH_COMPONENT.'/helpers/easysdi_service.php';
    	
        $this->view_list = 'services';
        parent::__construct();
    }

}