<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * virtualservice Table class
 */
class Easysdi_serviceTablevirtualservice extends sdiTable {
	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_virtualservice', 'id', $db);
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
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_easysdi_service.virtualservice.' . (int) $this->$k;
	}

	/**
	* Method to return the title to use for the asset table.
	*
	* @return  string
	*
	* @since   11.1
	*/
	protected function _getAssetTitle() {
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
	protected function _getAssetParentId(JTable $table = null, $id = null) {
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();
		// The item has the component as asset-parent
		$assetParent->loadByName('com_easysdi_service');
		// Return the found asset-parent-id
		if ($assetParent->id){
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
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
	public function loadByAlias($alias)
	{
		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from($query->quoteName($this->_tbl));
		$query->where($this->_db->quoteName('alias') . ' = ' .  $query->quote($alias));
		 
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
	 * Method to return the list of services ids used by the specified context id
	 *
	 * @param   integer    	$context_id   			A context identifier
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   EasySDI 3.0.0
	 */
	public function loadIdsByMapId($map_id = null)
	{
		if(empty($map_id))
			return false;
	
		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('vs.id');
		$query->from($query->quoteName($this->_tbl).'  AS vs ');
		$query->join('LEFT', '#__sdi_map_virtualservice AS cvs ON cvs.virtualservice_id=vs.id');
		$query->where('cvs.map_id = ' . (int) $map_id);
		$query->where('vs.state = 1' );
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
			return false;
		}
			
		return $rows;
	}
	
	/**
	 * Method to save the service compliance deducted from the aggregation process
	 *
	 * @param array 	$pks	array of the #__sdi_sys_servicecompliance ids to link with the current service
	 * @param int		$id		primary key of the current service to save.
	 *
	 * @return boolean 	True on success, False on error
	 *
	 * @since EasySDI 3.0.0
	 */
	public function saveServiceCompliance ($pks)
	{
		
		//Delete previously saved compliance
		$query = $this->_db->getQuery(true);
		$query->delete(' #__sdi_virtualservice_servicecompliance');
		$query->where('service_id = '.(int) $this->id);
		$this->_db->setQuery($query);
		$this->_db->query();
	
		$arr_pks = json_decode ($pks);
		foreach ($arr_pks as $pk)
		{
			try 
			{
				//Get servicecompliance
				$query = $this->_db->getQuery(true);
				$query->select('sc.id');
				$query->from('#__sdi_sys_servicecompliance  AS sc ');
				$query->join('INNER', '#__sdi_sys_serviceversion AS sv ON sv.id = sc.serviceversion_id');
				$query->where('sc.serviceconnector_id = ' . (int) $this->serviceconnector_id);
				$query->where('sv.value = '. $query->quote($pk));
				$this->_db->setQuery($query);
				$servicecompliance = $this->_db->loadResult();

				if(empty ($servicecompliance))
					continue;
				
				$query = $this->_db->getQuery(true);
				$query->insert('#__sdi_virtualservice_servicecompliance');
				$query->set('service_id='.(int) $this->id);
				$query->set('servicecompliance_id='.(int) $servicecompliance);
				$this->_db->setQuery($query);
				if (!$this->_db->query()) {
					throw new Exception($this->_db->getErrorMsg());
				}
				
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
}
