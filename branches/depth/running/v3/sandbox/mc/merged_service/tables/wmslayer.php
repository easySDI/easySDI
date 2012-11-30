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
class Easysdi_serviceTablewmslayer extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_wmslayer', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		$data['guid'] 								= $src['guid'];
		$data['name'] 								= $src['name'];
		$data['description'] 				= $src['description'];
		$data['anyitem'] 						= $src['anyitem'];
		$data['physicalservice_id']	= $src['physicalservice_id'];
		
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	public function loadByPhysicalServiceID($physicalservice_id = null, $reset = true) {
		if ($reset) {
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
		$query->where($this->_db->quoteName('physicalservice_id') . ' = ' . (int) $physicalservice_id);

		$this->_db->setQuery($query);

		try {
			$row = $this->_db->loadAssoc();
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

		// Check that we have a result.
		if (empty($row)) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
			$this->setError($e);
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}
}
