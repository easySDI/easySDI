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
 * layer Table class
 */
class Easysdi_mapTablelayer extends sdiTable {

	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__sdi_map_layer', 'id', $db);
	}

	/**
	 * Overloaded check function
	 */
	public function check() {
		//If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0) 
		{
			$this->ordering = self::getNextOrder('group_id = '.$this->group_id);
		}
	
		return true;
	}
	
	/**
	 * Method to provide a shortcut to binding, checking and storing a JTable
	 * instance to the database table.  The method will check a row in once the
	 * data has been stored and if an ordering filter is present will attempt to
	 * reorder the table rows based on the filter.  The ordering filter is an instance
	 * property name.  The rows that will be reordered are those whose value matches
	 * the JTable instance for the property specified.
	 *
	 * @param   mixed   $src             An associative array or object to bind to the JTable instance.
	 * @param   string  $orderingFilter  Filter for the order updating
	 * @param   mixed   $ignore          An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link	http://docs.joomla.org/JTable/save
	 * @since   11.1
	 */
	public function save($src, $orderingFilter = '', $ignore = '')
	{
		return parent::save($src, "group_id = ".$this->group_id);
	}
	
	/**
	 * Method to compact the ordering values of rows in a group of rows
	 * defined by an SQL WHERE clause.
	 *
	 * @param   string  $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 *
	 * @return  mixed  Boolean true on success.
	 *
	 * @link    http://docs.joomla.org/JTable/reorder
	 * @since   11.1
	 */
	public function reorder($where = '')
	{
		return parent::reorder("group_id = ".$this->group_id);
	}
	
	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the
	 * ordering values.
	 *
	 * @return  mixed    Boolean true on success.
	 *
	 * @link    http://docs.joomla.org/JTable/move
	 * @since   11.1
	 */
	public function move($delta, $where = '')
	{
		return parent::move($delta,"group_id = ".$this->group_id);
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
		return 'com_easysdi_map.layer.' . (int) $this->$k;
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
	 * Method to load Items from the database by group key 
	 *
	 * @param   int    $key   Group Id to load layer from
	 *
	 * @return  list list of layer object
	 *
	 * @since   EasySDI v3
	 */
	public function getItemsByGroup($key = null)
	{
		try
		{
			$query = $this->_db->getQuery(true);
			$query->select('l.*');
			$query->from($this->_tbl.' AS l');
			$query->where('l.group_id = ' . (int) $key);
			$query->where('l.state = 1' );
			$query->order('l.ordering DESC' );
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
			
			foreach ($rows as $row)
			{
				if($row->servicetype == 'virtual')
				{
					$query = $this->_db->getQuery(true);
					$query->select('v.url as virtualserviceurl,
									cv.value as virtualconnector,
									v.alias as virtualservicealias,
									v.id as virtualserviceid');
					$query->from($this->_tbl.' AS l');
					$query->join('LEFT', '#__sdi_virtualservice AS v ON l.service_id=v.id');
					$query->join('LEFT', '#__sdi_sys_serviceconnector AS cv ON v.serviceconnector_id=cv.id');
					$query->where('l.id = ' . (int) $row->id);
					$this->_db->setQuery($query);
					$service = $this->_db->loadObject();
					
					$row->serviceurl 			= $service->virtualserviceurl;
					$row->serviceconnector 		= $service->virtualconnector;
					$row->servicealias 			= $service->virtualservicealias;
				}
				else
				{
					$query = $this->_db->getQuery(true);
					$query->select('p.resourceurl as physicalserviceurl,
									cp.value as physicalconnector,
									p.alias as physicalservicealias,
									p.id as physicalserviceid');
					$query->from($this->_tbl.' AS l');
					$query->join('LEFT', '#__sdi_physicalservice AS p ON l.service_id=p.id');
					$query->join('LEFT', '#__sdi_sys_serviceconnector AS cp ON p.serviceconnector_id=cp.id');
					$query->where('l.id = ' . (int) $row->id);
					$this->_db->setQuery($query);
					$service = $this->_db->loadObject();
					
					$row->serviceurl 			= $service->physicalserviceurl;
					$row->serviceconnector 		= $service->physicalconnector;
					$row->servicealias 			= $service->physicalservicealias;
				}
				
				//Get the max supported version of the service
				if(!empty($row->servicetype) && $row->servicetype == 'physical' )
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_physicalservice AS ps');
					$query->join('LEFT', '#__sdi_service_servicecompliance ssc ON ssc.service_id = ps.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('ps.id = '.(int) $row->service_id);
					$query->where('ssc.servicetype = "physical"');
					$this->_db->setQuery($query);
				}
				if(!empty($row->servicetype) && $row->servicetype == 'virtual' )
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_virtualservice AS vs');
					$query->join('LEFT', '#__sdi_service_servicecompliance ssc ON ssc.service_id = vs.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('vs.id = '.(int) $row->service_id);
					$query->where('ssc.servicetype = "virtual"');
					$this->_db->setQuery($query);
				}
				
				$version = $this->_db->loadResult();
				$row->version = $version;
			}
		}
		catch (JDatabaseException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		return $rows;
	}

}
