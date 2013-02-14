<?php
/**
 * @version     3.0.0
  * @package     com_easysdi_user
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

/**
 * address Table class
 */
class Easysdi_userTableaddress extends sdiTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__sdi_address', 'id', $db);
	}

    
    /**
     * Method to load a row from the database by user id and address type, and bind the fields
     * to the JTable instance properties.
     *
     * @param   integer    	$user_id   			User identifier
     * @param   integer  	$addresstype_id  	Address type identifier
     *
     * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
     *
     * @link    http://docs.joomla.org/JTable/load
     * @since   EasySDI 3.0.0
     */
    public function loadByUserID($user_id = null,$addresstype_id=null, $reset = true)
    {
    	if ($reset)
    	{
    		$this->reset();
    	}
    
    	// Initialise the query.
    	$query = $this->_db->getQuery(true);
    	$query->select('*');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('user_id') . ' = ' . (int) $user_id);
    	$query->where($this->_db->quoteName('addresstype_id') . ' = ' . (int) $addresstype_id);
    	
    	$this->_db->setQuery($query);
    
    	try
    	{
    		$row = $this->_db->loadAssoc();
    		
    	}
    	catch (JDatabaseException $e)
    	{
    		$je = new JException($e->getMessage());
    		$this->setError($je);
    		return false;
    	}
    
    	// Legacy error handling switch based on the JError::$legacy switch.
    	// @deprecated  12.1
    	if (JError::$legacy && $this->_db->getErrorNum())
    	{
    		$e = new JException($this->_db->getErrorMsg());
    		$this->setError($e);
    		return false;
    	}
    
    	// Check that we have a result.
    	if (empty($row))
    	{
    		$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
    		$this->setError($e);
    		return false;
    	}
    	
    	// Bind the object with the row and return.
    	return $this->bind($row);
    }

    /**
     * Overloaded save function to bind the posted data to the table fields
     *
     * @param   mixed    	$src   			posted data
     * @param   string  	$type  			Address type alias ('billing', 'contact' or 'delivry')
     * @param 	mixed		$orderingFilter	Param for the parent 'save' funtion 
     * @param 	mixed		$ignore			Param for the parent 'save' funtion
     *
     * @return  boolean  True if successful. False if on error (internal error state set in that case).
     *
     * @link    http://docs.joomla.org/JTable/save
     * @since   EasySDI 3.0.0
     */
    public function saveByType($src,$type, $orderingFilter = '', $ignore = '')
    {
    	$data =array();
    	$data['id'] =$src[$type.'_id'];
    	$data['guid'] =$src[$type.'_guid'];
    	$data['addresstype_id'] =$src[$type.'_addresstype_id'];
    	$data['user_id'] =$src['id'];
    	$data['organismcomplement'] =$src[$type.'_organismcomplement'];
    	$data['organism'] =$src[$type.'_organism'];
    	If($src[$type.'_civility'] == 0)
    		$data['civility'] = null;
    	else
    		$data['civility'] = $src[$type.'_civility'];
    	$data['firstname'] =$src[$type.'_firstname'];
    	$data['lastname'] =$src[$type.'_lastname'];
    	$data['function'] =$src[$type.'_function'];
    	$data['address'] =$src[$type.'_address'];
    	$data['addresscomplement'] =$src[$type.'_addresscomplement'];
    	$data['postalcode'] =$src[$type.'_postalcode'];
    	$data['postalbox'] =$src[$type.'_postalbox'];
    	$data['locality'] =$src[$type.'_locality'];
    	$data['country'] =$src[$type.'_country'];
    	$data['phone'] =$src[$type.'_phone'];
    	$data['mobile'] =$src[$type.'_mobile'];
    	$data['fax'] =$src[$type.'_fax'];
    	$data['email'] =$src[$type.'_email'];
    	$data['sameascontact'] =$src[$type.'_sameascontact'];
    	
    	 
    	return parent::save($data, $orderingFilter , $ignore );
    }
}
