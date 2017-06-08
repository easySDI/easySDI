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
 * importref Table class
 */
class Easysdi_catalogTablecatalogresourcetype extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_catalog_resourcetype', 'id', $db);
    }

    
     /**
     * Delete all the entries for the specified catalog id
     * 
     * @param integer $id  : catalog identifier 
     * 
     * @return boolean           True if successful. 
     */
    public function deleteByCatalogId($id){
    	if(is_null($id))
    		return false;
    	
    	//Select 
    	$query = $this->_db->getQuery(true);
    	$query->select('id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('catalog_id') . ' = ' . (int) $id);
    	 
    	$this->_db->setQuery($query);
    	
    	try
    	{
    		$ids = $this->_db->loadAssocList();
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
    	
    	//Delete the user attribution
    	foreach ($ids as $id){
    		try{
    			$this->delete($id['id']);
    		}
    		catch  (JDatabaseException $e)
    		{
    			$je = new JException($e->getMessage());
    			$this->setError($je);
    			return false;
    		}
    	}
    	return true;
    }

    public function loadByCatalogID($id = null)
    {
    	// Initialise the query.
    	$query = $this->_db->getQuery(true);
    	$query->select('resourcetype_id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('catalog_id') . ' = ' . (int) $id);
    	$this->_db->setQuery($query);
    
    	try
    	{
    		$rows = $this->_db->loadColumn();
    		
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
    	if (empty($rows))
    	{
    		$e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
    		$this->setError($e);
    		return false;
    	}
    	
    	// Bind the object with the row and return.
    	return $rows;
    }
    
    

}
