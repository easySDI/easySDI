<?php
require_once('PhysicalService.php');

class CswPhysicalService extends PhysicalService{
	public function __construct ($id, $url) {
		parent::__construct($id, $url, 'CSW');
	}
	
	public function populate () {}
	public function sortLists () {}
	public function loadData ($data) {}
	public function setLayerAsConfigured ($layerList) {}
	
}