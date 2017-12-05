<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelgroup extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_MAP';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Group', $prefix = 'Easysdi_mapTable', $config = array())
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
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_easysdi_map.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.group.data', array());

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
									
			$layertable 			= JTable::getInstance('layer', 'easysdi_mapTable');
			$item->layersselected 	= $layertable->loadItemsIdByGroup($item->id);
			
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

		$jform = JRequest::getVar('jform');
		if (!isset($jform['isdefaultopen'])) { // see if the checkbox has been submitted
			$table->isdefaultopen = 0; // if it has not been submitted, mark the field unchecked
		}

		//Group id is set to default value '0' in case of creation.
		//So this section of code is never executed.
		//Ordering is set in sdiTable->check() function.
		//However, We keep this section in case of default id was not set to '0' anymore (changes in form xml)
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('MAX(ordering)');
                                $query->from('#__sdi_layergroup');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
		
		if (empty($table->alias)){
			$table->alias = $table->name;
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
			$db = JFactory::getDbo();
			
			//Save relations for the selected layer
			$layers 	= $data['layersselected'];
			
			//Get existing relations for the current group
			$query = $db->getQuery(true);
			try {
				$query
				->select('layer_id')
				->from('#__sdi_layer_layergroup ')
				->where('group_id= '. (int)$this->getItem()->get('id'));
				$db->setQuery($query);
				$pks = $db->loadColumn();
			} catch (Exception $e) {
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR" ) );
				return false;
			}			
			
			//Clean up the database from layers no more selected
                        if(is_array($pks)){
			foreach ($pks as $pk)
			{
				if(is_array($layers) && in_array($pk,$layers))//Existing layer
				{
					//Remove this layer from the selected list, because it doesn't have to be changed in the database
					if(($key = array_search($pk, $layers)) !== false) {
					    unset($layers[$key]);
					}
				}
				else //Layer is no more selected, delete the relation
				{
					$query = $db->getQuery(true);
					$query
					->delete('#__sdi_layer_layergroup')
					->where('group_id= '. (int)$this->getItem()->get('id'))
					->where('layer_id =' .$pk);
					$db->setQuery($query);
					try {
						// Execute the query in Joomla 3.0.
						$result = $db->execute();
					} catch (Exception $e) {
						$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR" ) );
						return false;
					}
				}
			}
                        }
			
			//Select max ordering for the layers of the current group
			$query = $db->getQuery(true);
			try{
				$query
				->select('MAX(ordering)')
				->from('#__sdi_layer_layergroup ')
				->where('group_id= '. (int)$this->getItem()->get('id'));
				$db->setQuery($query);
				$ordering = $db->loadResult();
			} catch (Exception $e) {
				// catch any database errors.
				$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR" ) );
				return false;
			}
			if(!$ordering)
				$ordering = 0;
			
			//Insert the new relation
                        if(is_array($layers)){
			foreach ($layers as $layer)
			{
				if(empty($layer))
					continue;
				$ordering ++;
				//Store layer-group relation
				$columns = array('group_id', 'layer_id', 'ordering');
				$values = array($this->getItem()->get('id'), $layer, $ordering);
				$query = $db->getQuery(true);
				$query
				->insert($db->quoteName('#__sdi_layer_layergroup'))
				->columns($db->quoteName($columns))
				->values(implode(',', $values));
				$db->setQuery($query);
				try {
					// Execute the query in Joomla 3.0.
					$result = $db->execute();
				} catch (Exception $e) {
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR" ) );
					return false;
				}
			}
                        }
			return true;
		}
	}
	
	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array    $pks    An array of primary key ids.
	 * @param   integer  $order  +1 or -1
	 *
	 * @return  mixed
	 *
	 * @since   12.2
	 */
	public function saveorder($pks = null, $order = null)
	{
		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}
	
		//Get if a filter on group was set.
		$app = JFactory::getApplication('administrator');
		$map = $app->getUserStateFromRequest('com_easysdi_map.groups.filter.map', 'filter_map', null, 'int');
		//No filter : standard saving process
		if(empty($map))
		{
			parent::saveorder($pks, $order);
		}
	
		$table = $this->getTable();
		$conditions = array();
	
		//A filter on group is set : Update ordering inside this group
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);
				
			// Access checks.
			if (!$this->canEditState($table))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
			}
			else
			{
				$order = $i + 1;
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query
				->update($db->quoteName('#__sdi_map_layergroup'))
				->set('ordering='.$order)
				->where('map_id= '. (int)$map)
				->where('group_id= '. (int)$pks[$i]);
				$db->setQuery($query);
				try {
					$result = $db->execute();
				} catch (Exception $e) {
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_DELBACKGROUND_FAIL_GROUP_ERROR" ) );
					return false;
				}
			}
		}
	
		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}
	
		// Clear the component's cache
		$this->cleanCache();
	
		return true;
	}

}