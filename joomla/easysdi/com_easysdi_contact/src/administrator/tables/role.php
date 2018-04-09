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

/**
 * address Table class
 */
class Easysdi_contactTablerole extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_user_role_organism', 'id', $db);
    }

    /**
     * Method to load a row from the database by user id and address type, and bind the fields
     * to the JTable instance properties.
     *
     * @param   integer    	$user_id   			User identifier
     *
     * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
     *
     * @link    http://docs.joomla.org/JTable/load
     * @since   EasySDI 3.0.0
     */
    public function loadByUserID($user_id = null, $type = null, $reset = true) {
        return $this->loadBy('user_id','organism_id', $user_id, $type, $reset);
    }

    /**
     * Method to load a row from the database by organism id and address type, and bind the fields
     * to the JTable instance properties.
     *
     * @param   integer    	$user_id   			User identifier
     *
     * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
     *
     * @link    http://docs.joomla.org/JTable/load
     * @since   EasySDI 3.0.0
     */
    public function loadByOrganismID($organism_id = null, $type= null,$reset = true) {
        return $this->loadBy('organism_id','user_id', $organism_id, $type, $reset);
    }

    /**
     * Generic method to load a row from the database by user or organism.
     * This method is called by Easysdi_contactTablerole::loadByUserID() and Easysdi_contactTablerole::loadByOrganismID()
     * @param unknown_type $by : field name giving the type of the object where the role belonged (value is 'user_id' or 'organism_id')
     * @param unknown_type $id : identifier of the object where the role belonged
     * @param unknown_type $reset : reset
     */
    private function loadBy($by, $return, $id = null, $type = null) {
        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select($this->_db->quoteName($return));
        $query->from($this->_tbl);
        $query->where($this->_db->quoteName($by) . ' = ' . (int) $id);
        if (!is_null($type)) {
            $query->where($this->_db->quoteName('role_id') . ' = ' . (int) $type);
        }
        $this->_db->setQuery($query);

        try {
            $rows = $this->_db->loadColumn();
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
        if (empty($rows)) {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        // Bind the object with the row and return.
        return $rows;
    }
    

    /**
     * Delete all the role attribution for the specified user
     * 
     * @param integer $user_id  : user identifier (fk on #__sdi_user)
     * 
     * @return boolean           True if successful. False if user not found or on error (internal error state set in that case).
     */
    public function deleteByUserId($user_id) {
        if (is_null($user_id))
            return false;

        //Select the user attribution
        $query = $this->_db->getQuery(true);
        $query->select('id');
        $query->from($this->_tbl);
        $query->where($this->_db->quoteName('user_id') . ' = ' . (int) $user_id);

        $this->_db->setQuery($query);

        try {
            $ids = $this->_db->loadAssocList();
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

        //Delete the user attribution
        foreach ($ids as $id) {
            try {
                $this->delete($id['id']);
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
        return true;
    }
}
