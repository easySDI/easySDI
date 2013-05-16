<?php
require_once('PhysicalService.php');

class CswPhysicalService extends PhysicalService{
	public function __construct ($id, $url) {
		parent::__construct($id, $url, 'CSW');
	}
	
	/**
	 * Populate the physical service with its stored capabilites
	 * 
	 */
	public function populate () {
		
	}
	
	public function sortLists () {}
	
	public function loadData ($data) {}
	
}