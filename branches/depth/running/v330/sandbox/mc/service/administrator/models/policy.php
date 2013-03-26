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
			$layout = JRequest::getVar('layout',null);
			$virtualservice_id = JRequest::getVar('virtualservice_id',null);
			
			if (!isset($item->virtualservice_id)) {
				$item->virtualservice_id = (int) $virtualservice_id;
			}
			
			if (method_exists('Easysdi_serviceModelpolicy', '_getItem' . $layout)) {
				$item->physicalService = $this->{'_getItem' . $layout}($pk, $virtualservice_id);
			}
		}

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
		
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wmsObjList = Array();
		foreach ($ps_list as $ps) {
			$wmsObj = new WmsPhysicalService($ps->id, $ps->resourceurl);
			$wmsObj->getCapabilities();
			$wmsObj->populate();
			$wmsObj->sortLists();
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
		
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wfsObjList = Array();
		foreach ($ps_list as $ps) {
			$wfsObj = new WfsPhysicalService($ps->id, $ps->resourceurl);
			$wfsObj->getCapabilities();
			$wfsObj->populate();
			$wfsObj->sortLists();
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
		
		$ps_list = $tab_physicalService->getListByVirtualService($virtualservice_id);
		$wmtsObjList = Array();
		foreach ($ps_list as $ps) {
			$wmtsObj = new WmtsPhysicalService($ps->id, $ps->resourceurl);
			$wmtsObj->getCapabilities();
			$wmtsObj->populate();
			$wmtsObj->sortLists();
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
		$data['virtualservice_id'] = JRequest::getVar('vs_id',null);
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
				$params = Array(
					'virtualServiceID' => $data['virtualservice_id'],
					'policyID' => $data['id'],
				);
				if (!WmsWebservice::saveAllLayers($params, true)) {
					return false;
				}
			}
			
			return true;
		}
		return false;
	}
}