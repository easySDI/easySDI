<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_service model.
 */
class Easysdi_serviceModelservice extends JModelAdmin
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
	public function getTable($type = 'Service', $prefix = 'Easysdi_serviceTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_service.service', 'service', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.service.data', array());

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
			$compliances = $this->getServiceCompliance($item->id);
			$compliance_ids =array();
			$compliance_values =array();
			foreach ($compliances as $compliance)
			{
				$compliance_ids[] =$compliance->id;
				$compliance_values[] =$compliance->value;
			}
			
			$item->compliance = json_encode($compliance_ids);
			$item->supportedversions = json_encode($compliance_values);

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

		if (empty($table->id)) {
			$table->serviceauthentication_id = null;
			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_service');
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
// 		if($data['serviceauthentication_id'] == 0){
// 			$data['serviceauthentication_id'] = null;
// 		}
		if(parent::save($data))
		{
			if(isset($data['compliance']))
			{
				return $this->saveServiceCompliance($data['compliance'], $this->getState($this->getName().'.id'));
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Method to save the service compliance deducted from the negotiation process
	 *
	 * @param array 	$pks	array of the #__sdi_sys_servicecompliance ids to link with the current service
	 * @param int		$id		primary key of the current service to save.
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveServiceCompliance ($pks, $id)
	{
		$arr_pks = json_decode ($pks);
	
		foreach ($arr_pks as $pk)
		{
			try {
				$db = $this->getDbo();
				$db->setQuery(
						'SELECT * FROM #__sdi_service_servicecompliance WHERE service_id = '.$id.' AND servicecompliance_id= '.$pk
				);
				$row = $db->loadObjectList();
				//If compliance does not already exist, insert it
				if(count($row) < 1){
					$db->setQuery(
							'INSERT INTO #__sdi_service_servicecompliance (service_id, servicecompliance_id) ' .
							' VALUES ('.$id.','.$pk.')'
								
					);
					if (!$db->query()) {
						throw new Exception($db->getErrorMsg());
					}
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Method to get the service compliance deducted from the negotiation process and saved into database
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
			$db = $this->getDbo();
			$db->setQuery(
					'SELECT sv.value as value, sc.id as id FROM #__sdi_service_servicecompliance ssc ' .
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