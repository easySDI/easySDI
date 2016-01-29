<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

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