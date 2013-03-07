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
class Easysdi_mapModellayer extends JModelAdmin
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
	public function getTable($type = 'Layer', $prefix = 'Easysdi_mapTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_map.layer', 'layer', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.layer.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			
			
			//Support for multiple or not foreign key field: group_id
			$array = array();
			foreach((array)$data->group_id as $value):
			if(!is_array($value)):
			$array[] = $value;
			endif;
			endforeach;
			$data->group_id = implode(',',$array);

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
			if(isset($item->service_id))
				$item->service_id = $item->servicetype.'_'.$item->service_id;
			if(isset($item->layername))
				$item->onloadlayername = $item->layername;
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		$jform = JRequest::getVar('jform');
		
		if (!isset($jform['isdefaultvisible'])) { // see if the checkbox has been submitted
			$table->isdefaultvisible = 0; // if it has not been submitted, mark the field unchecked
		}
		if (!isset($jform['istiled'])) { // see if the checkbox has been submitted
			$table->istiled = 0; // if it has not been submitted, mark the field unchecked
		}
		if (!isset($jform['asOL'])) { // see if the checkbox has been submitted
			$table->asOL = 0; // if it has not been submitted, mark the field unchecked
			$table->asOLoptions = "";
			$table->asOLstyle = "";
			$table->asOLmatrixset = "";
		}
		
		$service_id 	= $jform['service_id'];
		$pos 			= strstr ($service_id, 'physical_');
		if($pos){
			$table->service_id 	= substr ($service_id, strrpos ($service_id, '_')+1);
			$table->servicetype 		= 'physical';
		}
		else {
			$table->service_id		= substr ($service_id, strrpos ($service_id, '_')+1);
			$table->servicetype		= 'virtual';
		}
		
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_maplayer');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
		
		if (empty($table->alias)){
			$table->alias = $table->name;
		}
	}

}