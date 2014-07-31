<?php
class TileMatrix {
	public $identifier;
	public $scaleDenominator;
	public $topLeftCorner;
	public $tileWidth;
	public $tileHeight;
	public $matrixWidth;
	public $matrixHeight;
	
	public $minTileRow;
	public $maxTileRow;
	public $minTileCol;
	public $maxTileCol;
	public $anyTile;
	
	public function __construct ($identifier, $params) {
		$this->identifier = $identifier;
		foreach ($params as $property => $value) {
			if (property_exists('TileMatrix', $property)) {
				$this->{$property} = $value;
			}
		}
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('TileMatrix', $key)) {
				$this->{$key} = $value;
			}
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
	
	public static function compareDenominators ($a, $b) {
		$al = strtolower($a->scaleDenominator);
		$bl = strtolower($b->scaleDenominator);
		if ($al == $bl) {
			return 0;
		}
		return ($al < $bl) ? +1 : -1;
	}
	
	public function toArray() {
		return get_object_vars($this);
	}
	
}