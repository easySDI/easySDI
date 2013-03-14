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
				WmtsWebservice::getWmtsLayerForm($params);
				break;
			case 'setWmtsLayerSettings':
				WmtsWebservice::setWmtsLayerSettings($params);
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getWmtsLayerForm ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$layerObj = WmtsWebservice::getWmtsLayerSettings(
			$physicalServiceID,
			$policyID,
			$layerID
		);
		
		echo '
			<label class="checkbox">
				<input type="checkbox" name="enabled" value="1" ' . ((1 == $layerObj->enabled)?'checked="checked"':'') . ' /> ' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_ENABLED') . '
			</label>
			<hr />
			<table>
				<tr>
					<td></td>
					<td>
						<input type="text" name="northBoundLatitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_NORTH_BOUND_LATITUDE') . '" value="' . $layerObj->northBoundLatitude . '"/>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>
						<input type="text" name="westBoundLongitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_WEST_BOUND_LONGITUDE') . '" value="' . $layerObj->westBoundLongitude . '"/>
					</td>
					<td></td>
					<td>
						<input type="text" name="eastBoundLongitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_EAST_BOUND_LONGITUDE') . '" value="' . $layerObj->eastBoundLongitude . '"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="text" name="southBoundLatitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_SOUTH_BOUND_LATITUDE') . '" value="' . $layerObj->southBoundLatitude . '"/>
					</td>
					<td></td>
				</tr>
			</table>
			<hr />
			<select name="spatial_operator">
				<option value="">' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_SPATIAL_OPERATOR_LABEL') . '</option>
				<option value="touch" ' . (('touch' == $layerObj->spatialOperator)?'selected="selected"':'') . '>Touch</option>
				<option value="within" ' . (('within' == $layerObj->spatialOperator)?'selected="selected"':'') . '>Within</option>
			</select>
			<hr />
		';
		
		echo '
			<table class="table">
				<thead>
					<tr>
						<th>Tile matrix set</th>
						<th>Min scale denominator</th>
					</tr>
				</thead>
				<tbody>
		';
		foreach ($layerObj->getTileMatrixSetList() as $tms) {
			echo'
				<tr>
					<td>' . $tms->identifier . '</td>
					<td>
						<select name="select[' . $tms->identifier . ']">
							<option value="">' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_TILE_MATRIX_LABEL') . '</option>
			';
			foreach ($tms->getTileMatrixList() as $tm) {
				echo '
					<option value="' . $tm->identifier . '">' . $tm->identifier . '</option>
				';
			}
			echo '
						</select>
					</td>
				</tr>
			';
		}
		echo '
				</tbody>
			</table>
			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
		';
	}
	
	private static function getWmtsLayerSettings ($physicalServiceID, $policyID, $layerID) {
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
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_wmtslayerpolicy
			WHERE physicalservice_id = ' . $physicalServiceID . '
			AND policy_id = ' . $policyID . '
			AND layer_identifier = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$wmtslayerpolicy = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		$data = Array();
		if (isset($wmtslayerpolicy)) {
			$data[$layerID] = Array(
				'enabled' => $wmtslayerpolicy->enabled,
				'spatialOperator' => $wmtslayerpolicy->spatialoperator,
				'westBoundLongitude' => $wmtslayerpolicy->westboundlongitude,
				'eastBoundLongitude' => $wmtslayerpolicy->eastboundlongitude,
				'northBoundLatitude' => $wmtslayerpolicy->northboundlatitude,
				'southBoundLatitude' => $wmtslayerpolicy->southboundlatitude,
				'tileMatrixSetList' => Array(),
			);
		}
		
		$wmtsObj = new WmtsPhysicalService($physicalServiceID, $url);
		$wmtsObj->getCapabilities();
		$wmtsObj->populate();
		$wmtsObj->loadData($data);
		$layerObj = $wmtsObj->getLayerByName($layerID);
		
		return $layerObj;
	}
	
	private static function setWmtsLayerSettings ($raw_GET) {
		$enabled = (isset($raw_GET['enabled']))?1:0;
		$physicalServiceID = $raw_GET['psID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT *
			FROM #__sdi_wmtslayerpolicy
			WHERE physicalservice_id = ' . $physicalServiceID . '
			AND policy_id = ' . $policyID . '
			AND layer_identifier = \'' . $layerID . '\';
		');
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		if (0 == $num_result) {
			$query = $db->getQuery(true);
			$query->insert('#__sdi_wmtslayerpolicy')->columns('
				layer_identifier, enabled, spatialoperator, westboundlongitude, eastboundlongitude, northboundlatitude, southboundlatitude, policy_id, physicalservice_id
			')->values('
				\'' . $layerID . '\', \'' . $enabled . '\', \'' . $raw_GET['spatial_operator'] . '\', \'' . $raw_GET['westBoundLongitude'] . '\', \'' . $raw_GET['eastBoundLongitude'] . '\', \'' . $raw_GET['northBoundLatitude'] . '\', \'' . $raw_GET['southBoundLatitude'] . '\', \'' . $policyID . '\', \'' . $physicalServiceID . '\'
			');
		}
		else {
			$query = $db->getQuery(true);
			$query->update('#__sdi_wmtslayerpolicy')->set(Array(
				'enabled = \'' . $enabled . '\'',
				'spatialoperator = \'' . $raw_GET['spatial_operator'] . '\'',
				'westboundlongitude = \'' . $raw_GET['westBoundLongitude'] . '\'',
				'eastboundlongitude = \'' . $raw_GET['eastBoundLongitude'] . '\'',
				'northboundlatitude = \'' . $raw_GET['northBoundLatitude'] . '\'',
				'southboundlatitude = \'' . $raw_GET['southBoundLatitude'] . '\'',
			))->where(Array(
				'physicalservice_id = \'' . $physicalServiceID . '\'',
				'policy_id = \'' . $policyID . '\'',
				'layer_identifier = \'' . $layerID . '\'',
			));
		}
		
		$db->setQuery($query);
		
		try {
			$db->execute();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		return WmtsWebservice::setTileMatrixSettings($physicalServiceID, $policyID, $layerID);
	}
	
	private static function setTileMatrixSettings ($physicalServiceID, $policyID, $layerID){
		
		
		
		return true;
	}
	
}
