<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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