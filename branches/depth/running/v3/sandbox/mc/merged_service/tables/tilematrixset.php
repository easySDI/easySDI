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
class Easysdi_serviceTabletilematrixset extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_tilematrixset', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		//$data['guid'] 							= $src['guid'];
		$data['identifier'] 						= $src['identifier'];
		$data['supported_crs'] 				= $src['supported_crs'];
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	/**
	 * Delete all occurences of feature class based on their physicalservice_id
	 * 
	 * @param Int A physical service ID
	*/
	function wipeByPhysicalId($physicalservice_id) {
		$db = JFactory::getDbo();
		$db->setQuery('
			DELETE FROM #__sdi_tilematrixset
			WHERE physicalservice_id = ' . $physicalservice_id . ';
		');
		$db->execute();
	}
}
