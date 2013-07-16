<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';

/**
 * metadata Table class
 */
class Easysdi_catalogTablemetadata extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_metadata', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {


        $task = JRequest::getVar('task');
        if ($task == 'apply' || $task == 'save') {
            $array['modified'] = date("Y-m-d H:i:s");
        }


        if (!JFactory::getUser()->authorise('core.admin', 'com_easysdi_catalog.metadata.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_easysdi_catalog', 'metadata');
            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_catalog.metadata.' . $array['id'])->getData();
            $array_jaccess = array();
            foreach ($actions as $action) {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }
            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }
        //Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules'])) {
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name 
     *
     * @see JTable::_getAssetName 
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_catalog.metadata.' . (int) $this->$k;
    }

    /**
     * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId($table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_catalog');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

    /**
     * Overriden JTable::store to set modified data and user id.
     *
     * @param	boolean	True to update fields even if they are null.
     * @return	boolean	True on success.
     * @since	1.6
     */
    public function store($updateNulls = true) {
        (empty($this->id) ) ? $new = true : $new = false;

        if (parent::store($updateNulls)) {
            $CSWmetadata = new sdiMetadata($this->id);
            if ($new) {
                if(!$CSWmetadata->insert()){
                    return false;
                }
            }else{
                if(!$CSWmetadata->update()){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function delete($pk = null) {
        
        $CSWmetadata = new sdiMetadata($pk);
        
        if(parent::delete($pk)){
//            return $CSWmetadata->delete();
            return true;
        }
        return false;
    }
}
