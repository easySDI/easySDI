<?php
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
	
	public function calculateAuthorizedTiles () {
		foreach ($this->tileMatrixSetList as $tmsObj) {
			$tmsObj->calculateAuthorizedTiles($this->spatialOperator);
		}
	}
}