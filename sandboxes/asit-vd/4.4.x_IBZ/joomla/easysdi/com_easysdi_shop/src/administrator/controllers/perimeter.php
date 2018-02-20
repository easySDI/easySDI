<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Perimeter controller class.
 */
class Easysdi_shopControllerPerimeter extends JControllerForm
{

    function __construct() {
        $this->view_list = 'perimeters';
        parent::__construct();
    }
    
    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array   $data  An array of input data.
     * @param   string  $key   The name of the key for the primary key.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
    	// Initialise variables.
    	$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
    	
    	if($recordId < 3)
    		return false;
    	    	
        return parent::allowEdit($data, $key);
    }

}