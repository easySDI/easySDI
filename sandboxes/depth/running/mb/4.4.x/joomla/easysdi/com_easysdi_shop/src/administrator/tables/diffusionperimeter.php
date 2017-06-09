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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * orderdiffusion Table class
 */
class Easysdi_shopTablediffusionperimeter extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_diffusion_perimeter', 'id', $db);
    }

    public function loadByDiffusionID($id = null) {
        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('perimeter_id ');
        $query->from($this->_tbl.' as p');
        $query->innerjoin('#__sdi_perimeter per ON per.id = p.perimeter_id');
        $query->where($this->_db->quoteName('p.diffusion_id') . ' = ' . (int) $id);
        $query->order('per.ordering');
        
        $params = JComponentHelper::getParams('com_easysdi_shop');
        if ($params->get('userperimeteractivated') != 1){
            $query->where($this->_db->quoteName('p.perimeter_id') . ' NOT IN (2)');
        }
        $this->_db->setQuery($query);

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
        return 'com_easysdi_shop.diffusionperimeter.' . (int) $this->$k;
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
        $assetParent->loadByName('com_easysdi_shop');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
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
