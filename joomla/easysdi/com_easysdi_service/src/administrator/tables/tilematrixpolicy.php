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
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_tilematrixpolicy');

		if (isset($param['wmtslayerpolicy_id'])) {
                        $query->where('wmtslayerpolicy_id = ' . $param['wmtslayerpolicy_id']);
		}
		if (isset($param['tilematrixset_id'])) {
                        $query->where('tilematrixset_id = ' . $param['tilematrixset_id']);
		}
		if (isset($param['tilematrix_id'])) {
                        $query->where('tilematrix_id = ' . $param['tilematrix_id']);
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
