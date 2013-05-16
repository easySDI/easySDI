<?php
require_once('PhysicalService.php');
require_once('WmtsLayer.php');

class WmtsPhysicalService extends PhysicalService{
	private $layerList = Array();
	
	public function __construct ($id, $url) {
		parent::__construct($id, $url, 'WMTS');
	}
	
	public function getLayerList () {
		return $this->layerList;
	}
	
	public function addLayer ($layer) {
		$this->layerList[$layer->name] = $layer;
	}
	
	/**
	 * Populate the physical service with its stored capabilites
	 * 
	 */
	public function populate () {
		$wmtsLayerList = $this->xmlCapabilities->xpath('/dflt:Capabilities/dflt:Contents/dflt:Layer');
		
		//inserting each wmtslayer
		foreach ($wmtsLayerList as $wmtsLayer) {
			$objWmtsLayer = new WmtsLayer(
				(String) $wmtsLayer->children('ows', true)->Identifier,
				(String) $wmtsLayer->children('ows', true)->Title
			);
			
			foreach ($wmtsLayer->TileMatrixSetLink as $tileMatrixSet) {
				$tileMatrixSetIdentifier = (String) $tileMatrixSet->TileMatrixSet;
				
				//we save the tilematrixset
				$supported_SRS = $this->xmlCapabilities->xpath("/dflt:Capabilities/dflt:Contents/dflt:TileMatrixSet[ows:Identifier = '" . $tileMatrixSetIdentifier . "']");
				$supported_SRS = (String) $supported_SRS[0]->children('ows', true)->SupportedCRS;
				$objTileMatrixSet = new TileMatrixSet(
					$tileMatrixSetIdentifier,
					$supported_SRS
				);
				
				//we probe if the tilematrixset has limits
				$hasLimits = isset($tileMatrixSet->TileMatrixSetLimits);
				
				//we get the list of authorized tilematrix for this tilematrixset
				if ($hasLimits) {
					$authorized_tiles = Array();
					foreach ($tileMatrixSet->TileMatrixSetLimits->TileMatrixLimits as $limits) {
						$tileMatrixIdentifier = (String) $limits->TileMatrix;
						$authorized_tiles[] = $tileMatrixIdentifier;
					}
				}
				
				//we get the list of all the tilematrix for this tilematrixset
				$tileMatrixList = $this->xmlCapabilities->xpath("/dflt:Capabilities/dflt:Contents/dflt:TileMatrixSet[ows:Identifier = '" . $tileMatrixSetIdentifier . "']/dflt:TileMatrix");
				
				foreach ($tileMatrixList as $tileMatrix) {
					$identifier = (String) $tileMatrix->children('ows', true)->Identifier;
					//if there are limits on the tilematrixset we filter the list of tilematrix with authorized tilematrixes and we save
					if (!$hasLimits || in_array($identifier, $authorized_tiles)) {
						$objTileMatrix = new TileMatrix($identifier, Array(
							'scaledenominator' => (String) $tileMatrix->ScaleDenominator,
							'topleftcorner' => (String) $tileMatrix->TopLeftCorner,
							'tilewidth' => (String) $tileMatrix->TileWidth,
							'tileheight' => (String) $tileMatrix->TileHeight,
							'matrixwidth' => (String) $tileMatrix->MatrixWidth,
							'matrixheight' => (String) $tileMatrix->MatrixHeight,
						));
						$objTileMatrixSet->addTileMatrix($objTileMatrix);
					}
				}
				$objWmtsLayer->addTileMatrixSet($objTileMatrixSet);
			}
			$this->addLayer($objWmtsLayer);
		}
	}
	
	/**
	 * Recursively sorts lists in the physical service
	 * 
	 */
	public function sortLists () {
		foreach ($this->layerList as $layer) {
			$layer->sortLists();
		}
		uasort($this->layerList, array("WmtsLayer", "compareNames"));
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			$this->layerList[$key]->loadData($value);
		}
	}
}