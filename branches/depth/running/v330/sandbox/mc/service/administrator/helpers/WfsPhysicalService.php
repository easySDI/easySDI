<?php
require_once('PhysicalService.php');
require_once('WfsFeatureType.php');

class WfsPhysicalService extends PhysicalService{
	private $featureTypeList = Array();
	
	public function __construct ($id, $url) {
		parent::__construct($id, $url, 'WFS');
	}
	
	public function getLayerList () {
		return $this->featureTypeList;
	}
	
	public function getLayerByName ($name) {
		return $this->featureTypeList[$name];
	}
	
	public function addFeatureType ($featureType) {
		$this->featureTypeList[$featureType->name] = $featureType;
	}
	
	/**
	 * Populate the physical service with its stored capabilites
	 * 
	 */
	public function populate () {
		if ('SimpleXMLElement' != get_class($this->xmlCapabilities)) {
			return;
		}
		
		$featureTypeList = $this->xmlCapabilities->xpath('//dflt:FeatureType');
		
		//inserting each featureClass
		foreach ($featureTypeList as $featureType) {
			$this->addFeatureType(new WfsFeatureType((String) $featureType->Name, (String) $featureType->Title));
		}
	}
	
	/**
	 * Recursively sorts lists in the physical service
	 * 
	 */
	public function sortLists () {
		uasort($this->featureTypeList, array("WfsFeatureType", "compareNames"));
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			$this->featureTypeList[$key]->loadData($value);
		}
	}
	
	public function setLayerAsConfigured ($layerList) {
		foreach ($layerList as $layerIdentifier) {
			$this->getLayerByName($layerIdentifier)->setHasConfig(true);
		}
	}
	
}