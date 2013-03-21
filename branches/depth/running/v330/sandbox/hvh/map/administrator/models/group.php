<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
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
			$item->layers 			= $layertable->loadItemsByGroup($item->id);
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
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_layergroup');
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
			//Delete previous layers relations
			$db->setQuery('DELETE FROM #__sdi_layer_layergroup WHERE group_id = '.$this->getItem()->get('id'));
			$db->query();
						
			//Save new layers relations
			$layers 	= $data['layersselected'];
			$i = 1;
			foreach ($layers as $layer)
			{
				//Store layer group relation
				$db->setQuery('INSERT INTO #__sdi_layer_layergroup ( group_id, layer_id, ordering) VALUES ('.$this->getItem()->get('id').', '.$layer.', '.$i.')');
				if(!$db->query())
				{
					$this->setError( JText::_( "COM_EASYSDI_MAP_FORM_MAP_SAVE_FAIL_GROUP_ERROR" ) );
					return false;
				}
				$i ++;
			}
			return true;
		}
	}

}