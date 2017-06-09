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
 * context Table class
 */
class Easysdi_mapTablevisualization extends sdiTable {

	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db) {
		parent::__construct('#__sdi_visualization', 'id', $db);
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
		return 'com_easysdi_map.visualization.' . (int) $this->$k;
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

}
