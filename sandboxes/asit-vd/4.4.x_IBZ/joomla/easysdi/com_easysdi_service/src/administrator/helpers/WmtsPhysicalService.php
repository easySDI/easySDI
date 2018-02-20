<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once('PhysicalService.php');
require_once('WmtsLayer.php');

class WmtsPhysicalService extends PhysicalService{
	private $layerList = Array();
	private $SRSList = Array();
	
	public function __construct ($id, $url, $user=null, $password=null) {
		parent::__construct($id, $url, $user, $password, 'WMTS');
	}
	
	public function getLayerList () {
		return $this->layerList;
	}
	
	public function getSRSList () {
		return $this->SRSList;
	}
	
	public function getLayerByName ($name) {
		return $this->layerList[$name];
	}
	
	public function addLayer ($layer) {
		$this->layerList[$layer->name] = $layer;
	}
	
	/**
	 * Populate the physical service with its stored capabilites
	 * 
	 */
	public function populate () {
		if ('SimpleXMLElement' != get_class($this->xmlCapabilities)) {
			return;
		}
		
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
							'scaleDenominator' => (String) $tileMatrix->ScaleDenominator,
							'topLeftCorner' => (String) $tileMatrix->TopLeftCorner,
							'tileWidth' => (String) $tileMatrix->TileWidth,
							'tileHeight' => (String) $tileMatrix->TileHeight,
							'matrixWidth' => (String) $tileMatrix->MatrixWidth,
							'matrixHeight' => (String) $tileMatrix->MatrixHeight,
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
                        if (isset($this->layerList[$key])) {
                                $this->layerList[$key]->loadData($value);
                        }
		}
	}
	
	public function setLayerAsConfigured ($layerList) {
		foreach ($layerList as $layerIdentifier) {
			$this->getLayerByName($layerIdentifier)->setHasConfig(true);
		}
	}
	
	/**
	 * Populate SRSList with all srs found in the tilematrixes of this physical service
	 * 
	 */
	public function compileAllSRS () {
		foreach ($this->layerList as $layerIdentifier => $layer) {
			foreach ($layer->getTileMatrixSetList() as $tmsIdentifier => $tms) {
				$this->SRSList[] = $tms->srs;
			}
		}
		$this->SRSList = array_unique($this->SRSList);
	}
	
	/**
	 * Set the bounding box of all the layers
	 * 
	 * @param array $bbox	ascociative array with keys north-east-south-west
	 * 
	 * @param boolean $override	when true, ignore if value are already set in the layer
	 * 
	 */
	public function setAllBoundingBoxes ($bbox, $override = false) {
		foreach ($this->layerList as $layerIdentifier => $layer) {
			if (true === $override || (!isset($layer->northBoundLatitude) && !isset($layer->eastBoundLongitude) && !isset($layer->southBoundLatitude) && !isset($layer->westBoundLongitude))) {
				$layer->northBoundLatitude = $bbox['north'];
				$layer->eastBoundLongitude = $bbox['east'];
				$layer->southBoundLatitude = $bbox['south'];
				$layer->westBoundLongitude = $bbox['west'];
			}
		}
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
		foreach ($this->layerList as $layerIdentifier => $layer) {
			$layer->setAllSRSUnit($arr_unit);
		}
	}
	
	public function calculateAuthorizedTiles () {
		foreach ($this->layerList as $layerIdentifier => $layer) {
			$layer->calculateAuthorizedTiles();
			break;
		}
	}
	
}