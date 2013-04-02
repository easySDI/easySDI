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
// 			$layout = JRequest::getVar('layout',null);
			
			if (!isset($item->virtualservice_id)) {
				$item->virtualservice_id = (int) JRequest::getVar('virtualservice_id',null);
			}
			
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query
			->select('sc.value')
			->from(' #__sdi_virtualservice AS vs ')
			->join('LEFT', '#__sdi_sys_serviceconnector AS sc ON sc.id = vs.serviceconnector_id')
			->where('vs.id = ' . $item->virtualservice_id );
			$db->setQuery($query);
			$layout = $db->loadResult();
			
			if (method_exists('Easysdi_serviceModelpolicy', '_getItem' . $layout)) {
				$item->physicalService = $this->{'_getItem' . $layout}($pk, $item->virtualservice_id);
			}
		}
		
		//SetLayout : layout is the connector type
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
				->select('sc.value, vs.name')
				->from(' #__sdi_virtualservice AS vs ')
				->join('LEFT', '#__sdi_sys_serviceconnector AS sc ON sc.id = vs.serviceconnector_id')
				->where('vs.id = ' . $item->virtualservice_id );
		$db->setQuery($query);
		$result = $db->loadObject();

		$item->layout 			= ($result->value == "WMSC")? "WMS" : $result->value;
		$item->virtualservice 	= $result->name ;
		
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
				
				$layerList = Array();
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
			//check layers that have settings
			if (!empty($pk)) {
				$db->setQuery('
					SELECT wlp.name
					FROM #__sdi_policy p
					JOIN #__sdi_physicalservice_policy psp
					ON p.id = psp.policy_id
					JOIN #__sdi_wfslayer_policy wlp
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
				
				$layerList = Array();
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
				
				$layerList = Array();
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
			$serviceconnector_name = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		if(parent::save($data)){
			
			$data['id'] = $this->getItem()->get('id');
			if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name || 'WMTS' == $serviceconnector_name) {
				$physicalservicepolicy = JTable::getInstance('physicalservice_policy', 'Easysdi_serviceTable');
				if(!$physicalservicepolicy->save($data)){
					return false;
				}
			}
			
			if ('WMS' == $serviceconnector_name) {
				require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmsWebservice.php');
				
				$params = Array(
					'virtualServiceID' => $data['virtualservice_id'],
					'policyID' => $data['id'],
				);
// 				if (!WmsWebservice::saveAllLayers($params, true)) {
// 					return false;
// 				}
				if (!WmsWebservice::saveAllLayers( $data['virtualservice_id'], $data['id'])) {
					return false;
				}
			}
			
			//Access Scope
			if(! $this->saveAccessScope($data))
				return false;
			
			return true;
		}
		return false;
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
}