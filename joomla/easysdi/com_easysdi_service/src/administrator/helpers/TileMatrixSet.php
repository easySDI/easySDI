<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once('TileMatrix.php');

class TileMatrixSet {
	public $identifier;
	public $srs;
	public $srsUnit;
	public $minX;
	public $maxX;
	public $minY;
	public $maxY;
	public $maxTileMatrix;
	public $anyTileMatrix;
        private $tileMatrixList = Array();
	
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
	
	public function getTileMatrixByName ($name) {
		return $this->tileMatrixList[$name];
	}
	
	public function sortLists () {
		uasort($this->tileMatrixList, array("TileMatrix", "compareDenominators"));
	}
	
	/*
	 * Get all TileMatrix with upper denominators
	 * 
	 * @param String $indentifier : the identifier of the Tile Matrix
	 * 
	 * @return Array : An array with TileMatrix objects with denominators superior or equal to the Tile Matrix passed
	*/
	public function getUpperTileMatrix ($identifier) {
		$list = Array();
		
		if (isset($this->tileMatrixList[$identifier])) {
			$maxTmObj = $this->tileMatrixList[$identifier];
			
			foreach ($this->tileMatrixList as $tmObj) {
				if ($maxTmObj->scaleDenominator <= $tmObj->scaleDenominator) {
					$list[] = $tmObj;
				}
			}
		}
		
		return $list;
	}
	
