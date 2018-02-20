<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * orderdiffusion Table class
 */
class Easysdi_shopTablediffusionpropertyvalue extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_diffusion_propertyvalue', 'id', $db);
    }

    public function loadByDiffusionID($id = null) {
        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('d.propertyvalue_id, pv.property_id');
        $query->from($query->quoteName($this->_tbl).' as d');
        $query->innerJoin('#__sdi_propertyvalue pv ON pv.id = d.propertyvalue_id');
        $query->innerJoin('#__sdi_property p ON p.id = pv.property_id');
        $query->where($this->_db->quoteName('diffusion_id') . ' = ' . (int) $id);
        $query->order('p.ordering');
        $this->_db->setQuery($query);

        $t = $query->dump();
        try {
            $rows = $this->_db->loadObjectList();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        // Legacy error handling switch based on the JError::$legacy switch.
        // @deprecated  12.1
        if (JError::$legacy && $this->_db->getErrorNum()) {
            $e = new JException($this->_db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        // Check that we have a result.
        if (empty($rows)) {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        // Bind the object with the row and return.
        return $rows;
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name 
     *
     * @see JTable::_getAssetName 
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_shop.diffusionpropertyvalue.' . (int) $this->$k;
    }

    /**
     * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId(JTable $table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_shop');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }
    
    /**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database. 
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @link    http://docs.joomla.org/JTable/check
	 * @since   11.1
	 */
    public function check() {
        //Always set state published to avoid problem with sqlsrv driver on default value handling
        $this->state = 1;
        
        //If this is a new row then get the next ordering value
        if ($this->id == 0) {
            $this->ordering = $this->getNextOrder();
        }
        
        return parent::check();
    }
}
