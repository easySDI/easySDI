<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * resourcetype Table class
 */
class Easysdi_catalogTableresourcetype extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_resourcetype', 'id', $db);
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


        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');
        if (($task == 'save' || $task == 'apply') && (!JFactory::getUser()->authorise('core.edit.state', 'com_easysdi_catalog.resourcetype.' . $array['id']) && $array['state'] == 1)) {
            $array['state'] = 0;
        }
        
        if(!isset($array['monitoring'])){
            $array['monitoring'] = 0;
        }

        if (!JFactory::getUser()->authorise('core.admin', 'com_easysdi_catalog.resourcetype.' . $array['id'])) {
            $actions = JFactory::getACL()->getActions('com_easysdi_catalog', 'resourcetype');
            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_catalog.resourcetype.' . $array['id'])->getData();
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
        return 'com_easysdi_catalog.resourcetype.' . (int) $this->$k;
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
        $assetParent->loadByName('com_easysdi_catalog');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

    public function loadLocalname() {
            $lang = JFactory::getLanguage();

            $query = $this->_db->getQuery(true)
                    ->select('t.text1')
                    ->from('#__sdi_translation t')
                    ->innerJoin($this->_tbl . ' rt ON rt.guid = t.element_guid')
                    ->where('rt.id = ' . (int)$this->id)
                    ->where('t.language_id = (SELECT l.id FROM #__sdi_language l WHERE l.code = ' . $this->_db->quote($lang->getTag()) . ')');
            
            $this->_db->setQuery($query);
            $this->localname = $this->_db->loadResult();

            return true;
        
    }

}
