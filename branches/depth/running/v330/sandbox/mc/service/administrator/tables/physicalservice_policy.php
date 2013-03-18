<?php

// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

/**
 * policy Table class
 */
class Easysdi_serviceTablephysicalservice_policy extends sdiTable {
	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_policy', 'id', $db);
	}
	
	public function save ($data) {
		var_dump($data);
		//die();
	}
}
