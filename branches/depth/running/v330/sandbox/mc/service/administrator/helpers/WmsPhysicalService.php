<?php
require_once('PhysicalService.php');
require_once('WmsLayer.php');

class WmsPhysicalService extends PhysicalService{
	private $layerList = Array();
	
	public function __construct ($id, $url) {
		parent::__construct($id, $url, 'WMS');
	}
	
	public function getLayerList () {
		return $this->layerList;
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
		$version = $this->xmlCapabilities->xpath('@version');
		$version = (string)$version[0];
		$xpathQuery = 'Capability//Layer[Name]';
		
		if ('1.3.0' == $version) {
			$xpathQuery = '//dflt:Layer';
		}
		
		$wmsLayerList = $this->xmlCapabilities->xpath($xpathQuery);
		
		//inserting each wmslayer
		foreach ($wmsLayerList as $wmsLayer) {
			$this->addLayer(new WmsLayer((String) $wmsLayer->Name, (String) $wmsLayer->Title));
		}
	}
	
	/**
	 * Recursively sorts lists in the physical service
	 * 
	 */
	public function sortLists () {
		uasort($this->layerList, array("WmsLayer", "compareNames"));
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			$this->layerList[$key]->loadData($value);
		}
	}
}