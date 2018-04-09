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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * attribute Table class
 */
class Easysdi_catalogTablecatalogsearchcriteria extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_catalog_searchcriteria', 'id', $db);
    }

    
    public function loadBySearchCriteriaID($id = null)
    {
    	// Initialise the query.
    	$query = $this->_db->getQuery(true);
    	$query->select('catalog_id');
    	$query->from($this->_tbl);
    	$query->where($this->_db->quoteName('searchcriteria_id') . ' = ' . (int) $id);
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
