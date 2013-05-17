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
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';
/**
 * virtualmetadata Table class
 */
class Easysdi_serviceTabletilematrixset extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_tilematrixset', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		//$data['guid'] 							= $src['guid'];
		$data['identifier'] 			= $src['identifier'];
		$data['supported_crs'] 		= $src['supported_crs'];
		$data['wmtslayer_id'] 		= $src['wmtslayer_id'];
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	public function getListByWMTSLayer($wmtsLayerID) {
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_tilematrixset
			WHERE wmtslayer_id = ' . $wmtsLayerID . ';
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
}
