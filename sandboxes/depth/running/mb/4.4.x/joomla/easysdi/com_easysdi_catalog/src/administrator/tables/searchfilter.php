<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * attribute Table class
 */
class Easysdi_catalogTablesearchfilter extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_searchcriteriafilter', 'id', $db);
    }

    /**
     * Method to save searchsort field value.
     *
     * @param   mixed   $src             An associative array or object to bind to the JTable instance.
     * @param   string  $orderingFilter  Filter for the order updating
     * @param   mixed   $ignore          An optional array or space separated list of properties
     *                                   to ignore while binding.
     *
     * @return  boolean  True on success.
     *
     * @link	http://docs.joomla.org/JTable/save
     * @since   11.1
     */
    public function saveAll($src, $orderingFilter = '', $ignore = '') {

        if (isset($src['searchfilter']) && is_array($src['searchfilter'])) {
            $searchsort = $src['searchfilter'];
            foreach ($searchsort as $key => $value) {
                $language_id = $key;
                $keys = array();
                $keys ['searchcriteria_id'] = $src['searchcriteria_id'];
                $keys ['language_id'] = $language_id;
                if ($this->load($keys, false)) {
                    //Update
                    $this->ogcsearchfilter = $value;
                } else {
                    //Create
                    $this->searchcriteria_id = $src['searchcriteria_id'];
                    $this->language_id = $language_id;
                    $this->ogcsearchfilter = $value;
                }

                if (!$this->store()) {
                    $this->setError('Can t store');
                    return false;
                }
                //$this->reset();
                $this->id = null;
            }
        }

        return true;
    }

    /**
     * Method to load all row from the database by searchcriteria id key 
     *
     * @param   string   $id   The searchcriteria id
     * @param   boolean  $reset  True to reset the default values before loading the new row.
     *
     * @return  array  searcgsort fields for all languages. False if no row found.
     *
     * @throws  RuntimeException
     * @throws  UnexpectedValueException
     */
    public function loadAll($id = null) {
        if (empty($id)) {
            return false;
        }
        // Initialise the query.
        $query = $this->_db->getQuery(true)
                ->select('*')
                ->from($this->_tbl);
        $query->where($this->_db->quoteName('searchcriteria_id') . ' = ' . $this->_db->quote($id));

        $this->_db->setQuery($query);

        $rows = $this->_db->loadAssocList();

        // Check that we have a result.
        if (!is_array($rows)) {
            return false;
        }

        $result = array();
        // Bind the object with the row and return.
        foreach ($rows as $row) {
            $result['searchfilter'] [$row['language_id']] = $row['ogcsearchfilter'];
        }

        return $result;
    }

    /**
     * Delete all serachsort entries for a given searchcriteria id
     *
     * @param   string  $element_guid  Element guid.
     *
     * @return  boolean  True on success.
     *
     * @throws  UnexpectedValueException
     */
    public function deleteAll($id = null) {

        // If no primary key is given, return false.
        if ($id === null) {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }

        // Delete the row by primary key.
        $query = $this->_db->getQuery(true)
                ->delete($this->_tbl)
                ->where('searchcriteria_id = ' . $this->_db->quote($id));
        $this->_db->setQuery($query);

        // Check for a database error.
        $this->_db->execute();

        return true;
    }
    
    
}
