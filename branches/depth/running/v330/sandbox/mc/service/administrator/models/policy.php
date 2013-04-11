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
class Easysdi_serviceModelpolicy extends JModelAdmin
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
	public function getTable($type = 'Policy', $prefix = 'Easysdi_serviceTable', $config = array())
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
		$form = $this->loadForm('com_easysdi_service.policy', 'policy', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_easysdi_service.edit.policy.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}
	
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'virtualservice_id = '.(int) $table->virtualservice_id;
		return $condition;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null) {
		if ($item = parent::getItem($pk)) {
			//Do any procesing on fields here if needed
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'tables');
			
			$pk = $item->id;
			
			if (!isset($item->virtualservice_id)) {
				$item->virtualservice_id = (int) JRequest::getVar('virtualservice_id',null);
			}
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select('sc.value, vs.name')
				->from(' #__sdi_virtualservice AS vs ')
				->join('LEFT', '#__sdi_sys_serviceconnector AS sc ON sc.id = vs.serviceconnector_id')
				->where('vs.id = ' . $item->virtualservice_id );
			$db->setQuery($query);
			$result = $db->loadObject();

			$item->layout = ($result->value == "WMSC")? "WMS" : $result->value;
			$item->virtualservice = $result->name ;
			
			if (method_exists('Easysdi_serviceModelpolicy', '_getItem' . $item->layout)) {
				$item->physicalService = $this->{'_getItem' . $item->layout}($pk, $item->virtualservice_id);
			}
			
			$item->{'allowedoperation_' . strtolower($item->layout)} = $this->loadAllowedOperation($pk);
		}
		
		// Get the access scope
		$item->organisms 		= $this->getAccessScopeOrganism($item->id);
		$item->users 			= $this->getAccessScopeUser($item->id);
		
		return $item;
	}
	
	/**
	 *
	 * Get item with WMS service connector
	 *
	*/
	protected function _getItemWMS($pk, $virtualservice_id) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmsPhysicalService.php');
		$tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
		$db = JFactory::getDbo();
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wmsObjList = Array();
		foreach ($ps_list as $ps) {
			$layerList = Array();
			
			//check layers that have settings
			if (!empty($pk)) {
				$db->setQuery('
					SELECT wlp.name
					FROM #__sdi_policy p
					JOIN #__sdi_physicalservice_policy psp
					ON p.id = psp.policy_id
					JOIN #__sdi_wmslayer_policy wlp
					ON psp.id = wlp.physicalservicepolicy_id
					WHERE p.id = ' . $pk . '
					AND psp.physicalservice_id = ' . $ps->id . '
					AND wlp.spatialpolicy_id IS NOT NULL;
				');
				
				try {
					$db->execute();
					$resultset = $db->loadObjectList();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				foreach ($resultset as $row) {
					$layerList[] = $row->name;
				}
			}
			
			$wmsObj = new WmsPhysicalService($ps->id, $ps->resourceurl);
			$wmsObj->getCapabilities();
			$wmsObj->populate();
			$wmsObj->sortLists();
			$wmsObj->setLayerAsConfigured($layerList);
			$wmsObjList[] = $wmsObj;
		}
		
		return $wmsObjList;
	}
	
	/**
	 *
	 * Get item with WFS service connector
	 *
	*/
	protected function _getItemWFS($pk, $virtualservice_id) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WfsPhysicalService.php');
		$tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
		$db = JFactory::getDbo();
		
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wfsObjList = Array();
		foreach ($ps_list as $ps) {
			$layerList = Array();
			//check layers that have settings
			if (!empty($pk)) {
				$db->setQuery('
					SELECT wlp.name
					FROM #__sdi_policy p
					JOIN #__sdi_physicalservice_policy psp
					ON p.id = psp.policy_id
					JOIN #__sdi_featuretype_policy wlp
					ON psp.id = wlp.physicalservicepolicy_id
					WHERE p.id = ' . $pk . '
					AND psp.physicalservice_id = ' . $ps->id . ';
				');
				
				try {
					$db->execute();
					$resultset = $db->loadObjectList();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				foreach ($resultset as $row) {
					$layerList[] = $row->name;
				}
			}
			
			$wfsObj = new WfsPhysicalService($ps->id, $ps->resourceurl);
			$wfsObj->getCapabilities();
			$wfsObj->populate();
			$wfsObj->sortLists();
			$wfsObj->setLayerAsConfigured($layerList);
			$wfsObjList[] = $wfsObj;
		}
		
		return $wfsObjList;
	}
	
	/**
	 *
	 * Get item with WMTS service connector
	 *
	*/
	protected function _getItemWMTS($pk, $virtualservice_id) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmtsPhysicalService.php');
		$tab_physicalService = JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
		$db = JFactory::getDbo();
		
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wmtsObjList = Array();
		foreach ($ps_list as $ps) {
			$layerList = Array();
			//check layers that have settings
			if (!empty($pk)) {
				$db->setQuery('
					SELECT wlp.identifier
					FROM #__sdi_policy p
					JOIN #__sdi_physicalservice_policy psp
					ON p.id = psp.policy_id
					JOIN #__sdi_wmtslayer_policy wlp
					ON psp.id = wlp.physicalservicepolicy_id
					WHERE p.id = ' . $pk . '
					AND psp.physicalservice_id = ' . $ps->id . ';
				');
				
				try {
					$db->execute();
					$resultset = $db->loadObjectList();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				foreach ($resultset as $row) {
					$layerList[] = $row->identifier;
				}
			}
			
			$wmtsObj = new WmtsPhysicalService($ps->id, $ps->resourceurl);
			$wmtsObj->getCapabilities();
			$wmtsObj->populate();
			$wmtsObj->sortLists();
			$wmtsObj->setLayerAsConfigured($layerList);
			$wmtsObjList[] = $wmtsObj;
		}
		
		return $wmtsObjList;
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
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_policy ');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}
	
	
	public function save($data) {
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT sc.value
			FROM #__sdi_virtualservice vs
			JOIN #__sdi_sys_serviceconnector sc
			ON sc.id = vs.serviceconnector_id
			WHERE vs.id = ' . $data['virtualservice_id'] . ';
		');
		
		try {
			$db->execute();
			$serviceconnector_name = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		$isNew = (0 == $data['id']) ? true : false;
		
		if(parent::save($data)){
			
			$data['id'] = $this->getItem()->get('id');
			if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name || 'WMTS' == $serviceconnector_name) {
				$physicalservicepolicy = JTable::getInstance('physicalservice_policy', 'Easysdi_serviceTable');
				if(!$physicalservicepolicy->save($data)){
					$this->setError('Failed to save physicalservice_policy.');
					return false;
				}
			}
			
			if ('WMS' == $serviceconnector_name) {
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmsWebservice.php');
				
				if (!WmsWebservice::saveAllLayers( $data['virtualservice_id'], $data['id'])) {
					$this->setError('Failed to save all WMS layers.');
					return false;
				}
			}
			
			if (!$isNew) {
				switch ($serviceconnector_name) {
					case 'WMTS':
						if (!$this->saveWMTSInheritance($data)) {
							$this->setError('Failed to save inheritance.');
							return false;
						}
						break;
					case 'WMS':
						if (!$this->saveWMSInheritance($data)) {
							$this->setError('Failed to save inheritance.');
							return false;
						}
						break;
					case 'WFS':
						if (!$this->saveWFSInheritance($data)) {
							$this->setError('Failed to save inheritance.');
							return false;
						}
						break;
				}
			}
			
			if (!$this->saveAllowedOperation($data)) {
				$this->setError('Failed to save allowed operations.');
				return false;
			}
			
			if ('CSW' == $serviceconnector_name) {
				if (!$this->saveExcludedAttributes($data)) {
					$this->setError('Failed to save excluded attributes.');
					return false;
				}
			}
			
			//Access Scope
			if (!$this->saveAccessScope($data)) {
				$this->setError('Failed to save access scope.');
				return false;
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Method to save the inherited settings on layers
	 *
	 * @param array 	$data	data posted from the form
	 * 
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.3.0
	 */
	public function saveWMTSInheritance ($data) {
		$db = JFactory::getDbo();
		
		//Save the policy-wide inheritance
		$spatialPolicy = current($_POST['inherit_policy']);
		$spatialPolicyID = key($_POST['inherit_policy']);
		
		$policyUpdates = Array(
			'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
		);
		
		//test whether that policy already have a spatialPolicy or not
		if (-1 == $spatialPolicyID) {
			//we create the spatial policy
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wmts_spatialpolicy')->columns(
				'northboundlatitude, westboundlongitude, eastboundlongitude, southboundlatitude, spatialoperator_id'
			)->values('\'' . $spatialPolicy['northBoundLatitude'] . '\', \'' . $spatialPolicy['westBoundLongitude'] . '\', \'' . $spatialPolicy['eastBoundLongitude'] . '\', \'' . $spatialPolicy['southBoundLatitude'] . '\', \'' . $spatialPolicy['spatialoperatorid'] . '\'');
			
			try {
				$db->setQuery($query);
				$db->execute();
				$spatialPolicyID = $db->insertid();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//we update the spatial policy foreign key in policy
			$policyUpdates[] = 'wmts_spatialpolicy_id = ' . $spatialPolicyID;
			
		}
		else {
			//we update the spatial policy
			$query = $db->getQuery(true);
			$query->update('#__sdi_wmts_spatialpolicy')->set(Array(
				'northboundlatitude = \'' . $spatialPolicy['northBoundLatitude'] . '\'',
				'westboundlongitude = \'' . $spatialPolicy['westBoundLongitude'] . '\'',
				'eastboundlongitude = \'' . $spatialPolicy['eastBoundLongitude'] . '\'',
				'southboundlatitude = \'' . $spatialPolicy['southBoundLatitude'] . '\'',
				'spatialoperator_id = \'' . $spatialPolicy['spatialoperatorid'] . '\'',
			))->where('id = ' . $spatialPolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
			
		//we update the anyservice switch
		$query = $db->getQuery(true);
		$query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . $data['id']);
		
		try {
			$db->setQuery($query);
			$db->execute();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//Save the server-wide inheritance
		$spatialPolicy = null;
		foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
			$physicalServicePolicyUpdates = Array(
				'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
			);
			//check if a spatial policy exists for that physicalservice_policy
			$query = '
				SELECT wmts_spatialpolicy_id, id
				FROM #__sdi_physicalservice_policy
				WHERE physicalservice_id = ' . $physicalServiceID . '
				AND policy_id = ' . $data['id'] . ';
			';
			
			try {
				$db->setQuery($query);
				$db->execute();
				$resultSet = $db->loadObject();
				$spatialPolicyID = null;
				if (!empty($resultSet)) {
					$spatialPolicyID = $resultSet->wmts_spatialpolicy_id;
					$physicalServicePolicyID = $resultSet->id;
				}
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//test whether that physicalservice_policy already have a spatialPolicy or not
			if (empty($spatialPolicyID)) {
				//create a spatial policy
				$query = $db->getQuery(true);
				$query->insert('#__sdi_wmts_spatialpolicy')->columns(
					'northboundlatitude, westboundlongitude, eastboundlongitude, southboundlatitude, spatialoperator_id'
				)->values('\'' . $spatialPolicy['northBoundLatitude'] . '\', \'' . $spatialPolicy['westBoundLongitude'] . '\', \'' . $spatialPolicy['eastBoundLongitude'] . '\', \'' . $spatialPolicy['southBoundLatitude'] . '\', \'' . $spatialPolicy['spatialoperatorid'] . '\'');
				
				try {
					$db->setQuery($query);
					$db->execute();
					$spatialPolicyID = $db->insertid();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				//update the spatial foreign key in physicalservice_policy
				$physicalServicePolicyUpdates[] = 'wmts_spatialpolicy_id = ' . $spatialPolicyID;
				
			}
			else {
				//update the spatial policy
				$query = $db->getQuery(true);
				$query->update('#__sdi_wmts_spatialpolicy')->set(Array(
					'northboundlatitude = \'' . $spatialPolicy['northBoundLatitude'] . '\'',
					'westboundlongitude = \'' . $spatialPolicy['westBoundLongitude'] . '\'',
					'eastboundlongitude = \'' . $spatialPolicy['eastBoundLongitude'] . '\'',
					'southboundlatitude = \'' . $spatialPolicy['southBoundLatitude'] . '\'',
					'spatialoperator_id = \'' . $spatialPolicy['spatialoperatorid'] . '\'',
				))->where('id = ' . $spatialPolicyID);
				
				try {
					$db->setQuery($query);
					$db->execute();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
			}
			
			//update the anyitem switch
			$query = $db->getQuery(true);
			$query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . $physicalServicePolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Method to save the inherited settings on layers
	 *
	 * @param array 	$data	data posted from the form
	 * 
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.3.0
	 */
	public function saveWMSInheritance ($data) {
		$db = JFactory::getDbo();
		
		//Save the policy-wide inheritance
		$spatialPolicy = current($_POST['inherit_policy']);
		$spatialPolicyID = key($_POST['inherit_policy']);
		
		$policyUpdates = Array(
			'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
		);
		
		//test whether that policy already have a spatialPolicy or not
		if (-1 == $spatialPolicyID) {
			//we create the spatial policy
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wms_spatialpolicy')->columns(
				'maxx, maxy, minx, miny, geographicfilter, maximumscale, minimumscale, srssource'
			)->values('\'' . $spatialPolicy['maxx'] . '\', \'' . $spatialPolicy['maxy'] . '\', \'' . $spatialPolicy['minx'] . '\', \'' . $spatialPolicy['miny'] . '\', \'' . $spatialPolicy['geographicfilter'] . '\', \'' . $spatialPolicy['maximumscale'] . '\', \'' . $spatialPolicy['minimumscale'] . '\', \'' . $spatialPolicy['srssource'] . '\'');
			
			try {
				$db->setQuery($query);
				$db->execute();
				$spatialPolicyID = $db->insertid();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//we update the spatial policy foreign key in policy
			$policyUpdates[] = 'wms_spatialpolicy_id = ' . $spatialPolicyID;
			
		}
		else {
			//we update the spatial policy
			$query = $db->getQuery(true);
			$query->update('#__sdi_wms_spatialpolicy')->set(Array(
				'maxx = \'' . $spatialPolicy['maxx'] . '\'',
				'maxy = \'' . $spatialPolicy['maxy'] . '\'',
				'minx = \'' . $spatialPolicy['minx'] . '\'',
				'miny = \'' . $spatialPolicy['miny'] . '\'',
				'geographicfilter = \'' . $spatialPolicy['geographicfilter'] . '\'',
				'maximumscale = \'' . $spatialPolicy['maximumscale'] . '\'',
				'minimumscale = \'' . $spatialPolicy['minimumscale'] . '\'',
				'srssource = \'' . $spatialPolicy['srssource'] . '\'',
			))->where('id = ' . $spatialPolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
			
		//we update the anyservice switch
		$query = $db->getQuery(true);
		$query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . $data['id']);
		
		try {
			$db->setQuery($query);
			$db->execute();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//Save the server-wide inheritance
		$spatialPolicy = null;
		foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
			$physicalServicePolicyUpdates = Array(
				'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
			);
			//check if a spatial policy exists for that physicalservice_policy
			$query = '
				SELECT wms_spatialpolicy_id, id
				FROM #__sdi_physicalservice_policy
				WHERE physicalservice_id = ' . $physicalServiceID . '
				AND policy_id = ' . $data['id'] . ';
			';
			
			try {
				$db->setQuery($query);
				$db->execute();
				$resultSet = $db->loadObject();
				$spatialPolicyID = null;
				if (!empty($resultSet)) {
					$spatialPolicyID = $resultSet->wmts_spatialpolicy_id;
					$physicalServicePolicyID = $resultSet->id;
				}
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//test whether that physicalservice_policy already have a spatialPolicy or not
			if (empty($spatialPolicyID)) {
				//create a spatial policy
				$query = $db->getQuery(true);
				$query->insert('#__sdi_wms_spatialpolicy')->columns(
					'maxx, maxy, minx, miny, geographicfilter, maximumscale, minimumscale, srssource'
				)->values('\'' . $spatialPolicy['maxx'] . '\', \'' . $spatialPolicy['maxy'] . '\', \'' . $spatialPolicy['minx'] . '\', \'' . $spatialPolicy['miny'] . '\', \'' . $spatialPolicy['geographicfilter'] . '\', \'' . $spatialPolicy['maximumscale'] . '\', \'' . $spatialPolicy['minimumscale'] . '\', \'' . $spatialPolicy['srssource'] . '\'');
				
				try {
					$db->setQuery($query);
					$db->execute();
					$spatialPolicyID = $db->insertid();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				//update the spatial foreign key in physicalservice_policy
				$physicalServicePolicyUpdates[] = 'wms_spatialpolicy_id = ' . $spatialPolicyID;
				
			}
			else {
				//update the spatial policy
				$query = $db->getQuery(true);
				$query->update('#__sdi_wms_spatialpolicy')->set(Array(
					'maxx = \'' . $spatialPolicy['maxx'] . '\'',
					'maxy = \'' . $spatialPolicy['maxy'] . '\'',
					'minx = \'' . $spatialPolicy['minx'] . '\'',
					'miny = \'' . $spatialPolicy['miny'] . '\'',
					'geographicfilter = \'' . $spatialPolicy['geographicfilter'] . '\'',
					'maximumscale = \'' . $spatialPolicy['maximumscale'] . '\'',
					'minimumscale = \'' . $spatialPolicy['minimumscale'] . '\'',
					'srssource = \'' . $spatialPolicy['srssource'] . '\'',
				))->where('id = ' . $spatialPolicyID);
				
				try {
					$db->setQuery($query);
					$db->execute();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
			}
			
			//update the anyitem switch
			$query = $db->getQuery(true);
			$query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . $physicalServicePolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Method to save the inherited settings on layers
	 *
	 * @param array 	$data	data posted from the form
	 * 
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.3.0
	 */
	public function saveWFSInheritance ($data) {
		$db = JFactory::getDbo();
		
		//Save the policy-wide inheritance
		$spatialPolicy = current($_POST['inherit_policy']);
		$spatialPolicyID = key($_POST['inherit_policy']);
		
		$policyUpdates = Array(
			'anyservice = ' . ((isset($spatialPolicy['anyservice'])) ? 1 : 0),
		);
		
		//test whether that policy already have a spatialPolicy or not
		if (-1 == $spatialPolicyID) {
			//we create the spatial policy
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wfs_spatialpolicy')->columns(
				'localgeographicfilter, remotegeographicfilter'
			)->values('\'' . $spatialPolicy['localgeographicfilter'] . '\', \'' . $spatialPolicy['remotegeographicfilter'] . '\'');
			
			try {
				$db->setQuery($query);
				$db->execute();
				$spatialPolicyID = $db->insertid();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//we update the spatial policy foreign key in policy
			$policyUpdates[] = 'wfs_spatialpolicy_id = ' . $spatialPolicyID;
			
		}
		else {
			//we update the spatial policy
			$query = $db->getQuery(true);
			$query->update('#__sdi_wfs_spatialpolicy')->set(Array(
				'localgeographicfilter = \'' . $spatialPolicy['localgeographicfilter'] . '\'',
				'remotegeographicfilter = \'' . $spatialPolicy['remotegeographicfilter'] . '\'',
			))->where('id = ' . $spatialPolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
			
		//we update the anyservice switch
		$query = $db->getQuery(true);
		$query->update('#__sdi_policy')->set($policyUpdates)->where('id = ' . $data['id']);
		
		try {
			$db->setQuery($query);
			$db->execute();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//Save the server-wide inheritance
		$spatialPolicy = null;
		foreach ($_POST['inherit_server'] as $physicalServiceID => $spatialPolicy) {
			$physicalServicePolicyUpdates = Array(
				'anyitem = ' . ((isset($spatialPolicy['anyitem'])) ? 1 : 0),
			);
			//check if a spatial policy exists for that physicalservice_policy
			$query = '
				SELECT wfs_spatialpolicy_id, id
				FROM #__sdi_physicalservice_policy
				WHERE physicalservice_id = ' . $physicalServiceID . '
				AND policy_id = ' . $data['id'] . ';
			';
			
			try {
				$db->setQuery($query);
				$db->execute();
				$resultSet = $db->loadObject();
				$spatialPolicyID = null;
				if (!empty($resultSet)) {
					$spatialPolicyID = $resultSet->wmts_spatialpolicy_id;
					$physicalServicePolicyID = $resultSet->id;
				}
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			//test whether that physicalservice_policy already have a spatialPolicy or not
			if (empty($spatialPolicyID)) {
				//create a spatial policy
				$query = $db->getQuery(true);
				$query->insert('#__sdi_wfs_spatialpolicy')->columns(
					'localgeographicfilter, remotegeographicfilter'
				)->values('\'' . $spatialPolicy['localgeographicfilter'] . '\', \'' . $spatialPolicy['remotegeographicfilter'] . '\'');
				
				try {
					$db->setQuery($query);
					$db->execute();
					$spatialPolicyID = $db->insertid();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				//update the spatial foreign key in physicalservice_policy
				$physicalServicePolicyUpdates[] = 'wfs_spatialpolicy_id = ' . $spatialPolicyID;
				
			}
			else {
				//update the spatial policy
				$query = $db->getQuery(true);
				$query->update('#__sdi_wms_spatialpolicy')->set(Array(
				'localgeographicfilter = \'' . $spatialPolicy['localgeographicfilter'] . '\'',
				'remotegeographicfilter = \'' . $spatialPolicy['remotegeographicfilter'] . '\'',
				))->where('id = ' . $spatialPolicyID);
				
				try {
					$db->setQuery($query);
					$db->execute();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
			}
			
			//update the anyitem switch
			$query = $db->getQuery(true);
			$query->update('#__sdi_physicalservice_policy')->set($physicalServicePolicyUpdates)->where('id = ' . $physicalServicePolicyID);
			
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Method to save the organisms and users allowed by the access scope
	 *
	 * @param array 	$data	data posted from the form
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.3.0
	 */
	public function saveAccessScope ($data)
	{
		//Delete previously saved access
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM #__sdi_policy_organism WHERE policy_id = '.$data['id']);
		$db->query();
		$db->setQuery('DELETE FROM #__sdi_policy_user WHERE policy_id = '.$data['id']);
		$db->query();
	
		$pks = $data['organisms'];
		foreach ($pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_policy_organism (policy_id, organism_id) ' .
						' VALUES ('.$data['id'].','.$pk.')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		$pks = $data['users'];
		foreach ($pks as $pk)
		{
			try {
				$db->setQuery(
						'INSERT INTO #__sdi_policy_user (policy_id, user_id) ' .
						' VALUES ('.$data['id'].','.$pk.')'
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
	 * Method to get the organisms authorized to access this policy
	 *
	 * @param int		$id		primary key of the current policy to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getAccessScopeOrganism  ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.organism_id as id');
			$query->from('#__sdi_policy_organism p');
			$query->where('p.policy_id = ' . (int) $id);
			$db->setQuery($query);
	
			$scope = $db->loadColumn();
			return $scope;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
	
	/**
	 * Method to get the users authorized to access this policy
	 *
	 * @param int		$id		primary key of the current policy to get.
	 *
	 * @return boolean 	Object list on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function getAccessScopeUser  ( $id=null)
	{
		if(!isset($id))
			return null;
	
		try {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('p.user_id as id');
			$query->from('#__sdi_policy_user p');
			$query->where('p.policy_id = ' . (int) $id);
			$db->setQuery($query);
	
			$scope = $db->loadColumn();
			return $scope;
	
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	
	}
	
	/**
	 * Method to save allowed operations on that policy
	 *
	 * @param array 	$data	data posted from the form
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveAllowedOperation ($data) {
		$db = $this->getDbo();
		$db->setQuery('DELETE FROM #__sdi_allowedoperation WHERE policy_id = ' . $data['id']);
		$db->query();
		
		$arr_pks = $data['allowedoperation_' . strtolower($data['layout'])];
		foreach ($arr_pks as $pk) {
			try {
				$db->setQuery('
					INSERT INTO #__sdi_allowedoperation (policy_id, serviceoperation_id)
					VALUES (' . $data['id'] . ',' . $pk . ');
				');
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Load allowed operation
	 *
	 * @param int $pk the primary key of the current policy
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function loadAllowedOperation ($pk) {
		if (empty($pk)) {
			return Array();
		}
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT serviceoperation_id FROM #__sdi_allowedoperation
			WHERE policy_id =' . $pk . '
		');
		
		try {
			$db->execute();
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		return $db->loadColumn();
	}
	
	/**
	 * Method to save excluded attributes on a csw policy
	 *
	 * @param array 	$data	data posted from the form
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveExcludedAttributes ($data) {
		$db = $this->getDbo();
		$db->setQuery('DELETE FROM #__sdi_excludedattribute WHERE policy_id = ' . $data['id']);
		$db->query();
		
		$arr_ex = $_POST['excluded_attribute'];
		foreach ($arr_ex as $value) {
			try {
				$db->setQuery('
					INSERT INTO #__sdi_excludedattribute (policy_id, path)
					VALUES (' . $data['id'] . ',\'' . $value . '\');
				');
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
}