<?php
require_once('TileMatrix.php');

class TileMatrixSet {
	public $identifier;
	public $srs;
	public $srsUnit;
	public $maxTileMatrix;
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
		uasort($this->tileMatrixList, array("TileMatrix", "compareDenominators"));
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
	 * @param Array $bboxSRS : Associative array (keys : minX maxX minY maxY) containing the bounding box
	 * @param String $spatialOperator : The spatial operator to use
	 */
	public function calculateAuthorizedTiles($bboxSRS, $spatialOperator){
		//Calculate the meterPerUnit parameter
		if ($this->srsUnit == "m" || $this->srsUnit == "metre") {
			$meterPerUnit = 1;
		}
		else if ($this->srsUnit == "grad") {
			$meterPerUnit = 100187.54;
		}
		else if ($this->srsUnit == "degree" || $this->srsUnit == "Degree") {
			$meterPerUnit = 111319.49;
		}
		else if ($this->srsUnit == "rad" || $this->srsUnit == "radian" || $this->srsUnit == "Radian") {
			$meterPerUnit = 6378137;
		}
		else {
			$meterPerUnit = 111319.49;
		}
		
		foreach ($this->tileMatrixList as $tileMatrixObj) {
			//Get the East and North coordinates of the top left corner of the TileMatrix.
			//
			//EPSG authority SRS definition (see : www.epsg-registry.org):
			//- Geographic SRS give the topLeftCorner as <TopLeftCorner>North East</TopLeftCorner>
			//- Projected SRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
			//OGC authority SRS defnition (see : OGC 07-057r7 document)
			//- all SRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
			//Others authorities are not supported.
			if(!strpos($this->srsUnit,'m') && strpos($this->srs,'EPSG')){
				$topLeftCornerY = substr($tileMatrixObj->topLeftCorner, 0, strpos($tileMatrixObj->topLeftCorner," "));
				$topLeftCornerX = substr($tileMatrixObj->topLeftCorner, strpos($tileMatrixObj->topLeftCorner," ")+1);
			}else{
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
			if($spatialOperator == "touch"){
				$tileMinCol = floor(($bboxSRS['minX'] - $topLeftCornerX)/$tileSpanX + $epsilon);
				$tileMaxCol = floor(($bboxSRS['maxX'] - $topLeftCornerX)/$tileSpanX - $epsilon);
				$tileMinRow = floor(($topLeftCornerY - $bboxSRS['maxY'])/$tileSpanY + $epsilon);
				$tileMaxRow = floor(($topLeftCornerY - $bboxSRS['minY'])/$tileSpanY - $epsilon);
			}else{
				$tileMinCol = ceil(($bboxSRS['minX'] - $topLeftCornerX)/$tileSpanX + $epsilon);
				$tileMaxCol = floor(($bboxSRS['maxX'] - $topLeftCornerX)/$tileSpanX - $epsilon) -1;
				$tileMinRow = ceil(($topLeftCornerY - $bboxSRS['maxY'])/$tileSpanY + $epsilon) ;
				$tileMaxRow = floor(($topLeftCornerY - $bboxSRS['minY'])/$tileSpanY - $epsilon) -1;
			}
			
			//Error control to avoid requesting empty tiles
			if($tileMinCol < 0){
				$tileMinCol = 0;
			}
			if($tileMaxCol < 0){
				return;
			}
			if($tileMinCol > $tileMaxCol){
				return;
			}
			if($tileMinCol >= $tileMatrixObj->matrixWidth){
				return;
			}
			if($tileMaxCol >= $tileMatrixObj->matrixWidth){
				$tileMaxCol = $tileMatrixObj->matrixWidth -1;
			}
			if($tileMinRow < 0){
				$tileMinRow = 0;
			}
			if($tileMaxRow < 0){
				return;
			}
			if($tileMinRow > $tileMaxRow){
				return;
			}
			if($tileMinRow >= $tileMatrixObj->matrixHeight){
				return;
			}
			if($tileMaxRow >= $tileMatrixObj->matrixHeight){
				$tileMaxRow = $tileMatrixObj->matrixHeight -1;
			}
			
			//Control if the BBOX filter and the TileMatrix extent intersect each other
			if($tileMatrixMaxX < $bboxSRS['minX']
					|| $tileMatrixMinY > $bboxSRS['maxY']
					|| $topLeftCornerY < $bboxSRS['minY']
					|| $topLeftCornerX > $bboxSRS['maxX']){
				//No intersection : none of the Tile is allowed
				$tileMatrixObj->minTileRow = null;
				$tileMatrixObj->maxTileRow = null;
				$tileMatrixObj->minTileCol = null;
				$tileMatrixObj->maxTileCol = null;
				$tileMatrixObj->anyTile = false;
				
			}
			else{
				//Intersection
				$tileMatrixObj->minTileRow = $tileMinCol;
				$tileMatrixObj->maxTileRow = $tileMaxCol;
				$tileMatrixObj->minTileCol = $tileMinRow;
				$tileMatrixObj->maxTileCol = $tileMaxRow;
				$tileMatrixObj->anyTile = false;
			}
		}
	}
}