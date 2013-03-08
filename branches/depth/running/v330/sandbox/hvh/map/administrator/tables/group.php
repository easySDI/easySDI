<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

if(!defined('DS')) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_map'.DS.'tables'.DS.'layer.php';

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
	protected function _getAssetParentId($table = null, $id = null)
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
		
		$layertable 	= JTable::getInstance('layer', 'easysdi_mapTable');
		$layers 		= $layertable->loadItemsByGroup($this->id);
		$this->layers = $layers;
		return true;
	}
	
	/**
	 * Method to return the list of group ids used by the specified context id
	 *
	 * @param   integer    	$map_id   			A context identifier
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   EasySDI 3.0.0
	 */
	public function loadIdsByMapId($map_id = null, $reset = true)
	{
		if ($reset)
		{
			$this->reset();
		}
		
		if(empty($map_id))
			return false;
		
		// Initialise the query.
		$query = $this->_db->getQuery(true);
		$query->select('g.id, cg.isbackground, cg.isdefault');
		$query->from($this->_tbl.'  AS g ');
		$query->join('LEFT', '#__sdi_map_layergroup AS cg ON cg.group_id=g.id');
		$query->where('cg.map_id = ' . (int) $map_id);
		$query->where('g.state = 1' );
		$query->order('g.ordering ASC' );
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

}
