<?php
require_once('Layer.php');

class WfsFeatureType extends Layer{
	public $remoteFilterGML;
	public $localFilterGML;
	public $enabled;
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('WfsFeatureType', $key)) {
				$this->{$key} = $value;
			}
		}
	}
}