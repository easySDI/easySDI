<?php

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_service helper.
 */
class WmsWebservice {
	public static function request ($params) {
		switch ($params['method']) {
			case 'getWmsLayerForm':
				echo WmsWebservice::getWmsLayerForm($params);
				break;
			case 'setWmsLayerSettings':
				if (WmsWebservice::setWmsLayerSettings($params)) {
					echo 'OK';
				}
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getWmsLayerForm ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$policyID = ('' == $raw_GET['policyID'])?0:$raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$layerObj = WmsWebservice::getWmsLayerSettings(
			$physicalServiceID,
			$policyID,
			$layerID
		);
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_sys_spatialoperator
			WHERE state = 1
			ORDER BY ordering;
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
		
		$html = '
			<label class="checkbox">
				<input type="checkbox" name="enabled" value="1" ' . ((1 == $layerObj->enabled)?'checked="checked"':'') . ' /> ' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_ENABLED') . '
			</label>
			<hr />
			<label for="minimumscale">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MINIMUM_SCALE') . '</label>
			<input type="text" name="minimumscale" value="' . $layerObj->minimumScale . '" />
			<br />
			<label for="maximumscale">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MAXIMUM_SCALE') . '</label>
			<input type="text" name="maximumscale" value="' . $layerObj->maximumScale . '" />
			<br />
			<label for="geographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_FILTER') . '</label>
			<textarea name="geographicfilter" rows="5" class="span12">' . $layerObj->geographicFilter . '</textarea>
			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
		';
		return $html;
	}
	
	private static function getWmsLayerSettings ($physicalServiceID, $policyID, $layerID) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WmsPhysicalService.php');
		
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
		
		$db->setQuery('
			SELECT wlp.*, wsp.*, wlp.id AS wmslayerpolicy_id
			FROM #__sdi_wmslayer_policy wlp
			JOIN #__sdi_physicalservice_policy pp
			ON wlp.physicalservicepolicy_id = pp.id
			JOIN #__sdi_wms_spatialpolicy wsp
			ON wlp.spatialpolicy_id = wsp.id
			WHERE pp.physicalservice_id = ' . $physicalServiceID . '
			AND pp.policy_id = ' . $policyID . '
			AND wlp.name = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$wmslayerpolicy = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//preparing the object to be returned
		$data = Array();
		if (isset($wmslayerpolicy)) {
			$data[$layerID] = Array(
				'enabled' => $wmslayerpolicy->enabled,
				'geographicFilter' => $wmslayerpolicy->geographicfilter,
				'maximumScale' => $wmslayerpolicy->maximumscale,
				'minimumScale' => $wmslayerpolicy->minimumscale,
			);
		}
		
		$wmsObj = new WmsPhysicalService($physicalServiceID, $url);
		$wmsObj->getCapabilities();
		$wmsObj->populate();
		$wmsObj->loadData($data);
		$layerObj = $wmsObj->getLayerByName($layerID);
		
		return $layerObj;
	}
	
	private static function setWmsLayerSettings ($raw_GET) {
		$enabled = (isset($raw_GET['enabled']))?1:0;
		$physicalServiceID = $raw_GET['psID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		
		//save Spatial Policy
		$db->setQuery('
			SELECT sp.id
			FROM #__sdi_wms_spatialpolicy sp
			JOIN #__sdi_wmslayer_policy wlp
			ON sp.id = wlp.spatialpolicy_id
			JOIN #__sdi_physicalservice_policy psp
			ON psp.id = wlp.physicalservicepolicy_id
			WHERE psp.physicalservice_id = ' . $physicalServiceID . '
			AND psp.policy_id = ' . $policyID . '
			AND wlp.name = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
			$spatial_policy_id = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//TODO: add values calculated by the JS
		if (0 == $num_result) {
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wms_spatialpolicy')->columns('
				geographicfilter, minimumscale, maximumscale
			')->values('
				\'' . $raw_GET['geographicfilter'] . '\', \'' . $raw_GET['minimumscale'] . '\', \'' . $raw_GET['maximumscale'] . '\'
			');
		}
		else {
			$query = $db->getQuery(true);
			$query->update('#__sdi_wms_spatialpolicy')->set(Array(
				'geographicfilter = \'' . $raw_GET['geographicfilter'] . '\'',
				'minimumscale = \'' . $raw_GET['minimumscale'] . '\'',
				'maximumscale = \'' . $raw_GET['maximumscale'] . '\'',
			))->where(Array(
				'id = \'' . $spatial_policy_id . '\'',
			));
		}
		
		$db->setQuery($query);
		
		try {
			$db->execute();
			if (0 == $num_result) {
				$spatial_policy_id = $db->insertid();
			}
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//save Feature Type Policy
		$db->setQuery('
			SELECT wlp.id
			FROM #__sdi_wmslayer_policy wlp
			JOIN #__sdi_physicalservice_policy psp
			ON psp.id = wlp.physicalservicepolicy_id
			WHERE psp.physicalservice_id = ' . $physicalServiceID . '
			AND psp.policy_id = ' . $policyID . '
			AND wlp.name = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
			$wmslayerpolicy_id = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		
		if (0 == $num_result) {
			$db->setQuery('
				SELECT id
				FROM #__sdi_physicalservice_policy
				WHERE physicalservice_id = ' . $physicalServiceID . '
				AND policy_id = ' . $policyID . ';
			');
			
			try {
				$db->execute();
				$physicalservice_policy_id = $db->loadResult();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wmslayer_policy')->columns('
				name, enabled, spatialpolicy_id, physicalservicepolicy_id
			')->values('
				\'' . $layerID . '\', \'' . $enabled . '\', \'' . $spatial_policy_id . '\', \'' . $physicalservice_policy_id . '\'
			');
		}
		else {
			$query = $db->getQuery(true);
			$query->update('#__sdi_wmslayer_policy')->set(Array(
				'enabled = \'' . $enabled . '\'',
			))->where(Array(
				'id = \'' . $wmslayerpolicy_id . '\'',
			));
		}
		
		$db->setQuery($query);
		
		try {
			$db->execute();
			if (0 == $num_result) {
				$wmslayerpolicy_id = $db->insertid();
			}
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		return true;
	}
	
}
