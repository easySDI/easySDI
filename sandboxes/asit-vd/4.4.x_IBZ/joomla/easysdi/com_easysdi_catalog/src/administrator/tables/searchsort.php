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
class Easysdi_catalogTablesearchsort extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_catalog_searchsort', 'id', $db);
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
       
        if (isset($src['searchsort']) && is_array($src['searchsort'])) {
            $searchsort = $src['searchsort'];
            foreach($searchsort as $key => $value) {
                $language_id = $key;
                $catalog_id = $src['id'];
                $keys = array ();
                $keys ['catalog_id'] = $catalog_id;
                $keys ['language_id'] = $language_id;
                if($this->load($keys, false)){
                    //Update
                    $this->ogcsearchsorting = $value;
                }else{
                    //Create
                    $this->catalog_id = $catalog_id;
                    $this->language_id = $language_id;
                    $this->ogcsearchsorting = $value;
                    $this->state = 1;
                }
                
                if (!$this->store())
		{
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
       * Method to load all row from the database by catalog id key 
	 *
	 * @param   string   $id   The catalog id that searchsort fields are required
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  array  searcgsort fields for all languages. False if no row found.
	 *
	  * @throws  RuntimeException
	 * @throws  UnexpectedValueException
	 */
	public function loadAll($id = null)
	{
		if (empty($id))
		{
			return false;
		}
		
		// Initialise the query.
		$query = $this->_db->getQuery(true)
			->select('*')
			->from($this->_tbl);
		$query->where($this->_db->quoteName('catalog_id') . ' = ' . $this->_db->quote($id));
		
		$this->_db->setQuery($query);

		$rows = $this->_db->loadAssocList();

		// Check that we have a result.
		if (!is_array($rows))
		{
			return false;
		}

                $result = array();
		// Bind the object with the row and return.
                foreach ($rows as $row){
                   $result['searchsort'] [$row['language_id']] = $row['ogcsearchsorting'];
                }
                
                return $result;
	}
        
        /**
	 * Delete all serachsort entries for a given catalog id
	 *
	 * @param   string  $element_guid  Element guid.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  UnexpectedValueException
	 */
	public function deleteAll($id = null)
	{
		
		// If no primary key is given, return false.
		if ($id === null)
		{
			throw new UnexpectedValueException('Null primary key not allowed.');
		}

		

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true)
			->delete($this->_tbl)
			->where('catalog_id = ' . $this->_db->quote($id));
		$this->_db->setQuery($query);

		// Check for a database error.
		$this->_db->execute();

		return true;
	}
        

}
