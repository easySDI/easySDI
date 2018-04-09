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
class Easysdi_serviceTablefeatureclass extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_featureclass', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		//$data['guid'] 							= $src['guid'];
		$data['name'] 							= $src['name'];
		$data['description'] 				= $src['description'];
		$data['physicalservice_id']	= $src['physicalservice_id'];
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	public function getListByPhysicalService($physicalservice_id) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_featureclass');
                $query->where('physicalservice_id = ' . (int)$physicalservice_id);
                
		$db->setQuery($query);
		
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
	 * Delete all occurences of feature class based on their physicalservice_id
	 * 
	 * @param Int A physical service ID
	*/
	function wipeByPhysicalId($physicalservice_id) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->delete('#__sdi_featureclass');
                $query->where('physicalservice_id = ' . (int)$physicalservice_id);
                
		$db->setQuery($query);
		$db->execute();
	}
}
