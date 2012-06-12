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
//$params'] =JComponentHelper::getParams('com_easysdi_core');
//$this->params->get('serviceaccount');

    function __construct() {
    	
    	//Need to be add here even if it is in administrator/controller.php l.27
    	require_once JPATH_COMPONENT.'/helpers/easysdi_core.php';
        
    	$this->view_list ='users';
        parent::__construct();

    }
    
    /**
     * Overloaded method to save a single user record.
     *
     * @since	EasySDI 3.0.0
     */
    public function save($key =null, $urlVar =null)
    {
    	//Save user first
     	parent::save($key, $urlVar);
    	
    	//Then save addresses
     	$data =JRequest::getVar('jform', array(), 'post', 'array');
     	
     	//Instantiate an address JTable
     	$addresstable =& JTable::getInstance('address', 'Easysdi_coreTable');
    	
    	//Call the overloaded save function to store the input data
    	if(!$addresstable->save($data, 'billing'))
    	{
    		$this->setError($billingaddresstable->getError());
    		$this->setMessage($this->getError(), 'error');
    		$this->setRedirect(
    				JRoute::_(
    						'index.php?option=' . $this->option . '&view=' . $this->view_list
    						. $this->getRedirectToListAppend(), false
    				)
    		);
    		return false;
    	}
    	
    	if(!$addresstable->save($data, 'contact'))
    	{
    		$this->setError($billingaddresstable->getError());
    		$this->setMessage($this->getError(), 'error');
    		$this->setRedirect(
    				JRoute::_(
    						'index.php?option=' . $this->option . '&view=' . $this->view_list
    						. $this->getRedirectToListAppend(), false
    				)
    		);
    		return false;
    	}
    	
    	if(!$addresstable->save($data, 'delivry'))
    	{
    		$this->setError($billingaddresstable->getError());
    		$this->setMessage($this->getError(), 'error');
    		$this->setRedirect(
    				JRoute::_(
    						'index.php?option=' . $this->option . '&view=' . $this->view_list
    						. $this->getRedirectToListAppend(), false
    				)
    		);
    		return false;
    	}
    }
}