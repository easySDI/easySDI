<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Easysdi_service model.
 */
class Easysdi_serviceModelphysicalservice extends JModelAdmin
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
	public function getTable($type = 'PhysicalService', $prefix = 'Easysdi_serviceTable', $config = array())
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
				return $user->authorise('core.delete', 'com_easysdi_service.category.'.(int) $record->catid);
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
			return $user->authorise('core.edit.state', 'com_easysdi_service.category.'.(int) $record->catid);
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
		$form = $this->loadForm('com_easysdi_service.physicalservice', 'physicalservice', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.physicalservice.data', array());
		
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
			
			//Get the service supported versions 
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
				$item->compliance = json_encode($compliance_ids);
			else 
				$item->compliance = '';			
			$item->supportedversions = json_encode($compliance_values);

			//Get the authentication connectors (depend on connector type)
			if(!$item->serviceconnector_id)
			{
				$item->serviceconnector_id = JRequest::getVar( 'serviceconnector_id' );
			}
			
			$db = JFactory::getDbo();
                        
            $query = $db->getQuery(true);
            $query->select('0 AS id, \'- None -\' AS value');
            $db->setQuery($query);
			$item->currentresourceauthenticationconnectorlist = $db->loadObjectList();
			
            $unionquery = $db->getQuery(true);
            $unionquery->select('ac.id, ac.value');
            $unionquery->from('#__sdi_sys_authenticationconnector ac');
            $unionquery->innerJoin('#__sdi_sys_servicecon_authenticationcon sc ON sc.authenticationconnector_id = ac.id');
            $unionquery->innerJoin('#__sdi_sys_serviceconnector c ON c.id = sc.serviceconnector_id');
            $unionquery->innerJoin('#__sdi_sys_authenticationlevel al ON ac.authenticationlevel_id = al.id');
            $unionquery->where('c.state = 1');
            $unionquery->where('al.id = 1');
            $unionquery->where('c.id = '. (int)$item->serviceconnector_id);
                        
			//Method union is not working on SQL Server			
            //$query->union($unionquery);
            $db->setQuery($unionquery);			
			$item->currentresourceauthenticationconnectorlist = array_merge($item->currentresourceauthenticationconnectorlist, $db->loadObjectList());
                        
			if ($error = $db->getErrorMsg()) {
				$this->setError($error);
				return false;
			}
				
            $query = $db->getQuery(true);
            $query->select('0 AS id, \'- None -\' AS value');
            $db->setQuery($query);
			$item->currentserviceauthenticationconnectorlist = $db->loadObjectList();
                        
            $unionquery = $db->getQuery(true);
            $unionquery->select('ac.id, ac.value');
            $unionquery->from('#__sdi_sys_authenticationconnector ac');
            $unionquery->innerJoin('#__sdi_sys_servicecon_authenticationcon sc ON sc.authenticationconnector_id = ac.id');
            $unionquery->innerJoin('#__sdi_sys_serviceconnector c ON c.id = sc.serviceconnector_id');
            $unionquery->innerJoin('#__sdi_sys_authenticationlevel al ON ac.authenticationlevel_id = al.id');
            $unionquery->where('c.state = 1');
            $unionquery->where('al.id = 2');
            $unionquery->where('c.id = '. (int)$item->serviceconnector_id);
                        
            //Method union is not working on SQL Server
			//$query->union($unionquery);
            $db->setQuery($unionquery);
            $item->currentserviceauthenticationconnectorlist = array_merge($item->currentserviceauthenticationconnectorlist, $db->loadObjectList());
			
			if ($error = $db->getErrorMsg()) {
				$this->setError($error);
				return false;
			}
			
                        $query = $db->getQuery(true);
                        $query->select('value');
                        $query->from('#__sdi_sys_serviceconnector');
                        $query->where('id='. (int)$item->serviceconnector_id);
                       
			$db->setQuery($query);
			$item->serviceconnector = $db->loadResult();
			if ($error = $db->getErrorMsg()) {
				$this->setError($error);
				return false;
			}
			
			// Get the service scope
			$item->organisms = $this->getServiceScopeOrganism($item->id);
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

		//Service id is set to default value '0' in case of creation.
		//So this section of code is never executed.
		//Ordering is set in sdiTable->check() function.
		//However, We keep this section in case of default id was not set to '0' anymore (changes in form xml)
		if (empty($table->id)) {
			$table->serviceauthentication_id = null;
			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
                                $query = $db->getQuery(true);
                                $query->select('MAX(ordering)');
                                $query->from('#__sdi_physicalservice');
                                
				$db->setQuery($query);
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}
		}
		
		if (empty($table->alias)){
			$table->alias = $table->name;
		}
		
		if($table->resourceauthentication_id == 0)
			$table->resourceauthentication_id = null;
		if($table->serviceauthentication_id == 0)
			$table->serviceauthentication_id = null;
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
			if(! $this->saveServiceCompliance($data['compliance'], $this->getState($this->getName().'.id')))
				return false;
			if(! $this->saveServiceScopeOrganism($data['organisms'], $this->getState($this->getName().'.id')))
				return false;
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
		//Delete previously saved compliance
		$db = $this->getDbo();
                $query =$db->getQuery(true);
                $query->delete('#__sdi_physicalservice_servicecompliance');
                $query->where('service_id = '. (int)$id);
                
		$db->setQuery($query);
		$db->query();
		
		$arr_pks = json_decode ($pks);
                if(!is_array($arr_pks)){
                    return true;
                }
		foreach ($arr_pks as $pk)
		{
			try {
                                $query = $db->getQuery(true);
                                $columns = array('service_id', 'servicecompliance_id');
                                $values = array($id, $pk);
                                $query->insert('#__sdi_physicalservice_servicecompliance');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));
                            
				$db->setQuery($query);
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
	 * Method to get the service compliance 
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
			$query = $db->getQuery(true);
			$query->select('sv.value as value, sc.id as id');
			$query->from('#__sdi_physicalservice_servicecompliance ssc');
			$query->join('INNER', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id ');
			$query->join('INNER', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id ');
			$query->where('ssc.service_id = ' . (int) $id);
			$db->setQuery($query);

			$compliance = $db->loadObjectList();
			return $compliance;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
	
	/**
	 * Method to save the organisms allowed by the service scope
	 *
	 * @param array 	$pks	array of the #__sdi_organism ids to link with the current service
	 * @param int		$id		primary key of the current service to save.
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveServiceScopeOrganism ($pks, $id)
	{
		//Delete previously saved compliance
		$db = $this->getDbo();
                $query = $db->getQuery(true);
                $query->delete('#__sdi_physicalservice_organism');
                $query->where('physicalservice_id = '. (int)$id);
                
		$db->setQuery($query);
		$db->query();
	
                if(!is_array($pks)){
                    return true;
                }
		foreach ($pks as $pk)
		{
			try {
                                $query = $db->getQuery(true);
                                $columns = array('physicalservice_id', 'organism_id');
                                $values = array($id, $pk);
                                $query->insert('#__sdi_physicalservice_organism');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));
                            
				$db->setQuery($query);
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
	 * Method to get the organisms authorized to access this service
	 *
	 * @param int		$id		primary key of the current service to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getServiceScopeOrganism  ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('so.organism_id as id');
			$query->from('#__sdi_physicalservice_organism so');
			$query->where('so.physicalservice_id = ' . (int) $id);
			$db->setQuery($query);
	
			$scope = $db->loadColumn();
			return $scope;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
}