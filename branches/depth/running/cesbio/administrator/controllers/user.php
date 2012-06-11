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
    
    public function save($key = null, $urlVar = null)
    {
    	//Save user first
//     	parent::save($key, $urlVar);
    	
    	//Then save address
    	$billingaddressmodel = & JModel::getInstance('address', 'Easysdi_coreModel');
    	$billingdata = JRequest::getVar('jform', array(), 'post', 'array');
    	
    	
    	
    	// Validate the posted data.
    	$billingform = $billingaddressmodel->getForm($billingdata, false);
    	
    	if (!$billingform)
    	{
    		$app->enqueueMessage($model->getError(), 'error');
    		return false;
    	}
    	
    	// Test whether the data is valid.
    	$validData = $billingaddressmodel->validate($billingform, $billingdata);
    	
    	// Check for validation errors.
    	if ($validData === false)
    	{
    		// Get the validation messages.
    		$errors = $billingaddressmodel->getErrors();
    	
    		// Push up to three validation messages out to the user.
    		for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
    		{
    		if ($errors[$i] instanceof Exception)
    		{
    		$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
    		}
    		else
    		{
    		$app->enqueueMessage($errors[$i], 'warning');
    		}
    		}
    		return false;
    	}
    	
    	// Attempt to save the data.
    		if (!$billingaddressmodel->save($validData))
    		{
	    		// Save the data in the session.
	    		$app->setUserState($context . '.data', $validData);
    	
    			// Redirect back to the edit screen.
    			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
    			$this->setMessage($this->getError(), 'error');
    	
    			$this->setRedirect(JRoute::_(
    							'index.php?option=' . $this->option . '&view=' . $this->view_item
    							. $this->getRedirectToItemAppend($recordId, $key), false
    							)
    							);
    			return false;
    		}
    	
    	
    }

}