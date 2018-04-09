<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once('Layer.php');
require_once('TileMatrixSet.php');

class WmtsLayer extends Layer{
	private $tileMatrixSetList = Array();
	
	public $enabled;
	public $westBoundLongitude;
	public $eastBoundLongitude;
	public $northBoundLatitude;
	public $southBoundLatitude;
	public $spatialOperator;
	public $allTileMatrixSet;
	
	public function getTileMatrixSetList () {
		return $this->tileMatrixSetList;
	}
	
	public function addTileMatrixSet ($tileMatrixSet) {
		$this->tileMatrixSetList[$tileMatrixSet->identifier] = $tileMatrixSet;
	}
	
	public function getTileMatrixSetByName ($name) {
		return $this->tileMatrixSetList[$name];
	}
	
	public function sortLists () {
		foreach ($this->tileMatrixSetList as $tileMatrixSet) {
			$tileMatrixSet->sortLists();
		}
		uasort($this->tileMatrixSetList, array("TileMatrixSet", "compareIdentifiers"));
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('WmtsLayer', $key) && 'tileMatrixSetList' != $key) {
				$this->{$key} = $value;
				if ('enabled' != $key) {
					$this->hasConfig = true;
				}
			}
		}
		
		if (isset($data['tileMatrixSetList'])) {
			foreach ($data['tileMatrixSetList'] as $key => $value) {
                            	$this->tileMatrixSetList[$key]->loadData($value);
			}
		}
	}
	
	public function toArray() {
		$layer = get_object_vars($this);
		$layer['tileMatrixSetList'] = Array();
		
		foreach ($this->tileMatrixSetList as $tms) {
			array_push($layer['tileMatrixSetList'], Array($tms->identifier => $tms->toArray()));
		}
		
		return $layer;
	}
	
	/**
	 * Set the srsUnit of all the layers
	 * 
	 * @param array $arr_unit	an associative array containing unit for each srs Array(srs=>unit)
	 * 
	 * @param boolean $override	when true, ignore if value are already set in the layer
	 * 
	 */
	public function setAllSRSUnit ($arr_unit, $override = false) {
		foreach ($this->tileMatrixSetList as $tileMatrixSetIdentifier => $tileMatrixSet) {
			if (true === $override || (!isset($tileMatrixSet->northBoundLatitude))) {
			//	$tileMatrixSet->srsUnit = (isset($arr_unit->{$tileMatrixSet->srs}))?$arr_unit->{$tileMatrixSet->srs}:null;
			}
		}
	}
	
	public function calculateAuthorizedTiles () {
		foreach ($this->tileMatrixSetList as $tmsObj) {
			$tmsObj->calculateAuthorizedTiles($this->spatialOperator);
		}
	}
}