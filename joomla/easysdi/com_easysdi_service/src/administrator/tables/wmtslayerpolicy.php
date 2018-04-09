<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
		$policy_id = $src['id'];
		$virtualservice_id = $src['virtualservice_id'];
		$wmtslayerpolicy_id = null;
		$formated_data = Array();
		
		foreach ($wmtslayerpolicy as $key => $value) {
			$infos = explode('_', $key);
			$physicalService_id = $infos[2];
			$layer_id = $infos[3];
			if ('tilematrixpolicy' == $infos[1]) {
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
                                $query = $db->getQuery(true);
                                $query->select('id');
                                $query->from('#__sdi_wmtslayerpolicy');
                                $query->where('policy_id = ' . (int)$policy_id);
                                $query->where('wmtslayer_id = ' . (int)$layer_id);
                                
				$db->setQuery($query);
				$db->execute();
				$query = $db->getQuery(true);
				if (0 != $db->getNumRows()) {
					$wmtslayerpolicy_id = $db->loadResult();
					$query->update('#__sdi_wmtslayerpolicy')->set(Array(
						'enabled = ' . $enabled,
						'bbox_minimumx = ' . $layer_data['bboxminimumx'],
						'bbox_minimumy = ' . $layer_data['bboxminimumy'],
						'bbox_maximumx = ' . $layer_data['bboxmaximumx'],
						'bbox_maximumy = ' . $layer_data['bboxmaximumy'],
						'geographicfilter = ' . $layer_data['geographicfilter'],
						'spatialoperator = ' . $query->quote(((isset($layer_data['spatialoperator']))?$layer_data['spatialoperator']:'')),
					))->where(Array(
						'policy_id = ' . (int)$policy_id,
						'wmtslayer_id = ' . (int)$layer_id
					));
					$db->setQuery($query);
					$db->execute();
				}
				else {
					$query->insert('#__sdi_wmtslayerpolicy');
					$query->columns(
						'policy_id, wmtslayer_id, enabled, bbox_minimumx, bbox_minimumy, bbox_maximumx, bbox_maximumy, geographicfilter, spatialoperator'
					);
					$query->values(
						$policy_id . ', ' . $layer_id . ', \'' . $enabled . '\', \'' . $layer_data['bboxminimumx'] . '\', \'' . $layer_data['bboxminimumy'] . '\', \'' . $layer_data['bboxmaximumx'] . '\', \'' . $layer_data['bboxmaximumy'] . '\', \'' . $layer_data['geographicfilter'] . '\', \'' . ((isset($layer_data['spatialoperator']))?$layer_data['spatialoperator']:'') . '\''
					);
					$db->setQuery($query);
					$db->execute();
					$wmtslayerpolicy_id = $db->insertid();
				}
				
				var_dump((String) $query);
				
				foreach ($layer_data['tilematrixpolicy'] as $tms_id => $tm_id) {
                                        $query = $db->getQuery(true);
                                        $query->select('identifier');
                                        $query->from('#__sdi_tilematrix');
                                        $query->where('id = ' . (int)$tm_id);
                                        
					$db->setQuery($query);
					$db->execute();
					$tm_identifier = $db->loadResult();
					
					//TODO : translate the query in multi DB language
					$db->setQuery('
						SELECT id, CAST(SUBSTRING_INDEX(identifier,\':\',-1) AS UNSIGNED) as num, identifier
						FROM #__sdi_tilematrix
						WHERE CAST(SUBSTRING_INDEX(identifier,\':\',-1) AS UNSIGNED) <= CAST(SUBSTRING_INDEX(\'' . $tm_identifier . '\', \':\', -1) AS UNSIGNED)
						AND tilematrixset_id = ' . $tms_id . '
						ORDER BY num ASC;
					');
					$db->excute();
					foreach ($db->loadObjectList() as $row) {
						var_dump($row);
						
					}
					
					$query = $db->getQuery(true);
					$query->delete('#__sdi_wmtslayerpolicy')
                                                ->where(Array(
						'wmtslayerpolicy_id = ' . $wmtslayerpolicy_id,
						'tilematrixset_id = ' . $tms_id
					));
					$db->setQuery($query);
					$db->execute();
					
				}
				echo '<hr />';
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
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_wmtslayerpolicy');
                $query->where('wmtslayer_id = ' . (int)$wmtslayer_id);
                $query->where('policy_id = ' . (int)$policy_id);
                
		$db->setQuery($query);
		
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
