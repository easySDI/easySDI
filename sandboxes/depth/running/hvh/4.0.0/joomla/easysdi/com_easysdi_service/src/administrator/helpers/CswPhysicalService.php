<?php
require_once('PhysicalService.php');

class CswPhysicalService extends PhysicalService{
	public function __construct ($id, $url, $user=null, $password=null) {
		parent::__construct($id, $url, $user, $password,'CSW');
	}
	
	public function populate () {}
	public function sortLists () {}
	public function loadData ($data) {}
	public function setLayerAsConfigured ($layerList) {}
	
}