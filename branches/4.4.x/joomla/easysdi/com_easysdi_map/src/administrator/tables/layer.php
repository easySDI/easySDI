<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

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
		parent::__construct('#__sdi_maplayer', 'id', $db);
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
        $assetParent->loadByName('com_easysdi_map');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
	
    /**
     * Method to load only id Items from the database by group key
     *
     * @param   int    $key   Group Id to load layer from
     *
     * @return  list list of layer id
     *
     * @since   EasySDI v3
     */
    public function loadItemsIdByGroup ($key = null)
    {
    	if(empty($key))
    		return false;
    	
    	$query	= $this->_db->getQuery(true);
    	$query->select('lg.layer_id');
    	$query->from('#__sdi_layer_layergroup as lg');
    	$query->join('LEFT', '#__sdi_maplayer as l ON l.id = lg.layer_id');
    	$query->where('lg.group_id = ' . (int) $key);
    	$query->order('lg.ordering DESC' );
    	$this->_db->setQuery($query);
    	try 
    	{
    		$items = $this->_db->loadColumn();
    	}
    	catch (JDatabaseException $e)
    	{
    		$je = new JException($e->getMessage());
    		$this->setError($je);
    		return false;
    	}
    	return $items;
    }
    
	/**
	 * Method to load full Items from the database by group key 
	 *
	 * @param   int    $key   Group Id to load layer from
	 *
	 * @return  list list of layer object
	 *
	 * @since   EasySDI v3
	 */
	public function loadItemsByGroup($key = null)
	{
		if(empty($key))
			return false;
		try
		{
			$query = $this->_db->getQuery(true);
			$query->select('l.*,  m.guid as metadata_guid, d.id as diffusion_id, d.hasdownload as hasdownload, d.hasextraction as hasextraction');
			$query->from($this->_db->quoteName($this->_tbl).' AS l');
			$query->join('INNER', '#__sdi_layer_layergroup AS lg ON lg.layer_id=l.id');
                        $query->join('LEFT', '#__sdi_visualization v ON v.maplayer_id = l.id');
                        $query->join('LEFT', '#__sdi_version version ON version.id = v.version_id');
                        $query->join('LEFT', '#__sdi_metadata m ON m.version_id = version.id');
                        $query->join('LEFT', '#__sdi_diffusion d ON d.version_id = version.id ');                        
			$query->where('lg.group_id = '. (int) $key );
			$query->where('l.state = 1' );
			$query->order('lg.ordering DESC' );
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
					$query->from($this->_db->quoteName($this->_tbl).' AS l');
					$query->join('LEFT', '#__sdi_virtualservice AS v ON l.service_id=v.id');
                                        $query->join('LEFT', '#__sdi_sys_serviceconnector AS cv ON v.serviceconnector_id=cv.id');
					$query->where('l.id = ' . (int) $row->id);
					$this->_db->setQuery($query);
					$service = $this->_db->loadObject();
					
					$row->serviceurl 	= $service->virtualserviceurl;
					$row->serviceconnector 	= $service->virtualconnector;
					$row->servicealias 	= $service->virtualservicealias;
                                        
                                        //server type
                                        $query = $this->_db->getQuery(true);
                                        $query->select('p.server_id');
					$query->from($this->_db->quoteName($this->_tbl).' AS l');
					$query->join('LEFT', '#__sdi_virtualservice AS v ON l.service_id=v.id');
                                        $query->join('LEFT', '#__sdi_virtual_physical AS vp ON vp.virtualservice_id=v.id');
                                        $query->join('LEFT', '#__sdi_physicalservice AS p ON vp.physicalservice_id=p.id');
                                        $query->group('p.server_id');					
					$query->where('l.id = ' . (int) $row->id);
					$this->_db->setQuery($query);
					$services = $this->_db->loadColumn();
                                        if(count($services) > 1){
                                            //virtual service aggregates more than one kind of physical services
                                            $row->servertype        = 3;
                                        }else{
                                            $row->servertype = $services[0];
                                        }
				}
				else
				{
					$query = $this->_db->getQuery(true);
					$query->select('p.resourceurl as physicalserviceurl,
									cp.value as physicalconnector,
									p.alias as physicalservicealias,
									p.id as physicalserviceid,
                                                                        p.server_id as servertype');
					$query->from($this->_db->quoteName($this->_tbl).' AS l');
					$query->join('LEFT', '#__sdi_physicalservice AS p ON l.service_id=p.id');
					$query->join('LEFT', '#__sdi_sys_serviceconnector AS cp ON p.serviceconnector_id=cp.id');
					$query->where('l.id = ' . (int) $row->id);
					$this->_db->setQuery($query);
					$service = $this->_db->loadObject();
					
					$row->serviceurl 	= $service->physicalserviceurl;
					$row->serviceconnector 	= $service->physicalconnector;
					$row->servicealias 	= $service->physicalservicealias;
                                        $row->servertype        = $service->servertype;
				}
				
				//Get the max supported version of the service
				if(!empty($row->servicetype) && $row->servicetype == 'physical' )
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_physicalservice AS ps');
					$query->join('LEFT', '#__sdi_physicalservice_servicecompliance ssc ON ssc.service_id = ps.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('ps.id = '.(int) $row->service_id);
					$this->_db->setQuery($query);
				}
				if(!empty($row->servicetype) && $row->servicetype == 'virtual' )
				{
					$query = $this->_db->getQuery(true);
					$query->select('max(sv.value)');
					$query->from('#__sdi_virtualservice AS vs');
					$query->join('LEFT', '#__sdi_virtualservice_servicecompliance ssc ON ssc.service_id = vs.id');
					$query->join('LEFT', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
					$query->join('LEFT', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
					$query->where('vs.id = '.(int) $row->service_id);
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
        
        public function store($updateNulls = false)
	{
		$k = $this->_tbl_keys;

		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeStore', array($updateNulls, $k));

		$currentAssetId = 0;
                
                if (empty($this->guid)) {
                    $this->guid = Easysdi_coreHelper::uuid();
                }
                
                $date = JFactory::getDate();
                $user = JFactory::getUser();
                if ($this->id) {
                    // Existing item
                    $this->modified = $date->toSql();
                    $this->modified_by = $user->get('id');
                } else {
                    $this->created = $date->toSql();
                    $this->created_by = $user->get('id');
                }

		if (!empty($this->asset_id))
		{
			$currentAssetId = $this->asset_id;
		}

		// The asset id field is managed privately by this class.
		if ($this->_trackAssets)
		{
			unset($this->asset_id);
		}

		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey())
		{
			$result = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
		}
		else
		{
			$result = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
		}

		// If the table is not set to track assets return true.
		if ($this->_trackAssets)
		{
			if ($this->_locked)
			{
				$this->_unlock();
			}

			/*
			 * Asset Tracking
			 */
			$parentId = $this->_getAssetParentId();
			$name     = $this->_getAssetName();
			$title    = $this->_getAssetTitle();

			$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
			$asset->loadByName($name);

			// Re-inject the asset id.
			$this->asset_id = $asset->id;

			// Check for an error.
			$error = $asset->getError();

			if ($error)
			{
				$this->setError($error);

				return false;
			}
			else
			{
				// Specify how a new or moved node asset is inserted into the tree.
				if (empty($this->asset_id) || $asset->parent_id != $parentId)
				{
					$asset->setLocation($parentId, 'last-child');
				}

				// Prepare the asset to be stored.
				$asset->parent_id = $parentId;
				$asset->name      = $name;
				$asset->title     = $title;

				if ($this->_rules instanceof JAccessRules)
				{
					$asset->rules = (string) $this->_rules;
				}

				if (!$asset->check() || !$asset->store($updateNulls))
				{
					$this->setError($asset->getError());

					return false;
				}
				else
				{
					// Create an asset_id or heal one that is corrupted.
					if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id)))
					{
						// Update the asset_id field in this table.
						$this->asset_id = (int) $asset->id;

						$query = $this->_db->getQuery(true)
							->update($this->_db->quoteName($this->_tbl))
							->set('asset_id = ' . (int) $this->asset_id);
						$this->appendPrimaryKeys($query);
						$this->_db->setQuery($query)->execute();
					}
				}
			}
		}

		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterStore', array(&$result));

		return $result;
	}

}
