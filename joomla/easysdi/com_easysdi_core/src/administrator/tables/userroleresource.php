<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * resource Table class
 */
class Easysdi_coreTableuserroleresource extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_user_role_resource', 'id', $db);
    }

    /**
     * Delete all the entries for the specified resource id
     * 
     * @param integer $id  : resource identifier 
     * 
     * @return boolean           True if successful. 
     */
    public function deleteByResourceId($id){
    	if(is_null($id))
    		return false;
    	
    	//Select 
    	$query = $this->_db->getQuery(true);
    	$query->select('id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('resource_id') . ' = ' . (int) $id);
    	 
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

}
