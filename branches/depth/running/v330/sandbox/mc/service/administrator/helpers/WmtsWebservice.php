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
		
		echo '
			<div class="row-fluid">
			<div class="span12">
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
						<select name="select_' . $tms->identifier . '">
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
			</div>
			</div>
		';
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
