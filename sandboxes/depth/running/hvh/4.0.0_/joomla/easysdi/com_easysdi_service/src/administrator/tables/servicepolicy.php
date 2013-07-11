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
class Easysdi_serviceTableservicepolicy extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_servicepolicy', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$db = JFactory::getDbo();
		
		$servicepolicy = $_POST['servicepolicy'];
		$modif = Array();
		
		foreach ($servicepolicy as $key => $value) {
			$infos = explode('_', $key);
			$modif[$infos[2]][$infos[1]] = $value;
		}
		
		foreach ($modif as $id => $value) {
			$db = JFactory::getDbo();
			$db->setQuery('
				SELECT COUNT(*) FROM #__sdi_servicepolicy WHERE physicalservice_id = ' . $id . '
				AND policy_id = ' . $src['id'] . ';
			');
			$num = $db->loadResult();
			
			if (0 < $num) {
				$query = '
					UPDATE #__sdi_servicepolicy
					SET prefix = "' . $value['prefix'] . '",
					namespace = "' . $value['namespace'] . '"
					WHERE physicalservice_id = ' . $id . '
					AND policy_id = ' . $src['id'] . ';
				';
			}
			else {
				$query = '
					INSERT #__sdi_servicepolicy
					(prefix, namespace, physicalservice_id, policy_id)
					VALUES ("' . $value['prefix'] . '","' . $value['namespace'] . '",' . $id . ',' . $src['id'] . ');
				';
			}
			
			$db->setQuery($query);
			$db->execute();
		}
		return true;
	}
	
	/**
	 * Return a servicepolicy
	 *
	 * @param Int A physicalService ID
	 * @param Int A policy ID
	 */
	public function getByIDs ($physicalservice_id, $policy_id) {
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_servicepolicy
			WHERE physicalservice_id = ' . $physicalservice_id . '
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
