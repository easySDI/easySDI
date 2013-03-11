<?php

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_service helper.
 */
class WmtsWebservice {
	public static function request ($params) {
		switch ($params['method']) {
			case 'getWmtsLayerForm':
				WmtsWebservice::getWmtsLayerForm(
					$params['physicalServiceID'],
					$params['layerID']
				);
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getWmtsLayerForm ($physicalServiceID, $layerID) {
		$layerObj = WmtsWebservice::getWmtsLayerSettings(
			$physicalServiceID,
			$layerID
		);
		var_dump($layerObj);
	}
	
	private static function getWmtsLayerSettings ($physicalServiceID, $layerID) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmtsPhysicalService.php');
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT resourceurl
			FROM #__sdi_physicalservice 
			WHERE id = ' . $physicalServiceID . ';
		');
		
		try {
			$url = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//TODO : retrieve data to load it in the object
		
		$wmtsObj = new WmtsPhysicalService($physicalServiceID, $url);
		$wmtsObj->getCapabilities();
		$wmtsObj->populate();
		$wmtsObj->loadData(Array());
		$layerObj = $wmtsObj->getLayerByName($layerID);
		
		return $layerObj;
	}
	
}
