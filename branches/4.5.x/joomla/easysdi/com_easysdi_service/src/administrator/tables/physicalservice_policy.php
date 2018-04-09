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
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * policy Table class
 */
class Easysdi_serviceTablephysicalservice_policy extends sdiTable {
	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_policy', 'id', $db);
	}
	
	public function saveAll ($virtualservice_id, $policy_id = '%') {
		$db = JFactory::getDbo();
		
		// save each Physical Service Policy
                $query = $db->getQuery(true);
                $query->select('ps.id AS physicalservice_id, psp.id AS physicalservicepolicy_id, p.id AS policy_id');
                $query->from('#__sdi_policy p');
                $query->innerJoin('#__sdi_virtualservice vs ON vs.id = p.virtualservice_id');
                $query->innerJoin('#__sdi_virtual_physical vp ON vs.id = vp.virtualservice_id');
                $query->innerJoin('#__sdi_physicalservice ps ON ps.id = vp.physicalservice_id');
                $query->leftJoin('#__sdi_physicalservice_policy psp ON p.id = psp.policy_id AND ps.id = psp.physicalservice_id');
                $query->where('vs.id = ' . $virtualservice_id);
                $query->where('psp.policy_id IS NULL');
                $query->where('p.id LIKE ' . $query->quote($policy_id));
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$resultset = $db->loadObjectList();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		foreach ($resultset as $result) {
			$query = $db->getQuery(true);
			if (empty($result->physicalservicepolicy_id)) {
                                $columns = array('physicalservice_id', 'policy_id');
                                $values = array($result->physicalservice_id, $result->policy_id);
				$query->insert('#__sdi_physicalservice_policy')
                                        ->columns($query->quoteName($columns))
                                        ->values(implode(',', $values));
				$db->setQuery($query);
					
				try {
					$db->execute();
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
			}
		}
		
		return true;
	}
}
