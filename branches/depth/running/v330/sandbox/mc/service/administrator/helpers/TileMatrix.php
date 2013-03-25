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
	
	public function toArray() {
		return get_object_vars($this);
	}
	
	/**
	 * Calculate the range of tiles allowed by the BBOX filter
	 * Algorithm given by OGC 07-057r2 document (page 9).
	 * @param unknown_type $theTileMatrixSet : Element of the policy document describing the TileMatrixSet
	 * @param unknown_type $tileMatrixIdentifier : identifier of the TileMatrix
	 * @param unknown_type $tileMatrix : Element of the GetCapabilities document describing the TileMatrix
	 * @param unknown_type $tileMatrixSetSupportedCRS : CRS of the TileMatrixSet
	 * @param unknown_type $listBBOXTileMatrixSetId : list of the BBOX filter defined in the submited form. Accessible by the TileMatrixSetId
	 * @param unknown_type $listTileMatrixSetCRSUnits : list of the CRS units get from the submited form. Accessible by the TileMatrixSetId
	 * @param unknown_type $tileMatrixSetIdentifier : identifier of the TileMatrixSet
	 * @param unknown_type $tileMatrixScaleDenominator : ScaleDenominator of the TileMatrix
	 */
	public function addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator){
		//Get TileMatrix informations
		$tileWidth = $tileMatrix->getElementsByTagName('TileWidth')->item(0)->nodeValue;
		$tileHeight = $tileMatrix->getElementsByTagName('TileHeight')->item(0)->nodeValue;
		$matrixWidth = $tileMatrix->getElementsByTagName('MatrixWidth')->item(0)->nodeValue;
		$matrixHeight = $tileMatrix->getElementsByTagName('MatrixHeight')->item(0)->nodeValue;

		//Get BBOX filter define for this layer
		$bboxCRS = $listBBOXTileMatrixSetId[$tileMatrixSetIdentifier];
		//Get the unit of the TileMatrixSet CRS
		$CRSunits = $listTileMatrixSetCRSUnits[$tileMatrixSetIdentifier];

		//Calculate the meterPerUnit parameter
		$meterPerUnit = 111319.49;
		if($CRSunits == "m" || $CRSunits == "metre")
			$meterPerUnit = 1;
		if($CRSunits == "grad")
			$meterPerUnit = 100187.54;
		if($CRSunits == "degree" || $CRSunits == "Degree")
			$meterPerUnit = 111319.49;
		if($CRSunits == "rad" || $CRSunits == "radian" || $CRSunits == "Radian")
			$meterPerUnit = 6378137;

		//Get the East and North coordinates of the top left corner of the TileMatrix.
		//
		//EPSG authority CRS definition (see : www.epsg-registry.org):
		//- Geographic CRS give the topLeftCorner as <TopLeftCorner>North East</TopLeftCorner>
		//- Projected CRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
		//OGC authority CRS defnition (see : OGC 07-057r7 document)
		//- all CRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
		//Others authorities are not supported.
		$topLeftCorner = $tileMatrix->getElementsByTagName('TopLeftCorner')->item(0)->nodeValue;
		if(!strpos($CRSunits,'m') && strpos($tileMatrixSetSupportedCRS,'EPSG')){
			$topLeftCornerY = substr($topLeftCorner, 0, strpos($topLeftCorner," "));
			$topLeftCornerX = substr($topLeftCorner, strpos($topLeftCorner," ")+1);
		}else{
			$topLeftCornerX = substr($topLeftCorner, 0, strpos($topLeftCorner," "));
			$topLeftCornerY = substr($topLeftCorner, strpos($topLeftCorner," ")+1);
		}

		//Calculate TileMatrix dimensions
		$pixelSpan = $tileMatrixScaleDenominator *0.00028 / $meterPerUnit;
		$tileSpanX = $tileWidth * $pixelSpan;
		$tileSpanY = $tileHeight * $pixelSpan;
		$tileMatrixMaxX = $topLeftCornerX + $tileSpanX * $matrixWidth;
		$tileMatrixMinY = $topLeftCornerY - $tileSpanY * $matrixHeight;
		$epsilon = 0.000001;

		//Calculate the range of tileset indexes included in the BBOX filter
		if($spatialoperator == "touch"){
			$tileMinCol = floor(($bboxCRS[minx] - $topLeftCornerX)/$tileSpanX + $epsilon);
			$tileMaxCol = floor(($bboxCRS[maxx] - $topLeftCornerX)/$tileSpanX - $epsilon);
			$tileMinRow = floor(($topLeftCornerY - $bboxCRS[maxy])/$tileSpanY + $epsilon);
			$tileMaxRow = floor(($topLeftCornerY - $bboxCRS[miny])/$tileSpanY - $epsilon);
		}else{
			$tileMinCol = ceil(($bboxCRS[minx] - $topLeftCornerX)/$tileSpanX + $epsilon);
			$tileMaxCol = floor(($bboxCRS[maxx] - $topLeftCornerX)/$tileSpanX - $epsilon) -1;
			$tileMinRow = ceil(($topLeftCornerY - $bboxCRS[maxy])/$tileSpanY + $epsilon) ;
			$tileMaxRow = floor(($topLeftCornerY - $bboxCRS[miny])/$tileSpanY - $epsilon) -1;
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
		if($tileMinCol >= $matrixWidth){
			return;
		}
		if($tileMaxCol >= $matrixWidth){
			$tileMaxCol = $matrixWidth -1;
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
		if($tileMinRow >= $matrixHeight){
			return;
		}
		if($tileMaxRow >= $matrixHeight){
			$tileMaxRow = $matrixHeight -1;
		}

		//Control if the BBOX filter and the TileMatrix extent intersect each other
		if($tileMatrixMaxX < $bboxCRS[minx]
				|| $tileMatrixMinY > $bboxCRS[maxy]
				|| $topLeftCornerY < $bboxCRS[miny]
				|| $topLeftCornerX > $bboxCRS[maxx]){
			//No intersection : none of the Tile is allowed
			//$theTileMatrix['none'] = 'true';
		}
		else{
			$theTileMatrix = $theTileMatrixSet->addChild(TileMatrix);
			$theTileMatrix['id']= $tileMatrixIdentifier;
			$theTileMatrix['All']= 'false';
			//Intersection
			$theTileMatrix->TileMinCol = $tileMinCol;
			$theTileMatrix->TileMaxCol = $tileMaxCol;
			$theTileMatrix->TileMinRow = $tileMinRow;
			$theTileMatrix->TileMaxRow = $tileMaxRow;
		}
	}
}