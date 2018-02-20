<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once('PhysicalService.php');
require_once('WfsFeatureType.php');

class WfsPhysicalService extends PhysicalService{
	private $featureTypeList = Array();
	
	public function __construct ($id, $url, $user=null, $password=null) {
		parent::__construct($id, $url, $user, $password, 'WFS');
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

            //We have to use a DOM document instead of an XML ELement
            //to retrieve feature types description because XML Element
            //presents an issue to get elements with a prefixed namespace
            //(!= default namespace used without prefixe)
            $capabilities = dom_import_simplexml($this->xmlCapabilities);
            $featuretypelist = $capabilities->getElementsByTagName('FeatureType');
            for($i = 0; $i < $featuretypelist->length; $i++) {
                $name = $featuretypelist->item($i)->getElementsByTagName('Name');
                $title = $featuretypelist->item($i)->getElementsByTagName('Title');
                $this->addFeatureType(new WfsFeatureType($name->item(0)->nodeValue,$title->item(0)->nodeValue));
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