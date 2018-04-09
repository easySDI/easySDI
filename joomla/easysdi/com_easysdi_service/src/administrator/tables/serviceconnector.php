<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * user Table class
 */
class Easysdi_serviceTableserviceconnector extends JTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_sys_serviceconnector', 'id', $db);
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
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_serviceconnector.serviceconnector.' . (int) $this->$k;
    }

    /**
     * Method to return the title to use for the asset table.
     *
     * @return  string
     *
     * @since   11.1
     */
    protected function _getAssetTitle() {
        return $this->alias;
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


        if (!JFactory::getUser()->authorise('core.admin', 'com_easysdi_serviceconnector.serviceconnector.' . $array['id'])) {

            $actions = JFactory::getACL()->getActions('com_easysdi_serviceconnector', 'serviceconnector');

            $default_actions = JFactory::getACL()->getAssetRules('com_easysdi_serviceconnector.serviceconnector.' . $array['id'])->getData();

            $array_jaccess = array();
            foreach ($actions as $action) {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }

            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }

        return parent::bind($array, $ignore);
    }

}
