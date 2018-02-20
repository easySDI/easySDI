<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/resource.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/version.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_service/tables/physicalservice.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_service/tables/virtualservice.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/models/perimeter.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelDownload extends JModelLegacy {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $id = JFactory::getApplication()->input->get('id');
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.download.id', $id);
        $this->setState('download.id', $id);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('download.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                if ($table->state != 1) {
                    return $this->_item;
                }
                // Convert the JTable to a clean JObject.
                $_properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($_properties, 'JObject');

                //Load accessscope
                $this->_item->organisms = sdiModel::getAccessScopeOrganism($this->_item->guid);
                $this->_item->users = sdiModel::getAccessScopeUser($this->_item->guid);
                $this->_item->categories = sdiModel::getAccessScopeCategory($this->_item->guid);
                //Load notified user
                $diffusionnotifieduser = JTable::getInstance('diffusionnotifieduser', 'Easysdi_shopTable');
                $this->_item->notifieduser_id = $diffusionnotifieduser->loadBydiffusionID($this->_item->id);
                //Load grid perimeter
                if (!empty($this->_item->perimeter_id)):
                    $this->_item->perimeter = JModelLegacy::getInstance('perimeter', 'Easysdi_shopModel');
                    $this->_item->perimeter->getData($this->_item->perimeter_id);
                endif;
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Diffusion', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

}
