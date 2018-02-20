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
class Easysdi_serviceTabletilematrix extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_tilematrix', 'id', $db);
	}
	
	public function getListByTileMatrixSet($tileMatrixSetID) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_tilematrix');
                $query->where('tilematrixset_id = ' . (int)$tileMatrixSetID);
                
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
	
	public function saveBatch ($src) {
		$query = 'INSERT INTO #__sdi_tilematrix (identifier, scaledenominator, topleftcorner, tilewidth, tileheight, matrixwidth, matrixheight, tilematrixset_id) VALUES ';
		foreach ($src as $tileMatrix) {
			$query .= '(' . $query->quote($tileMatrix['identifier']) . ', ' . $query->quote($tileMatrix['scaledenominator']) . ', ' . $query->quote($tileMatrix['topleftcorner']) . ', ' . $query->quote($tileMatrix['tilewidth']) . ', ' . $query->quote($tileMatrix['tileheight']) . ', ' . $query->quote($tileMatrix['matrixwidth']) . ', ' . $query->quote($tileMatrix['matrixheight']) . ', ' . $query->quote($tileMatrix['tilematrixset_id']) . '),';
		}
		$query = substr($query, 0, -1) . ';';
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$db->execute();
	}
}
