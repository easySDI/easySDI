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
class Easysdi_serviceTabletilematrixpolicy extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_tilematrixpolicy', 'id', $db);
	}
	
	public function save ($src) {
		
	}
	
	/**
	 * Check if a tilematrixpolicy exists
	 *
	 * @param Array An array containing the foreign keys on which to search
	 */
	public function exist ($param) {
		$exist = false;
		$query = 'SELECT * FROM #__sdi_tilematrixpolicy';
		
		$separator = ' WHERE ';
		if (isset($param['wmtslayerpolicy_id'])) {
			$query .= $separator . 'wmtslayerpolicy_id = ' . $param['wmtslayerpolicy_id'];
			$separator = ' AND ';
		}
		if (isset($param['tilematrixset_id'])) {
			$query .= $separator . 'tilematrixset_id = ' . $param['tilematrixset_id'];
			$separator = ' AND ';
		}
		if (isset($param['tilematrix_id'])) {
			$query .= $separator . 'tilematrix_id = ' . $param['tilematrix_id'];
			$separator = ' AND ';
		}
		
		$db = JFactory::getDbo();
		$db->setQuery($query);
		
		try {
			$db->execute();
			if (0 < $db->getNumRows()) {
				$exist = true;
			}
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		return $exist;
	}
}
