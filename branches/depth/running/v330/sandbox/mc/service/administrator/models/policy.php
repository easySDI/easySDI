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
		@$tab_physicalService =& JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
		@$tab_servicePolicy =& JTable::getInstance('servicepolicy', 'Easysdi_serviceTable');
		@$tab_layer =& JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
		@$tab_layerPolicy =& JTable::getInstance('wmslayerpolicy', 'Easysdi_serviceTable');
		
		$physicalservice = Array();
		$ps_list = $tab_physicalService->getListByConnectorType($layout);
		foreach ($ps_list as $ps) {
			$ps_arr = Array();
			$ps_arr['id'] = $ps->id;
			$ps_arr['name'] = $ps->name;
			$ps_arr['resourceurl'] = $ps->resourceurl;
			
			$sp = $tab_servicePolicy->getByIDs($ps->id, $pk);
			@$ps_arr['prefix'] = $sp->prefix;
			@$ps_arr['namespace'] = $sp->namespace;
			
			$layerList = $tab_layer->getListByPhysicalService($ps->id);
			$ps_arr['layers'] = Array();
			foreach ($layerList as $layer) {
				$lp = $tab_layerPolicy->getByIDs($layer->id, $pk);
				$layer_infos = Array(
					'id' => $layer->id,
					'name' => $layer->name,
					'description' => $layer->description
				);
				
				if (!empty($lp)) {
					$layer_infos['minimumscale'] = $lp->minimumscale;
					$layer_infos['maximumscale'] = $lp->maximumscale;
					$layer_infos['geographicfilter'] = $lp->geographicfilter;
				}
				
				@$ps_arr['layers'][] = $layer_infos;
			}
			$physicalservice[] = $ps_arr;
		}
		return $physicalservice;
	}
	
	/**
	 *
	 * Get item with WFS service connector
	 *
	*/
	protected function _getItemWFS($pk, $virtualservice_id) {
		@$tab_physicalService =& JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
		@$tab_servicePolicy =& JTable::getInstance('servicepolicy', 'Easysdi_serviceTable');
		@$tab_layer =& JTable::getInstance('featureclass', 'Easysdi_serviceTable');
		@$tab_layerPolicy =& JTable::getInstance('featureclasspolicy', 'Easysdi_serviceTable');
		
		$physicalservice = Array();
		$ps_list = $tab_physicalService->getListByConnectorType($layout);
		foreach ($ps_list as $ps) {
			$ps_arr = Array();
			$ps_arr['id'] = $ps->id;
			$ps_arr['name'] = $ps->name;
			$ps_arr['resourceurl'] = $ps->resourceurl;
			
			$sp = $tab_servicePolicy->getByIDs($ps->id, $pk);
			@$ps_arr['prefix'] = $sp->prefix;
			@$ps_arr['namespace'] = $sp->namespace;
			
			$layerList = $tab_layer->getListByPhysicalService($ps->id);
			$ps_arr['layers'] = Array();
			foreach ($layerList as $layer) {
				$lp = $tab_layerPolicy->getByIDs($layer->id, $pk);
				$layer_infos = Array(
					'id' => $layer->id,
					'name' => $layer->name,
					'description' => $layer->description
				);
				
				if (!empty($lp)) {
					$layer_infos['attributerestriction'] = $lp->attributerestriction;
					$layer_infos['boundingboxfilter'] = $lp->boundingboxfilter;
					$layer_infos['geographicfilter'] = $lp->geographicfilter;
				}
				
				@$ps_arr['layers'][] = $layer_infos;
			}
			$physicalservice[] = $ps_arr;
		}
		return $physicalservice;
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
		
		
		/*
		@$tab_layer =& JTable::getInstance('wmtslayer', 'Easysdi_serviceTable');
		@$tab_layerPolicy =& JTable::getInstance('wmtslayerpolicy', 'Easysdi_serviceTable');
		@$tab_tilematrixset =& JTable::getInstance('tilematrixset', 'Easysdi_serviceTable');
		@$tab_tilematrix =& JTable::getInstance('tilematrix', 'Easysdi_serviceTable');
		@$tab_tileMatrixPolicy =& JTable::getInstance('tilematrixpolicy', 'Easysdi_serviceTable');
		
		$physicalservice = Array();
		foreach ($ps_list as $ps) {
			$ps_arr = Array();
			$ps_arr['id'] = $ps->id;
			$ps_arr['name'] = $ps->name;
			$ps_arr['resourceurl'] = $ps->resourceurl;
			
			$layerList = $tab_layer->getListByPhysicalService($ps->id);
			$ps_arr['layers'] = Array();
			foreach ($layerList as $layer) {
				$layer_infos = Array(
					'id' => $layer->id,
					'name' => $layer->name,
					'description' => $layer->description
				);
				
				$lp = $tab_layerPolicy->getByIDs($layer->id, $pk);
				if (!empty($lp)) {
					$layer_infos['geographicfilter'] = $lp->geographicfilter;
					$layer_infos['spatialoperator'] = $lp->spatialoperator;
					$layer_infos['bbox_minimumx'] = $lp->bbox_minimumx;
					$layer_infos['bbox_minimumy'] = $lp->bbox_minimumy;
					$layer_infos['bbox_maximumx'] = $lp->bbox_maximumx;
					$layer_infos['bbox_maximumy'] = $lp->bbox_maximumy;
				}
				else {
					$layer_infos['geographicfilter'] = '';
					$layer_infos['spatialoperator'] = '';
					$layer_infos['bbox_minimumx'] = '';
					$layer_infos['bbox_minimumy'] = '';
					$layer_infos['bbox_maximumx'] = '';
					$layer_infos['bbox_maximumy'] = '';
				}
				
				$layer_infos['tileMatrixSetList'] = Array();
				$tileMatrixSetList = $tab_tilematrixset->getListByWMTSLayer($layer->id);
				foreach ($tileMatrixSetList as $tileMatrixSet) {
					$tileMatrixSet_arr = Array(
						'id' => $tileMatrixSet->id,
						'identifier' => $tileMatrixSet->identifier,
						'supported_crs' => $tileMatrixSet->supported_crs,
						'tileMatrixList' => Array(),
					);
					
					$tileMatrixList = $tab_tilematrix->getListByTileMatrixSet($tileMatrixSet->id);
					foreach ($tileMatrixList as $tileMatrix) {
						$policy_exists = $tab_tileMatrixPolicy->exist(Array(
							'wmtslayerpolicy_id' => $layer->id,
							'tilematrixset_id' => $tileMatrixSet->id,
							'tilematrix_id' => $tileMatrix->id,
						));
						
						$tileMatrixSet_arr['tileMatrixList'][] = Array(
							'id' => $tileMatrix->id,
							'identifier' => $tileMatrix->identifier,
							'scaledenominator' => $tileMatrix->scaledenominator,
							'topleftcorner' => $tileMatrix->topleftcorner,
							'tilewidth' => $tileMatrix->tilewidth,
							'tileheight' => $tileMatrix->tileheight,
							'matrixwidth' => $tileMatrix->matrixwidth,
							'matrixheight' => $tileMatrix->matrixheight,
							'selected' => ($policy_exists)?'selected':'',
						);
					}
					$layer_infos['tileMatrixSetList'][] = $tileMatrixSet_arr;
				}
				
				
				$ps_arr['layers'][] = $layer_infos;
			}
			$physicalservice[] = $ps_arr;
		}
		*/
		
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
			if ('WMS' == $serviceconnector_name) {
				$wmslayerpolicy = JTable::getInstance('wmslayerpolicy', 'Easysdi_serviceTable');
				if( !$wmslayerpolicy->save($data) ){
					return false;
				}
			}
			else if ('WFS' == $serviceconnector_name) {
				$featureclasspolicy = JTable::getInstance('featureclasspolicy', 'Easysdi_serviceTable');
				if( !$featureclasspolicy->save($data) ){
					return false;
				}
			}
			else if ('WMTS' == $serviceconnector_name) {
				$wmtslayerpolicy = JTable::getInstance('wmtslayerpolicy', 'Easysdi_serviceTable');
				if( !$wmtslayerpolicy->save($data) ){
					return false;
				}
			}
			
			if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name) {
				$servicepolicy = JTable::getInstance('servicepolicy', 'Easysdi_serviceTable');
				if( !$servicepolicy->save($data) ){
					return false;
				}
			}
			//die();
			return true;
		}
		return false;
	}
}