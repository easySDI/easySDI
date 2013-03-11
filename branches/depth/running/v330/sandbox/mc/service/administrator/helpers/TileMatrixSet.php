<?php
require_once('TileMatrix.php');

class TileMatrixSet {
	public $identifier;
	public $srs;
	private $tileMatrixList = Array();
	
	public $allTileMatrix;
	
	public function __construct ($identifier, $srs) {
		$this->identifier = $identifier;
		$this->srs = $srs;
	}
	
	public function getTileMatrixList () {
		return $this->tileMatrixList;
	}
	
	public function addTileMatrix ($tileMatrix) {
		$this->tileMatrixList[$tileMatrix->identifier] = $tileMatrix;
	}
	
	public function sortLists () {
		uasort($this->tileMatrixList, array("TileMatrix", "compareIdentifiers"));
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('TileMatrixSet', $key)) {
				$this->{$key} = $value;
			}
			$this->tileMatrixList[$key]->loadData($value);
		}
	}
	
	public static function compareIdentifiers ($a, $b) {
		$al = strtolower($a->identifier);
		$bl = strtolower($b->identifier);
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	
	public function toArray() {
		$tms = get_object_vars($this);
		$tms['tileMatrixList'] = Array();
		
		foreach ($this->tileMatrixList as $tm) {
			array_push($tms['tileMatrixList'], Array($tm->identifier => $tm->toArray()));
		}
		
		return $tms;
	}
}