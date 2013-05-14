<?php

/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * cswspatialpolicy Table class
 */
class Easysdi_serviceTablecswspatialpolicy extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_csw_spatialpolicy', 'id', $db);
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
        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }
        if(!JFactory::getUser()->authorise('core.admin', 'com_easysdi_service.cswspatialpolicy.'.$array['id'])){
            $actions = JFactory::getACL()->getActions('com_easysdi_service','cswspatialpolicy');
            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_service.cswspatialpolicy.'.$array['id'])->getData();
            $array_jaccess = array();
            foreach($actions as $action){
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
        return 'com_easysdi_service.cswspatialpolicy.' . (int) $this->$k;
    }
 
    /**
      * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
      *
      * @see JTable::_getAssetParentId 
    */
    protected function _getAssetParentId($table = null, $id = null){
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
    
    

}
