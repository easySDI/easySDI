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
 * Service controller class.
 */
class Easysdi_coreControllerService extends JControllerForm
{

    function __construct() {
    	
    	//Need to be add here even if it is in administrator/controller.php l.27
    	require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
    	
        $this->view_list = 'services';
        parent::__construct();
    }
    
    /**
     * Overloaded method to save a single service record.
     *
     * @since	EasySDI 3.0.0
     */
    public function save($key =null, $urlVar =null)
    {
    	//Save service first
    	parent::save($key, $urlVar);
    	
    	
    	//then save service compliance
    	$data =JRequest::getVar('compliance', null);
    	if(!isset($data))
    		return;
    	
    	$compliance_ids = explode(',', $data);

    	print_r($compliance_ids);
    	die();
    	 
    }
}