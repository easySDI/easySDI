<?php

// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

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
		$db->setQuery('
			SELECT ps.id AS physicalservice_id, psp.id AS physicalservicepolicy_id, p.id AS policy_id
			FROM #__sdi_policy p
			JOIN #__sdi_virtualservice vs
			ON vs.id = p.virtualservice_id
			JOIN #__sdi_virtual_physical vp
			ON vs.id = vp.virtualservice_id
			JOIN #__sdi_physicalservice ps
			ON ps.id = vp.physicalservice_id
			LEFT JOIN #__sdi_physicalservice_policy psp
			ON p.id = psp.policy_id AND ps.id = psp.physicalservice_id
			WHERE vs.id = ' . $virtualservice_id . '
			AND psp.policy_id IS NULL
			AND p.id LIKE \'' . $policy_id . '\';
		');
		
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
				$query->insert('#__sdi_physicalservice_policy')->columns('
					physicalservice_id, policy_id
				')->values('
					\'' . $result->physicalservice_id . '\', \'' . $result->policy_id . '\'
				');
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