	public function loadData ($data) {
            	foreach ($data as $key => $value) {
                    	if (property_exists('TileMatrixSet', $key)) {
                            	$this->{$key} = $value;
			}
			else {
                            	$this->tileMatrixList[$key]->loadData($value);
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
	
	public function toArray() {
		$tms = get_object_vars($this);
		$tms['tileMatrixList'] = Array();
		
		foreach ($this->tileMatrixList as $tm) {
			array_push($tms['tileMatrixList'], Array($tm->identifier => $tm->toArray()));
		}
		
		return $tms;
	}
	
	/**
	 * Calculate the range of tiles allowed by the BBOX filter
	 * Algorithm given by OGC 07-057r2 document (page 9).
	 * @param String $spatialOperator : The spatial operator to use
	 */
	public function calculateAuthorizedTiles($spatialOperator){
                if (empty($this->minX) || empty($this->maxX) || empty($this->minY) || empty($this->maxY)) {
                    foreach ($this->tileMatrixList as $tileMatrixObj) {   
                        $tileMatrixObj->anyTile = true;
                    }
                    return;
		}
		
		//Calculate the meterPerUnit parameter
		if ($this->srsUnit == "m" || $this->srsUnit == "metre") {
			$meterPerUnit = 1;
		}
		else if ($this->srsUnit == "grad") {
			$meterPerUnit = 100187.54;
		}
		else if ($this->srsUnit == "degree" || $this->srsUnit == "Degree"  || $this->srsUnit == "degrees") {
			$meterPerUnit = 111319.49;
		}
		else if ($this->srsUnit == "rad" || $this->srsUnit == "radian" || $this->srsUnit == "Radian") {
			$meterPerUnit = 6378137;
		}
		else {
			$meterPerUnit = 111319.49;
		}
		
		foreach ($this->tileMatrixList as $tileMatrixObj) {
			//Get the West and North coordinates of the top left corner of the TileMatrix.
			//
			//EPSG authority SRS definition (see : www.epsg-registry.org):
			//- Geographic SRS give the topLeftCorner as <TopLeftCorner>North West</TopLeftCorner>
			//- Projected SRS give the topLeftCorner as <TopLeftCorner>West North</TopLeftCorner>
			//OGC authority SRS defnition (see : OGC 07-057r7 document)
			//- all SRS give the topLeftCorner as <TopLeftCorner>West North</TopLeftCorner>
			//Others authorities are not supported.
			
			if ($this->srsUnit != "m" && $this->srsUnit != "metre" && strpos($this->srs, 'EPSG')) {
				$topLeftCornerY = substr($tileMatrixObj->topLeftCorner, 0, strpos($tileMatrixObj->topLeftCorner," "));
				$topLeftCornerX = substr($tileMatrixObj->topLeftCorner, strpos($tileMatrixObj->topLeftCorner," ")+1);
			}
			else {
				$topLeftCornerX = substr($tileMatrixObj->topLeftCorner, 0, strpos($tileMatrixObj->topLeftCorner," "));
				$topLeftCornerY = substr($tileMatrixObj->topLeftCorner, strpos($tileMatrixObj->topLeftCorner," ")+1);
			}
			
			//Calculate TileMatrix dimensions
			$pixelSpan = $tileMatrixObj->scaleDenominator * 0.00028 / $meterPerUnit;
			$tileSpanX = $tileMatrixObj->tileWidth * $pixelSpan;
			$tileSpanY = $tileMatrixObj->tileHeight * $pixelSpan;
			$tileMatrixMaxX = $topLeftCornerX + $tileSpanX * $tileMatrixObj->matrixWidth;
			$tileMatrixMinY = $topLeftCornerY - $tileSpanY * $tileMatrixObj->matrixHeight;
			$epsilon = 0.000001;
			//Calculate the range of tileset indexes included in the BBOX filter
			if ($spatialOperator == "touch") {
				$tileMinCol = floor(($this->minX - $topLeftCornerX)/$tileSpanX + $epsilon);
				$tileMaxCol = floor(($this->maxX - $topLeftCornerX)/$tileSpanX - $epsilon);
				$tileMinRow = floor(($topLeftCornerY - $this->maxY)/$tileSpanY + $epsilon);
				$tileMaxRow = floor(($topLeftCornerY - $this->minY)/$tileSpanY - $epsilon);
			}
			else {
				$tileMinCol = ceil(($this->minX - $topLeftCornerX)/$tileSpanX + $epsilon);
				$tileMaxCol = floor(($this->maxX - $topLeftCornerX)/$tileSpanX - $epsilon) -1;
				$tileMinRow = ceil(($topLeftCornerY - $this->maxY)/$tileSpanY + $epsilon) ;
				$tileMaxRow = floor(($topLeftCornerY - $this->minY)/$tileSpanY - $epsilon) -1;
			}
			
			//Error control to avoid requesting empty tiles
			if ($tileMinCol < 0) {
				$tileMinCol = 0;
			}
			if ($tileMaxCol < 0) {
				$tileMaxCol = -1;
			}
			if ($tileMinCol > $tileMaxCol) {
				$tileMinCol = -1;
			}
			if ($tileMinCol >= $tileMatrixObj->matrixWidth) {
				$tileMinCol = -1;
			}
			if ($tileMaxCol >= $tileMatrixObj->matrixWidth) {
				$tileMaxCol = $tileMatrixObj->matrixWidth -1;
			}
			if ($tileMinRow < 0) {
				$tileMinRow = 0;
			}
			if ($tileMaxRow < 0) {
				$tileMaxRow = -1;
			}
			if ($tileMinRow > $tileMaxRow) {
				$tileMinRow = -1;
			}
			if ($tileMinRow >= $tileMatrixObj->matrixHeight) {
				$tileMinRow = -1;
			}
			if ($tileMaxRow >= $tileMatrixObj->matrixHeight) {
				$tileMaxRow = $tileMatrixObj->matrixHeight -1;
			}
			
			//Control if the BBOX filter and the TileMatrix extent intersect each other
			if($tileMatrixMaxX < $this->minX
					|| $tileMatrixMinY > $this->maxY
					|| $topLeftCornerY < $this->minY
					|| $topLeftCornerX > $this->maxX){
				//No intersection : none of the Tile is allowed
				$tileMatrixObj->minTileRow = -1;
				$tileMatrixObj->maxTileRow = -1;
				$tileMatrixObj->minTileCol = -1;
				$tileMatrixObj->maxTileCol = -1;
				$tileMatrixObj->anyTile = false;
				
			}
			else{
				//Intersection
				$tileMatrixObj->minTileRow = $tileMinRow;
				$tileMatrixObj->maxTileRow = $tileMaxRow;
				$tileMatrixObj->minTileCol = $tileMinCol;
				$tileMatrixObj->maxTileCol = $tileMaxCol;
				$tileMatrixObj->anyTile = false;
			}
		}
	}
}