<?php

/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';
/**
 * virtualmetadata Table class
 */
class Easysdi_serviceTablewmtslayerpolicy extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_wmtslayerpolicy', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$db = JFactory::getDbo();
		
		$wmtslayerpolicy = $_POST['wmtslayerpolicy'];
		$formated_data = Array();
		
		foreach ($wmtslayerpolicy as $key => $value) {
			$infos = explode('_', $key);
			$physicalService_id = $infos[2];
			$layer_id = $infos[3];
			if ('tilematrixsetpolicy' == $infos[1]) {
				$tileMatrixSet_id = $infos[4];
				$formated_data[$physicalService_id][$layer_id][$infos[1]][$tileMatrixSet_id] = $value;
			}
			else {
				$formated_data[$physicalService_id][$layer_id][$infos[1]] = $value;
			}
		}
		//var_dump($formated_data);
		
		foreach ($formated_data as $ps_id => $ps_data) {
			foreach ($ps_data as $layer_id => $layer_data) {
				var_dump($layer_data);
				$enabled = 0;
				if (isset($layer_data['enabled'])) {
					$enabled = ('on' == $layer_data['enabled'])?1:0;
				}
				$data = Array(
					'enabled' => $enabled,
					'bbox_minimumx' => $layer_data['bbox_minimumx'],
					'bbox_minimumy' => $layer_data['bbox_minimumy'],
					'bbox_maximumx' => $layer_data['bbox_maximumx'],
					'bbox_maximumy' => $layer_data['bbox_maximumy'],
					'geographicfilter' => $layer_data['geographicfilter'],
					'spatialoperator' => $layer_data['spatialoperator'],
				);
				parent::save($data, $orderingFilter , $ignore );
				foreach ($layer_data['tilematrixsetpolicy'] as $tms_id => $tm_id) {
					@$tilematrixpolicy =& JTable::getInstance('tilematrixpolicy', 'Easysdi_serviceTable');
					$data = Array(
						'wmtslayerpolicy_id' => 
					);
					$tilematrixpolicy->save($data);
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Return a servicepolicy
	 *
	 * @param Int A physicalService ID
	 * @param Int A policy ID
	 */
	public function getByIDs ($wmtslayer_id, $policy_id) {
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_wmtslayerpolicy
			WHERE wmtslayer_id = ' . $wmtslayer_id . '
			AND policy_id = ' . $policy_id . ';
		');
		
		try {
			$resultSet = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}

		// Legacy error handling switch based on the JError::$legacy switch.
		// @deprecated  12.1
		if (JError::$legacy && $this->_db->getErrorNum())	{
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}
		
		return $resultSet;
	}
}
