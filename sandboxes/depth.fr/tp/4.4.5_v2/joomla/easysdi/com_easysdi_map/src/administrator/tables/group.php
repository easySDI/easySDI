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
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_map/tables/layer.php';

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
		parent::__construct('#__sdi_layergroup', 'id', $db);
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
      * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
      *
      * @see JTable::_getAssetParentId 
    */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
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
	 * Method to load a group from the database and his associated layers full objects
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 * set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   EasySDI 3.3.0
	 */
	public function loadWithLayers($keys = null, $reset = true)
	{
		if(!parent::load($keys,$reset))
			return false;
	
				$layertable 	= JTable::getInstance('layer', 'easysdi_mapTable');
				$this->layers 	= $layertable->loadItemsByGroup($this->id);
	
		return true;
	}
	
	/**
	 * Method to return the list of group ids used by the specified map id
	 *
	 * @param   integer    	$map_id   			A context identifier
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
		$query->select('g.id, mg.isbackground, mg.isdefault');
		$query->from($this->_tbl.'  AS g ');
		$query->join('LEFT', '#__sdi_map_layergroup AS mg ON mg.group_id=g.id');
		$query->where('mg.map_id = ' . (int) $map_id);
		$query->where('g.state = 1' );
		$query->order('mg.ordering ASC' );
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
	
		// Check that we have a result.
		if (empty($rows))
		{
			return false;
		}
		
		return $rows;
	}
	
	/**
	 * Method to return the list of group linked to the specified layer
	 *
	 * @param   integer    	$layer   			layer identifier
	 *
	 * @return  false if request failed, the list of group if succeed
	 *
	 * @since   EasySDI 3.3.0
	 */
	public function loadItemsByLayer ($layer)
	{
		$query	= $this->_db->getQuery(true);
		$query->select('g.id');
		$query->from('#__sdi_layer_layergroup as lg');
		$query->join('inner', '#__sdi_layergroup as g ON g.id = lg.group_id');
		$query->where('lg.layer_id = ' . (int) $layer);
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
		// Check that we have a result.
		if (empty($items))
		{
			return false;
		}
		return $items;
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
