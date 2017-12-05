<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_contact model.
 */
class Easysdi_contactModeladdress extends JModelAdmin
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
	public function getTable($type = 'Address', $prefix = 'Easysdi_contactTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		//$date is th user id
		// Initialise variables.
		$app	= JFactory::getApplication();
		
		// Get the form.
		$form = $this->loadForm('com_easysdi_contact.address', 'address', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	/**
	 * Method to get the address form to load in one of the user form left fieldsets.
	 *
	 * @param	int		$user_id	User id.
	 * @param	int		$addresstype_id	Address type id.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm 	object on success, false on failure
	 * @since	EasySDI 3.0.0
	 */
	public function getFormForUserForm($user_id, $addresstype_id, $loadData = true)
	{
		//$date is th user id
		// Initialise variables.
		$app	= JFactory::getApplication();
	
		//To load a specific address, user_id and addresstype_id are needed
		$this->user_id = $user_id;
		$this->addresstype_id = $addresstype_id;
	
		// Get the form.
		$form = $this->loadForm('com_easysdi_contact.address', 'address', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_contact.edit.address.data', array());
		
		if (empty($data)) {
			//Load data by using user.id
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	$user_id 			integer		The id of the user.
	 * @param	$addresstype_id 	integer		The id of the addresstype.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	EasySDI 3.0.0
	 */
	public function getItemByUserID($user_id, $addresstype_id)
	{
		$this->user_id = $user_id;
		$this->addresstype_id = $addresstype_id;
		$item = $this->getItem(null);
		
		return $item;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param	$organism_id 		integer		The id of the organism.
	 * @param	$addresstype_id 	integer		The id of the addresstype.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	EasySDI 3.0.0
	 */
	public function getItemByOrganismID($organism_id, $addresstype_id)
	{
		$this->organism_id = $organism_id;
		$this->addresstype_id = $addresstype_id;
		$item = $this->getItem(null);
	
		return $item;
	}
	
	/**
	 * Overloaded method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	EasySDI 3.0.0
	 */
	public function getItem($pk = null)
	{
		//Get Item by User Id
		if(isset($this->user_id)){
			$table = $this->getTable();
			
			$return = $table->loadByUserID($this->user_id,$this->addresstype_id );

			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
			
			return $table;
		}else if(isset($this->organism_id)){//Get Item by Organism Id
			$table = $this->getTable();
			$return = $table->loadByOrganismID($this->organism_id,$this->addresstype_id );

			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
			
			return $table;
		}else{//Get item by Id
			if ($item = parent::getItem($pk)) {
			}
		}
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
                                $query = $db->getQuery(true);
                                $query->select('MAX(ordering)');
                                $query->from('#__sdi_address');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
	}

}