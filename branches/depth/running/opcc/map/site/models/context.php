<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_service'.DS.'tables'.DS.'physicalservice.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_service'.DS.'tables'.DS.'virtualservice.php';

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelContext extends JModelForm
{
    
    var $_item = null;
    
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_easysdi_map');

		// Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_map.edit.context.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_map.edit.context.id', $id);
        }
		$this->setState('context.id', $id);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('context.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				if ($table->state != 1) 
					return $this->_item;
				
				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
				
				//Get the unit value
				$db = JFactory::getDbo();
				$db->setQuery('SELECT alias FROM #__sdi_sys_unit WHERE id='.$this->_item->unit_id);
				$unit = $db->loadResult();
				$this->_item->unit = $unit;
				
				//Load the groups
				$groupTable 	= JTable::getInstance('group', 'easysdi_mapTable');
				$groups 		= $groupTable->GetIdsByContextId($id);
				$this->_item->groups = array();
				foreach($groups as $group )
				{
					$groupTable 	= JTable::getInstance('group', 'easysdi_mapTable');
					$groupTable->load($group->id, true);
					$groupTable->isbackground = $group->isbackground;
					$groupTable->isdefault = $group->isdefault;
					$this->_item->groups[] =$groupTable;
				}
				
				//Load the services
				$physicalserviceTable 	= JTable::getInstance('physicalservice', 'easysdi_serviceTable');
				$services 		= $physicalserviceTable->GetIdsByContextId($id);
				$this->_item->physicalservices = array();
				foreach($services as $service )
				{
					$physicalserviceTable 	= JTable::getInstance('physicalservice', 'easysdi_serviceTable');
					$physicalserviceTable->loadWithAccessInheritance($service, true);
					if($physicalserviceTable->state == 0)
						continue;
					$this->_item->physicalservices[] =$physicalserviceTable;
				}
				$virtualserviceTable 	= JTable::getInstance('virtualservice', 'easysdi_serviceTable');
				$services 		= $virtualserviceTable->GetIdsByContextId($id);
				$this->_item->virtualservices = array();
				foreach($services as $service )
				{
					$virtualserviceTable 	= JTable::getInstance('virtualservice', 'easysdi_serviceTable');
					$virtualserviceTable->load($service, true);
					$this->_item->virtualservices[] =$virtualserviceTable;
				}
				
				//Load the tools
				$toolTable 	= JTable::getInstance('tool', 'easysdi_mapTable');
				$tools 		= $toolTable->GetIdsByContextId($id);
				$this->_item->tools = array();
				foreach($tools as $tool )
				{
					$toolTable 	= JTable::getInstance('tool', 'easysdi_mapTable');
					$toolTable->load($tool, true);
					$this->_item->tools[]  =$toolTable;
				}
			} 
			elseif ($error = $table->getError()) 
			{
				$this->setError($error);
			}
		}

		return $this->_item;
	}
    
	
	
	
	public function getTable($type = 'Context', $prefix = 'Easysdi_mapTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     

    
	/**
	 * Method to check in an item.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int)$this->getState('context.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int)$this->getState('context.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}    
    
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
     * 
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_easysdi_map.context', 'context', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getData(); 
        
        return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('context.id');
        $user = JFactory::getUser();

        if($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'context.'.$id);
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_easysdi_map');
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

		$table = $this->getTable();
        if ($table->save($data) === true) {
            return $id;
        } else {
            return false;
        }
        
	}    
    
}