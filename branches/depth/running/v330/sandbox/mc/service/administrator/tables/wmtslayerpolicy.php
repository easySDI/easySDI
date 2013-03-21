<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
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
		
		foreach ($formated_data as $ps_id => $ps_data) {
			foreach ($ps_data as $layer_id => $layer_data) {
				var_dump($layer_data);
				$enabled = 0;
				if (isset($layer_data['enabled'])) {
					$enabled = ('on' == $layer_data['enabled'])?1:0;
				}
				$db->setQuery('
					SELECT id
					FROM #__sdi_wmtslayer_policy
					WHERE policy_id = ' . $policy_id . '
					AND wmtslayer_id = ' . $layer_id . ';
				');
				$db->execute();
				$query = $db->getQuery(true);
				if (0 != $db->getNumRows()) {
					$wmtslayerpolicy_id = $db->loadResult();
					$query->update('#__sdi_wmtslayer_policy')->set(Array(
						'enabled = ' . $enabled,
						'bbox_minimumx = ' . $layer_data['bboxminimumx'],
						'bbox_minimumy = ' . $layer_data['bboxminimumy'],
						'bbox_maximumx = ' . $layer_data['bboxmaximumx'],
						'bbox_maximumy = ' . $layer_data['bboxmaximumy'],
						'geographicfilter = ' . $layer_data['geographicfilter'],
						'spatialoperator = ' . ((isset($layer_data['spatialoperator']))?$layer_data['spatialoperator']:''),
					))->where(Array(
						'policy_id = ' . $policy_id,
						'wmtslayer_id = ' . $layer_id
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
				
				echo '<hr />';
			}
		}
		
		return true;
	}
}
