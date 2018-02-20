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

require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * searchcriteria Table class
 */
class Easysdi_catalogTablesearchcriteria extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_searchcriteria', 'id', $db);
    }

    /**
      * Define a namespaced asset name for inclusion in the #__assets table
      * @return string The asset name 
      *
      * @see JTable::_getAssetName 
    */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_catalog.searchcriteria.' . (int) $this->$k;
    }
 
    /**
      * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
      *
      * @see JTable::_getAssetParentId 
    */
    protected function _getAssetParentId(JTable $table = null, $id = null){
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_catalog');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
    
     /**
     * Delete all the entries for the specified relation id
     * 
     * @param integer $relation_id  : relation identifier (fk on #__sdi_user)
     * 
     * @return boolean           True if successful. False if user not found or on error (internal error state set in that case).
     */
    public function deleteByRelationId($relation_id){
    	if(is_null($relation_id))
    		return false;
    	
    	//Select the user attribution
    	$query = $this->_db->getQuery(true);
    	$query->select('id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('relation_id') . ' = ' . (int) $relation_id);
    	 
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
    
    public function loadByRelationID($id = null)
    {
    	// Initialise the query.
    	$query = $this->_db->getQuery(true);
    	$query->select('id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('relation_id') . ' = ' . (int) $id);
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
