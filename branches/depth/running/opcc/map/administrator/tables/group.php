<?php

/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

/**
 * group Table class
 */
class Easysdi_mapTablegroup extends sdiTable {

	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__sdi_map_group', 'id', $db);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_easysdi_map.group.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->alias;
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID 1.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_easysdi_map');
		return $asset->id;
	}
	
	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 * set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   11.1
	 */
	public function load($keys = null, $reset = true)
	{
		if(!parent::load($keys,$reset))
			return false;
		
		$query = $this->_db->getQuery(true);
		$query->select('l.*, v.url as virtualserviceurl, p.resourceurl as physicalserviceurl, cv.value as virtualconnector, cp.value as physicalconnector, v.alias as virtualservicealias, p.alias as physicalservicealias');
		$query->from('#__sdi_map_layer AS l');
		$query->join('LEFT', '#__sdi_virtualservice AS v ON l.virtualservice_id=v.id');
		$query->join('LEFT', '#__sdi_physicalservice AS p ON l.physicalservice_id=p.id');
		$query->join('LEFT', '#__sdi_sys_serviceconnector AS cv ON v.serviceconnector_id=cv.id');
		$query->join('LEFT', '#__sdi_sys_serviceconnector AS cp ON p.serviceconnector_id=cp.id');
		$query->where('l.group_id = ' . (int) $this->id);
		$query->where('l.state = 1' );
		$query->order('l.ordering ASC' );
		$this->_db->setQuery($query);
		
		try
		{
			$rows = $this->_db->loadObjectList();
		}
		catch (JDatabaseException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		foreach ($rows as $row)
		{
			if(!empty($row->virtualserviceurl))
				$row->serviceurl = $row->virtualserviceurl;
			if(!empty($row->physicalserviceurl))
				$row->serviceurl = $row->physicalserviceurl;
			if(!empty($row->virtualconnector))
				$row->serviceconnector = $row->virtualconnector;
			if(!empty($row->physicalconnector))
				$row->serviceconnector = $row->physicalconnector;
			if(!empty($row->virtualservicealias))
				$row->servicealias = $row->virtualservicealias;
			if(!empty($row->physicalservicealias))
				$row->servicealias = $row->physicalservicealias;
		}
		
		$this->layers = $rows;
		return true;
	}
	
	/**
	 * Method to return the list of group ids used by the specified context id
	 *
	 * @param   integer    	$context_id   			A context identifier
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   EasySDI 3.0.0
	 */
	public function GetIdsByContextId($context_id = null, $reset = true)
	{
		if ($reset)
		{
			$this->reset();
		}
		
		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('g.id');
		$query->from($this->_tbl.'  AS g ');
		$query->join('LEFT', '#__sdi_map_context_group AS cg ON cg.group_id=g.id');
		$query->where('cg.context_id = ' . (int) $context_id);
		$query->where('g.state = 1' );
		$query->order('g.ordering ASC' );
		$this->_db->setQuery($query);
	
		try
		{
			$rows = $this->_db->loadResultArray();
	
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
			return false;
		}
		
		return $rows;
	}

}
