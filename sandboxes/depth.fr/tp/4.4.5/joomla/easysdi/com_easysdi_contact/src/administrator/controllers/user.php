<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * User controller class.
 */
class Easysdi_contactControllerUser extends JControllerForm
{
//$params'] =JComponentHelper::getParams('com_easysdi_core');
//$this->params->get('serviceaccount');

    function __construct() {
    	
    	//Need to be add here even if it is in administrator/controller.php l.27
    	require_once JPATH_COMPONENT.'/helpers/easysdi_contact.php';
        
    	$this->view_list ='users';
        parent::__construct();

    }
    
    /**
     * Method override to check if you can add a new record.
     *
     * @param   array  $data  An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = array())
    {
    	// Initialise variables.
    	$user		= JFactory::getUser();
    	$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
    	$allow		= null;
    
    	if ($categoryId)
    	{
    		// If the category has been passed in the URL check it.
    		$allow	= $user->authorise('core.create', $this->option . '.category.' . $categoryId);
    	}
    
    	if ($allow === null)
    	{
    		// In the absence of better information, revert to the component permissions.
    		return parent::allowAdd($data);
    	}
    	else
    	{
    		return $allow;
    	}
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
    	$user		= JFactory::getUser();
    	$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
    	$categoryId = 0;
    
    	if ($recordId)
    	{
    		$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
    	}
    
    	if ($categoryId)
    	{
    		// The category has been set. Check the category permissions.
    		return $user->authorise('core.edit', $this->option . '.category.' . $categoryId);
    	}
    	else
    	{
    		// Since there is no asset tracking, revert to the component permissions.
    		return parent::allowEdit($data, $key);
    	}
    }
}