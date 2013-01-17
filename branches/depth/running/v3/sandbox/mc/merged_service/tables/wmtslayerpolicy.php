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
		$modif = Array();
		
		foreach ($wmtslayerpolicy as $key => $value) {
			$infos = explode('_', $key);
			$modif[$infos[3]][$infos[1]] = $value;
		}
		
		foreach ($modif as $id => $value) {
			$db = JFactory::getDbo();
			$db->setQuery('
				SELECT COUNT(*) FROM #__sdi_wmtslayerpolicy WHERE wmtslayer_id = ' . $id . '
				AND policy_id = ' . $src['id'] . ';
			');
			$num = $db->loadResult();
			
			if (0 < $num) {
				$query = '
					UPDATE #__sdi_wmtslayerpolicy
					SET minimumScale = "' . $value['minimumscale'] . '",
					maximumScale = "' . $value['maximumscale'] . '",
					geographicFilter = "' . $value['geographicfilter'] . '"
					WHERE wmtslayer_id = ' . $id . '
					AND policy_id = ' . $src['id'] . ';
				';
			}
			else {
				$query = '
					INSERT #__sdi_wmtslayerpolicy
					(minimumScale, maximumScale, geographicFilter, wmtslayer_id, policy_id)
					VALUES ("' . $value['minimumscale'] . '","' . $value['maximumscale'] . '","' . $value['geographicfilter'] . '",' . $id . ',' . $src['id'] . ');
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
