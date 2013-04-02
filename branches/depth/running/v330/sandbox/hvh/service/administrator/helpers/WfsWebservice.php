<?php

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_service helper.
 */
class WfsWebservice {
	public static function request ($params) {
		switch ($params['method']) {
			case 'getFeatureTypeForm':
				echo WfsWebservice::getFeatureTypeForm($params);
				break;
			case 'setFeatureTypeSettings':
				if (WfsWebservice::setFeatureTypeSettings($params)) {
					echo 'OK';
				}
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getFeatureTypeForm ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$policyID = ('' == $raw_GET['policyID'])?0:$raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$layerObj = WfsWebservice::getFeatureTypeSettings(
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
			<label for="localgeographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_LOCAL_FILTER') . '</label>
			<textarea name="localgeographicfilter" rows="5" class="span12">' . $layerObj->localFilterGML . '</textarea>
			<br />
			<label for="remotegeographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_REMOTE_FILTER') . '</label>
			<textarea name="remotegeographicfilter" rows="5" class="span12">' . $layerObj->remoteFilterGML . '</textarea>
			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
		';
		return $html;
	}
	
	private static function getFeatureTypeSettings ($physicalServiceID, $policyID, $layerID) {
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'WfsPhysicalService.php');
		
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
			SELECT ftp.*, wsp.*, ftp.id AS featuretypepolicy_id
			FROM #__sdi_featuretype_policy ftp
			JOIN #__sdi_physicalservice_policy pp
			ON ftp.physicalservicepolicy_id = pp.id
			JOIN #__sdi_wfs_spatialpolicy wsp
			ON ftp.spatialpolicy_id = wsp.id
			WHERE pp.physicalservice_id = ' . $physicalServiceID . '
			AND pp.policy_id = ' . $policyID . '
			AND ftp.name = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$featuretypepolicy = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//preparing the object to be returned
		$data = Array();
		if (isset($featuretypepolicy)) {
			$data[$layerID] = Array(
				'enabled' => $featuretypepolicy->enabled,
				'remoteFilterGML' => $featuretypepolicy->localgeographicfilter,
				'localFilterGML' => $featuretypepolicy->remotegeographicfilter,
			);
		}
		
		$wfsObj = new WfsPhysicalService($physicalServiceID, $url);
		$wfsObj->getCapabilities();
		$wfsObj->populate();
		$wfsObj->loadData($data);
		$layerObj = $wfsObj->getLayerByName($layerID);
		
		return $layerObj;
	}
	
	private static function setFeatureTypeSettings ($raw_GET) {
		$enabled = (isset($raw_GET['enabled']))?1:0;
		$physicalServiceID = $raw_GET['psID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		
		//save Spatial Policy
		$db->setQuery('
			SELECT sp.id
			FROM #__sdi_wfs_spatialpolicy sp
			JOIN #__sdi_featuretype_policy ftp
			ON sp.id = ftp.spatialpolicy_id
			JOIN #__sdi_physicalservice_policy psp
			ON psp.id = ftp.physicalservicepolicy_id
			WHERE psp.physicalservice_id = ' . $physicalServiceID . '
			AND psp.policy_id = ' . $policyID . '
			AND ftp.name = \'' . $layerID . '\';
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
		
		if (0 == $num_result) {
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wfs_spatialpolicy')->columns('
				localgeographicfilter, remotegeographicfilter
			')->values('
				\'' . $raw_GET['localgeographicfilter'] . '\', \'' . $raw_GET['remotegeographicfilter'] . '\'
			');
		}
		else {
			$query = $db->getQuery(true);
			$query->update('#__sdi_wfs_spatialpolicy')->set(Array(
				'localgeographicfilter = \'' . $raw_GET['localgeographicfilter'] . '\'',
				'remotegeographicfilter = \'' . $raw_GET['remotegeographicfilter'] . '\'',
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
			SELECT ftp.id
			FROM #__sdi_featuretype_policy ftp
			JOIN #__sdi_physicalservice_policy psp
			ON psp.id = ftp.physicalservicepolicy_id
			WHERE psp.physicalservice_id = ' . $physicalServiceID . '
			AND psp.policy_id = ' . $policyID . '
			AND ftp.name = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
			$featuretypepolicy_id = $db->loadResult();
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
			$query->insert('#__sdi_featuretype_policy')->columns('
				name, enabled, spatialpolicy_id, physicalservicepolicy_id
			')->values('
				\'' . $layerID . '\', \'' . $enabled . '\', \'' . $spatial_policy_id . '\', \'' . $physicalservice_policy_id . '\'
			');
		}
		else {
			$query = $db->getQuery(true);
			$query->update('#__sdi_featuretype_policy')->set(Array(
				'enabled = \'' . $enabled . '\'',
			))->where(Array(
				'id = \'' . $featuretypepolicy_id . '\'',
			));
		}
		
		$db->setQuery($query);
		
		try {
			$db->execute();
			if (0 == $num_result) {
				$featuretypepolicy_id = $db->insertid();
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
