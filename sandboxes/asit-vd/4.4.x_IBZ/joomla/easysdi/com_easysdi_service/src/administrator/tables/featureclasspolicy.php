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
class Easysdi_serviceTablefeatureclasspolicy extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_featureclasspolicy', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$db = JFactory::getDbo();
		
		$featureclasspolicy = $_POST['featureclasspolicy'];
		$modif = Array();
		
		foreach ($featureclasspolicy as $key => $value) {
			$infos = explode('_', $key);
			$modif[$infos[3]][$infos[1]] = $value;
		}
		
		foreach ($modif as $id => $value) {
			$db = JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $query->select('COUNT(*)');
                        $query->from('#__sdi_featureclasspolicy');
                        $query->where('featureclass_id = ' . (int)$id);
                        $query->where('policy_id = ' . (int)$src['id']);
                        
			$db->setQuery($query);
			$num = $db->loadResult();
			
			if (0 < $num) {
                                $query = $db->getQuery(true);
                                $query->update('#__sdi_featureclasspolicy');
                                $query->set('attributeRestriction = ' . $query->quote($value['attributerestriction']));
                                $query->set('boundingBoxFilter = ' . $query->quote($value['boundingboxfilter']));
                                $query->set('geographicFilter = "' . $query->quote($value['geographicfilter']));
                                $query->where('featureclass_id = ' . (int)$id);
                                $query->where('policy_id = ' . (int)$src['id']);
			}
			else {
                                $query = $db->getQuery(true);
                                $columns = array('attributeRestriction', 'boundingBoxFilter', 'geographicFilter', 'featureclass_id', 'policy_id');
                                $values = array($query->quote($value['attributerestriction']), $query->quote($value['boundingboxfilter']), $query->quote($value['geographicfilter']), $id, $src['id']);
                                $query->insert('#__sdi_featureclasspolicy');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));
                           
			}
			var_dump($query);
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
	public function getByIDs ($featureclass_id, $policy_id) {
		$db = JFactory::getDbo();
                
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_featureclasspolicy');
                $query->where('featureclass_id = ' . (int)$featureclass_id);
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
