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
class Easysdi_serviceTablewmslayerpolicy extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_wmslayerpolicy', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$db = JFactory::getDbo();
		
		$wmslayerpolicy = $_POST['wmslayerpolicy'];
		$modif = Array();
		
		foreach ($wmslayerpolicy as $key => $value) {
			$infos = explode('_', $key);
			$modif[$infos[3]][$infos[1]] = $value;
		}
		
		foreach ($modif as $id => $value) {
			$db = JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $query->select('COUNT(*)');
                        $query->from('#__sdi_wmslayerpolicy');
                        $query->where('wmslayer_id = ' . (int)$id);
                        $query->where('policy_id = ' . (int)$src['id']);
                        
			$db->setQuery($query);
			$num = $db->loadResult();
			
			if (0 < $num) {
                                $query = $db->getQuery(true);
                                $query->update('#__sdi_wmslayerpolicy');
                                $query->set('minimumScale = ' . $query->quote($value['minimumscale']));
                                $query->set('maximumScale = ' . $query->quote($value['maximumscale']));
                                $query->set('geographicFilter = ' . $query->quote($value['geographicfilter']));
                                $query->where('wmslayer_id = ' . (int)$id);
                                $query->where('policy_id = ' . (int)$src['id']);
                                
			}
			else {
                                $query = $db->getQuery(true);
                                $columns = array('minimumScale', 'maximumScale', 'geographicFilter', 'wmslayer_id', 'policy_id');
                                $values = array($query->quote($value['minimumscale']), $query->quote($value['maximumscale']), $query->quote($value['geographicfilter']), $id, $src['id']);
                                $query->insert('#__sdi_wmslayerpolicy');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));
                            
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
	public function getByIDs ($wmslayer_id, $policy_id) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_wmslayerpolicy');
                $query->where('wmslayer_id = ' . (int)$wmslayer_id);
                $query->where('policy_id = ' . (int)$policy_id);
                
		$db->setQuery($query);
		
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
