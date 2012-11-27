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
			$query->select('l.*,
					v.url as virtualserviceurl,
					p.resourceurl as physicalserviceurl,
					cv.value as virtualconnector,
					cp.value as physicalconnector,
					v.alias as virtualservicealias,
					p.alias as physicalservicealias,
					v.id as virtualserviceid,
					p.id as physicalserviceid'
			);
			$query->from($this->_tbl.' AS l');
			$query->join('LEFT', '#__sdi_virtualservice AS v ON l.virtualservice_id=v.id');
			$query->join('LEFT', '#__sdi_physicalservice AS p ON l.physicalservice_id=p.id');
			$query->join('LEFT', '#__sdi_sys_serviceconnector AS cv ON v.serviceconnector_id=cv.id');
			$query->join('LEFT', '#__sdi_sys_serviceconnector AS cp ON p.serviceconnector_id=cp.id');
			$query->where('l.group_id = ' . (int) $key);
			$query->where('l.state = 1' );
			$query->order('l.ordering ASC' );
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();
			
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
			
				//Get the max supported version of the service
				if(!empty($row->physicalserviceid))
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_physicalservice AS ps');
					$query->join('LEFT', '#__sdi_service_servicecompliance ssc ON ssc.service_id = ps.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('ps.id = '.(int) $row->physicalserviceid);
					$query->where('ssc.servicetype = "physical"');
					$this->_db->setQuery($query);
				}
				if(!empty($row->virtualserviceid))
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_virtualservice AS vs');
					$query->join('LEFT', '#__sdi_service_servicecompliance ssc ON ssc.service_id = vs.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('vs.id = '.(int) $row->virtualserviceid);
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
