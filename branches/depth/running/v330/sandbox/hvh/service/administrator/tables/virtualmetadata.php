<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
		$data['guid'] 										= $src['guid'];
		$data['title'] 										= $src['title'];
		$data['summary'] 								= $src['summary'];
		$data['keyword'] 								= $src['keyword'];
		$data['contactOrganization'] 	= $src['contactOrganization'];
		$data['contactName'] 						= $src['contactName'];
		$data['contactPosition'] 				= $src['contactPosition'];
		$data['contactAdress'] 					= $src['contactAdress'];
		$data['contactPostalCode'] 			= $src['contactPostalCode'];
		$data['contactLocality'] 				= $src['contactLocality'];
		$data['contactState'] 					= $src['contactState'];
		$data['contactCountry'] 				= $src['contactCountry'];
		$data['contactPhone'] 					= $src['contactPhone'];
		$data['contactFax'] 							= $src['contactFax'];
		$data['contactEmail'] 					= $src['contactEmail'];
		$data['contactURL'] 							= $src['contactURL'];
		$data['contactAvailability'] 	= $src['contactAvailability'];
		$data['contactInstruction'] 		= $src['contactInstruction'];
		$data['fee'] 											= $src['fee'];
		$data['accessConstraint'] 			= $src['accessConstraint'];
		$data['virtualservice_id'] 			= $src['id'];
		
		return parent::save($data, $orderingFilter , $ignore );
	}
	
	public function loadByVirtualServiceID($virtualservice_id = null, $reset = true) {
		if ($reset) {
			$this->reset();
		}

		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($this->_tbl);
		$query->where($this->_db->quoteName('virtualservice_id') . ' = ' . (int) $virtualservice_id);

		$this->_db->setQuery($query);

		try {
			$row = $this->_db->loadAssoc();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}

		// Legacy error handling switch based on the JError::$legacy switch.
		// @deprecated  12.1
		if (JError::$legacy && $this->_db->getErrorNum())	{
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}

		// Check that we have a result.
		if (empty($row)) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
			$this->setError($e);
			return false;
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}
}
