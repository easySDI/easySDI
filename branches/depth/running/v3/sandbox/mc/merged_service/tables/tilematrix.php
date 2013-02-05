<?php

/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';
/**
 * virtualmetadata Table class
 */
class Easysdi_serviceTabletilematrix extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_tilematrix', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		//$data['guid'] 						= $src['guid'];
		$data['identifier'] 				= $src['Identifier'];
		$data['scaledenominator'] 	= $src['ScaleDenominator'];
		$data['topleftcorner'] 			= $src['TopLeftCorner'];
		$data['tilewidth'] 					= $src['TileWidth'];
		$data['tileheight'] 				= $src['TileHeight'];
		$data['matrixwidth'] 				= $src['MatrixWidth'];
		$data['matrixheight'] 			= $src['MatrixHeight'];
		$data['tilematrixset_id'] 	= $src['tilematrixset_id'];
		return parent::save($data, $orderingFilter , $ignore );
	}
}
