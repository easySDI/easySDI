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
                        $query = $db->getQuery(true);
                        $query->select('COUNT(*)');
                        $query->from('#__sdi_servicepolicy');
                        $query->where('physicalservice_id = ' . $id);
                        $query->where('policy_id = ' . $src['id']);
                        
			$db->setQuery($query);
			$num = $db->loadResult();
			
			if (0 < $num) {
                                $query = $db->getQuery(true);
                                $query->update('#__sdi_servicepolicy');
                                $query->set('prefix = ' . $query->quote($value['prefix']));
                                $query->set('namespace = ' . $query->quote($value['namespace']));
                                $query->where('physicalservice_id = ' . (int)$id);
                                $query->where('policy_id = ' . (int)$src['id']);
                                
			}
			else {
                                $query = $db->getQuery(true);
                                $columns = array('prefix', 'namespace', 'physicalservice_id', 'policy_id');
                                $values = array($query->quote($value['prefix']), $query->quote($value['namespace']), $id, $src['id']);
                                $query->insert('#__sdi_servicepolicy');
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
	public function getByIDs ($physicalservice_id, $policy_id) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_servicepolicy');
                $query->where('physicalservice_id = ' . (int)$physicalservice_id);
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
