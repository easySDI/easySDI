<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_contact model.
 */
class Easysdi_contactModeluser extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_CONTACT';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'User', $prefix = 'Easysdi_contactTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
	
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			if (!empty($record->catid)) {
				return $user->authorise('core.delete', 'com_easysdi_contact.category.'.(int) $record->catid);
			}
			// Default to component settings if category not known.
			else {
				return parent::canDelete($record);
			}
		}
	}
	
	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
	
		// Check against the category.
		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_easysdi_contact.category.'.(int) $record->catid);
		}
		// Default to component settings if category not known.
		else {
			return parent::canEditState($record);
		}
	}
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_easysdi_contact.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_easysdi_contact.edit.user.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			$role =& JTable::getInstance('role', 'Easysdi_contactTable');
			$item->organismsRM = $role->loadByUserID($item->id, 1);
			$item->organismsMM = $role->loadByUserID($item->id, 2);
			$item->organismsME = $role->loadByUserID($item->id, 3);
			$item->organismsPM = $role->loadByUserID($item->id, 4);
			$item->organismsVM = $role->loadByUserID($item->id, 5);
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_user');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   11.1
	 */
	public function save($data)
	{
		if(parent::save($data))
		{
			//Delete existing role attribution for this user
			$role =& JTable::getInstance('role', 'Easysdi_contactTable');
			$role->deleteByUserId($data['id']);
			
			//Insert new role attribution
			foreach ($data['organismsRM'] as $organism){
				$array = array();
				$array['user_id'] 		= $this->getItem()->get('id');
				$array['role_id'] 		= 1;
				$array['organism_id'] 	= $organism;
				$role =& JTable::getInstance('role', 'Easysdi_contactTable');
				$role->save($array);
			}
			foreach ($data['organismsMM'] as $organism){
				$array = array();
				$array['user_id'] 		= $this->getItem()->get('id');
				$array['role_id'] 		= 2;
				$array['organism_id'] 	= $organism;
				$role =& JTable::getInstance('role', 'Easysdi_contactTable');
				$role->save($array);
			}
			foreach ($data['organismsME'] as $organism){
				$array = array();
				$array['user_id'] 		= $this->getItem()->get('id');
				$array['role_id'] 		= 3;
				$array['organism_id'] 	= $organism;
				$role =& JTable::getInstance('role', 'Easysdi_contactTable');
				$role->save($array);
			}
			foreach ($data['organismsPM'] as $organism){
				$array = array();
				$array['user_id'] 		= $this->getItem()->get('id');
				$array['role_id'] 		= 4;
				$array['organism_id'] 	= $organism;
				$role =& JTable::getInstance('role', 'Easysdi_contactTable');
				$role->save($array);
			}
			foreach ($data['organismsVM'] as $organism){
				$array = array();
				$array['user_id'] 		= $this->getItem()->get('id');
				$array['role_id'] 		= 5;
				$array['organism_id'] 	= $organism;
				$role =& JTable::getInstance('role', 'Easysdi_contactTable');
				$role->save($array);
			}

			//Instantiate an address JTable
			$addresstable =& JTable::getInstance('address', 'Easysdi_contactTable');

			//Call the overloaded save function to store the input data
			//$data['id'] 			= $this->getItem()->get('id');
			$data['user_id'] 		= $this->getItem()->get('id');
			$data['organism_id']	= null;
			if(!$addresstable->saveByType($data, 'contact' ))
			{	
				return false;
			}
			 
			if(!$addresstable->saveByType($data, 'billing'))
			{
				return false;
			}
			 
			if(!$addresstable->saveByType($data, 'delivry'))
			{
				return false;
			}
			return true;
		}
		return false;
	}

}