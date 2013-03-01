<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_service model.
 */
class Easysdi_serviceModelvirtualservice extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EASYSDI_SERVICE';


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Virtualservice', $prefix = 'Easysdi_serviceTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_service.virtualservice', 'virtualservice', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.virtualservice.data', array());

		if (empty($data)) {
			$data = $this->getItem();
			
			//Support for multiple or not foreign key field: proxytype_id
			$array = array();
			foreach((array)$data->proxytype_id as $value):
			if(!is_array($value)):
			$array[] = $value;
			endif;
			endforeach;
			$data->proxytype_id = implode(',',$array);
			
			//Support for multiple or not foreign key field: exceptionlevel_id
			$array = array();
			foreach((array)$data->exceptionlevel_id as $value):
			if(!is_array($value)):
			$array[] = $value;
			endif;
			endforeach;
			$data->exceptionlevel_id = implode(',',$array);
			
			//Support for multiple or not foreign key field: loglevel_id
			$array = array();
			foreach((array)$data->loglevel_id as $value):
			if(!is_array($value)):
			$array[] = $value;
			endif;
			endforeach;
			$data->loglevel_id = implode(',',$array);
			
			//Support for multiple or not foreign key field: logroll_id
			$array = array();
			foreach((array)$data->logroll_id as $value):
			if(!is_array($value)):
			$array[] = $value;
			endif;
			endforeach;
			$data->logroll_id = implode(',',$array);
            
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

			//Do any procesing on fields here if needed
			//inserting virtualmetadata content in virtualservice for display of edit form
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'tables');
			$metadata =& JTable::getInstance('virtualmetadata', 'Easysdi_serviceTable');
			$metadata->loadByVirtualServiceID(JRequest::getVar('id',null));
			$item_fields = Array();
			foreach ($item as $key => $value) {
				$item_fields[] = $key;
			}
			foreach ($metadata->getFields() as $field) {
				if (!in_array($field->Field, $item_fields)) {
					$item->{$field->Field} = $metadata->{$field->Field};
				}
			}
			
			$compliances = $this->getServiceCompliance($item->id);
			$compliance_ids =array();
			$compliance_values =array();
				
			if(isset($compliances))
			{
				foreach ($compliances as $compliance)
				{
					$compliance_ids[] =$compliance->id;
					$compliance_values[] =$compliance->value;
				}
			}
			if(count($compliance_ids)>0)
				$item->compliance = json_encode($compliance_values);
			else
				$item->compliance = '';
			$item->supportedversions = json_encode($compliance_values);
			
			$item->physicalservice_id = $this->getPhysicalServiceAggregation($item->id);
		}

		return $item;
	}
/*
public function getItem($pk = null)
	{
		//Get Item 
		if(isset($this->alias)){
			$table = $this->getTable();
				
			$return = $table->loadByAlias($this->alias);
		
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
				
			return $table;
		}else{//Get item by Id
			if ($item = parent::getItem($pk)) {
				//Do any procesing on fields here if needed
			}
		}
		
		return $item;
	}
*/

	/**
	 * Method to get a single record.
	 *
	 * @param	$alias 			string		Alias of the service.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	EasySDI 3.0.0
	 */
	public function getItemByServiceAlias($alias)
	{
		$this->alias = $alias;
		$item = $this->getItem(null);
	
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

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_virtualservice');
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
	public function save($data) {
		if(parent::save($data)){
			//Instantiate an address JTable
			$virtualmetadata =& JTable::getInstance('virtualmetadata', 'Easysdi_serviceTable');
			//Call the overloaded save function to store the input data
			$data['id'] = $this->getItem()->get('id');
			if( !$virtualmetadata->save($data) ){	
				return false;
			}
			if(isset($data['physicalservice_id']))
			{
				 if(!$this->savePhysicalServiceAggregation($data, $this->getState($this->getName().'.id')))
				 	return false;
			}
			if(isset($data['compliance']))
			{
				 if(!$this->saveServiceCompliance($data['compliance'],$data['serviceconnector_id'], $this->getState($this->getName().'.id')))
				 	return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 */
	protected function savePhysicalServiceAggregation ($data, $id)
	{
		$db = $this->getDbo();
		$db->setQuery(
				'DELETE FROM #__sdi_virtual_physical WHERE virtualservice_id = '.$id
		);
		$db->query();
		
		$arr_pks = $data['physicalservice_id'];
		foreach ($arr_pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_virtual_physical (virtualservice_id, physicalservice_id) ' .
						' VALUES ('.$id.','.$pk.')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 
	 */
	public function getPhysicalServiceAggregation ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$db->setQuery(
					'SELECT physicalservice_id FROM #__sdi_virtual_physical  ' .
					' WHERE virtualservice_id ='.$id
			);
			return  $db->loadColumn();
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
	
	/**
	 * Method to save the service compliance deducted from the agregation process
	 *
	 * @param array 	$pks	array of the #__sdi_sys_servicecompliance ids to link with the current service
	 * @param int		$id		primary key of the current service to save.
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveServiceCompliance ($pks, $connector, $id)
	{
		//Delete previously saved compliance
		$db = $this->getDbo();
		$db->setQuery(
				'DELETE FROM #__sdi_virtualservice_servicecompliance WHERE service_id = '.$id
		);
		$db->query();
	
		$arr_pks = json_decode ($pks);
		foreach ($arr_pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_virtualservice_servicecompliance (service_id, servicecompliance_id) ' .
						' VALUES ('.$id.',(SELECT c.id FROM #__sdi_sys_servicecompliance c INNER JOIN #__sdi_sys_serviceversion v ON c.serviceversion_id = v.id WHERE c.serviceconnector_id = '.$connector.' AND v.value="'.$pk.'"))'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to get the service compliance deducted from the agregation process and saved into database
	 *
	 * @param int		$id		primary key of the current service to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getServiceCompliance ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$db->setQuery(
					'SELECT sv.value as value, sc.id as id FROM #__sdi_virtualservice_servicecompliance ssc ' .
					' INNER JOIN #__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id '.
					' INNER JOIN #__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id'.
					' WHERE ssc.service_id ='.$id
			);
			$compliance = $db->loadObjectList();
			return $compliance;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}

}