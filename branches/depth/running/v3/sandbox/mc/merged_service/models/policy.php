<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
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
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			//Do any procesing on fields here if needed
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'tables');
			
			$pk = $item->id;
			$layout = JRequest::getVar('layout',null);
			$virtualservice_id = JRequest::getVar('virtualservice_id',null);
			
			if (!isset($item->virtualservice_id)) {
				$item->virtualservice_id = (int) $virtualservice_id;
			}
			
			$db = JFactory::getDbo();
			$db->setQuery('
				SELECT sc.value
				FROM #__sdi_virtualservice vs
				JOIN #__sdi_sys_serviceconnector sc
				ON sc.id = vs.sys_serviceconnector_id
				WHERE vs.id = ' . $item->virtualservice_id . ';
			');
			
			try {
				$serviceconnector_name = $db->loadResult();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			@$physicalService =& JTable::getInstance('physicalservice', 'Easysdi_serviceTable');
			@$servicePolicy =& JTable::getInstance('servicepolicy', 'Easysdi_serviceTable');
			
			if ('WMS' == $serviceconnector_name) {
				@$layer =& JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
				@$layerPolicy =& JTable::getInstance('wmslayerpolicy', 'Easysdi_serviceTable');
			}
			else if ('WFS' == $serviceconnector_name) {
				@$layer =& JTable::getInstance('featureclass', 'Easysdi_serviceTable');
				@$layerPolicy =& JTable::getInstance('featureclasspolicy', 'Easysdi_serviceTable');
			}
			
			if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name) {
				$item->physicalservice = Array();
				$ps_list = $physicalService->getListByConnectorType($layout);
				foreach ($ps_list as $ps) {
					$ps_arr = Array();
					$ps_arr['id'] = $ps->id;
					$ps_arr['name'] = $ps->name;
					$ps_arr['resourceurl'] = $ps->resourceurl;
					
					$sp = $servicePolicy->getByIDs($ps->id, $pk);
					@$ps_arr['prefix'] = $sp->prefix;
					@$ps_arr['namespace'] = $sp->namespace;
					
					$layerList = $layer->getListByPhysicalService($ps->id);
					$ps_arr['layers'] = Array();
					foreach ($layerList as $layer) {
						$lp = $layerPolicy->getByIDs($layer->id, $pk);
						$layer_infos = Array(
							'id' => $layer->id,
							'name' => $layer->name,
							'description' => $layer->description
						);
						
						if ('WMS' == $serviceconnector_name) {
							$layer_infos['minimumscale'] = $lp->minimumscale;
							$layer_infos['maximumscale'] = $lp->maximumscale;
							$layer_infos['geographicfilter'] = $lp->geographicfilter;
						}
						else if ('WFS' == $serviceconnector_name) {
							$layer_infos['attributerestriction'] = $lp->attributerestriction;
							$layer_infos['boundingboxfilter'] = $lp->boundingboxfilter;
							$layer_infos['geographicfilter'] = $lp->geographicfilter;
						}
						
						@$ps_arr['layers'][] = $layer_infos;
					}
					$item->physicalservice[] = $ps_arr;
				}
			}
			
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

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sdi_policy');
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
			ON sc.id = vs.sys_serviceconnector_id
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
				$wmslayerpolicy =& JTable::getInstance('wmslayerpolicy', 'Easysdi_serviceTable');
				if( !$wmslayerpolicy->save($data) ){
					return false;
				}
			}
			else if ('WFS' == $serviceconnector_name) {
				$featureclasspolicy =& JTable::getInstance('featureclasspolicy', 'Easysdi_serviceTable');
				if( !$featureclasspolicy->save($data) ){
					return false;
				}
			}
			
			if ('WMS' == $serviceconnector_name || 'WFS' == $serviceconnector_name) {
				$servicepolicy =& JTable::getInstance('servicepolicy', 'Easysdi_serviceTable');
				if( !$servicepolicy->save($data) ){
					return false;
				}
			}
			
			return true;
		}
		return false;
	}
}