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
class Easysdi_serviceTablewmtslayer extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_wmtslayer', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		//$data['guid'] 						= $src['guid'];
		$data['name'] 							= $src['name'];
		$data['description'] 				= $src['description'];
		$data['physicalservice_id']	= $src['physicalservice_id'];
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	public function getListByPhysicalService($physicalservice_id) {
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_wmtslayer
			WHERE physicalservice_id = ' . $physicalservice_id . ';
		');
		
		try {
			$resultSet = $db->loadObjectList();
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
	
	
	/**
	 * Delete all occurences of wmtslayer based on their physicalservice_id
	 * 
	 * @param Int A physical service ID
	*/
	function wipeByPhysicalId($physicalservice_id) {
		$db = JFactory::getDbo();
		$db->setQuery('
			DELETE FROM #__sdi_wmtslayer
			WHERE physicalservice_id = ' . $physicalservice_id . ';
		');
		$db->execute();
	}
}
