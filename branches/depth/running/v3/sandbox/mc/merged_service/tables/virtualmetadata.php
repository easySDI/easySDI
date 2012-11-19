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
class Easysdi_serviceTablevirtualmetadata extends sdiTable {

	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_virtualmetadata', 'id', $db);
	}
	
	public function save($src, $orderingFilter = '', $ignore = '') {
		$data = array();
		$data['id'] = $src['id'];
		$data['guid'] = $src['guid'];
		$data['title'] = $src['title'];
		$data['summary'] = $src['abstract'];
		$data['keyword'] = $src['keyword'];
		$data['contactOrganization'] = $src['contactOrganization'];
		$data['contactName'] = $src['contactName'];
		$data['contactPosition'] = $src['contactPosition'];
		$data['contactAdress'] = $src['contactAdress'];
		$data['contactPostalCode'] = $src['contactPostalCode'];
		$data['contactLocality'] = $src['contactLocality'];
		$data['contactState'] = $src['contactState'];
		$data['contactCountry'] = $src['contactCountry'];
		$data['contactPhone'] = $src['contactPhone'];
		$data['contactFax'] = $src['contactFax'];
		$data['contactEmail'] = $src['contactEmail'];
		$data['contactURL'] = $src['contactURL'];
		$data['contactAvailability'] = $src['contactAv0ailability'];
		$data['contactInstruction'] = $src['contactInstruction'];
		$data['fee'] = $src['fee'];
		$data['accessConstraint'] = $src['accessConstraint'];
		
		return parent::save($data, $orderingFilter , $ignore );
	}
}
