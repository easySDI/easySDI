<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * address Table class
 */
class Easysdi_contactTableaddress extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
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
    public function loadByUserID($user_id = null, $addresstype_id = null, $reset = true) {
        return $this->loadBy('user_id', $user_id, $addresstype_id, $reset);
    }

    /**
     * Method to load a row from the database by organism id and address type, and bind the fields
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
    public function loadByOrganismID($organism_id = null, $addresstype_id = null, $reset = true) {
        return $this->loadBy('organism_id', $organism_id, $addresstype_id, $reset);
    }

    /**
     * Generic method to load a row from the database by user or organism.
     * This method is called by Easysdi_contactTableaddress::loadByUserID() and Easysdi_contactTableaddress::loadByOrganismID()
     * @param unknown_type $by : field name giving the type of object where the address belonged (value is 'user_id' or 'organism_id')
     * @param unknown_type $id : identifier of the object where the address belonged
     * @param unknown_type $addresstype_id : Address type identifier
     * @param unknown_type $reset : reset
     */
    private function loadBy($by, $id = null, $addresstype_id = null) {
        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from($this->_tbl);
        $query->where($this->_db->quoteName($by) . ' = ' . (int) $id);
        $query->where($this->_db->quoteName('addresstype_id') . ' = ' . (int) $addresstype_id);

        $this->_db->setQuery($query);

        try {
            $row = $this->_db->loadAssoc();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        // Legacy error handling switch based on the JError::$legacy switch.
        // @deprecated  12.1
        if (JError::$legacy && $this->_db->getErrorNum()) {
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
    public function saveByType($src, $type, $orderingFilter = '', $ignore = '') {
        $data = array();
        $data['id'] = $src[$type . '_id'];
        $data['guid'] = $src[$type . '_guid'];
        $data['addresstype_id'] = $src[$type . '_addresstype_id'];
        $data['user_id'] = $src['user_id'];
        $data['organism_id'] = $src['organism_id'];
        $data['civility'] = $src[$type . '_civility'];
        $data['firstname'] = $src[$type . '_firstname'];
        $data['lastname'] = $src[$type . '_lastname'];
        $data['function'] = $src[$type . '_function'];
        $data['address'] = $src[$type . '_address'];
        $data['addresscomplement'] = $src[$type . '_addresscomplement'];
        $data['postalcode'] = $src[$type . '_postalcode'];
        $data['postalbox'] = $src[$type . '_postalbox'];
        $data['locality'] = $src[$type . '_locality'];
        $data['country_id'] = ((int)$src[$type . '_country_id'] == 0) ? null : $src[$type . '_country_id'];
        $data['phone'] = $src[$type . '_phone'];
        $data['mobile'] = $src[$type . '_mobile'];
        $data['fax'] = $src[$type . '_fax'];
        $data['email'] = $src[$type . '_email'];
        $data['sameascontact'] = isset($src[$type . '_sameascontact']) ? $src[$type . '_sameascontact'] : 1 ;
        $data['state'] = 1;

        return parent::save($data, $orderingFilter, $ignore);
    }
    
    

}
