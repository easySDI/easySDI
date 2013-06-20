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

/**
 * relation Table class
 */
class Easysdi_catalogTablerelation extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_relation', 'id', $db);
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
		if($task == 'apply' || $task == 'save'){
			$array['modified'] = date("Y-m-d H:i:s");
		}
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');
		if(($task == 'save' || $task == 'apply') && (!JFactory::getUser()->authorise('core.edit.state','com_easysdi_catalog.relation.'.$array['id']) && $array['state'] == 1)){
			$array['state'] = 0;
		}

		//Support for multiple or not foreign key field: parent_id
			if(isset($array['parent_id'])){
				if(is_array($array['parent_id'])){
					$array['parent_id'] = implode(',',$array['parent_id']);
				}
				else if(strrpos($array['parent_id'], ',') != false){
					$array['parent_id'] = explode(',',$array['parent_id']);
				}
				else if(empty($array['parent_id'])) {
					$array['parent_id'] = '';
				}
			}

		//Support for multiple or not foreign key field: attributechild_id
			if(isset($array['attributechild_id'])){
				if(is_array($array['attributechild_id'])){
					$array['attributechild_id'] = implode(',',$array['attributechild_id']);
				}
				else if(strrpos($array['attributechild_id'], ',') != false){
					$array['attributechild_id'] = explode(',',$array['attributechild_id']);
				}
				else if(empty($array['attributechild_id'])) {
					$array['attributechild_id'] = '';
				}
			}

		//Support for multiple or not foreign key field: classchild_id
			if(isset($array['classchild_id'])){
				if(is_array($array['classchild_id'])){
					$array['classchild_id'] = implode(',',$array['classchild_id']);
				}
				else if(strrpos($array['classchild_id'], ',') != false){
					$array['classchild_id'] = explode(',',$array['classchild_id']);
				}
				else if(empty($array['classchild_id'])) {
					$array['classchild_id'] = '';
				}
			}

        if(!JFactory::getUser()->authorise('core.admin', 'com_easysdi_catalog.relation.'.$array['id'])){
            $actions = JFactory::getACL()->getActions('com_easysdi_catalog','relation');
            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_catalog.relation.'.$array['id'])->getData();
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
        return 'com_easysdi_catalog.relation.' . (int) $this->$k;
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
        $assetParent->loadByName('com_easysdi_catalog');
        // Return the found asset-parent-id
        if ($assetParent->id){
            $assetParentId=$assetParent->id;
        }
        return $assetParentId;
    }
    
    

}
