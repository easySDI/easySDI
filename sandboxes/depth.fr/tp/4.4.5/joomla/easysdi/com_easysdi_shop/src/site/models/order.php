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
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelOrder extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_shop');
        $layout = $app->input->get('layout');

        //user accesing the order with token, set token and id
        if ($app->input->getString('a_token')) {
            $this->setState('access_token', $app->input->getString('a_token'));
            $id = $app->input->getInt('id');
            $app->setUserState('com_easysdi_shop.edit.order.id', $id);
        }

        // Load state from the request userState on edit or from the passed variable on default
        if ($layout == 'edit') {
            $id = $app->getUserState('com_easysdi_shop.edit.order.id');
        } else {
            if ($layout == 'validation') {
                $this->setState('layout.validation', true);
                if ($app->input->getInt('vm')) {
                    $this->setState('validation.manager', $app->input->getInt('vm'));
                }
                if ($app->input->getString('v_token')) {
                    $this->setState('validation_token', $app->input->getString('v_token'));
                }
            }
            $id = $app->input->getInt('id');
            $app->setUserState('com_easysdi_shop.edit.order.id', $id);
        }
        $this->setState('order.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('order.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
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
                $id = $this->getState('order.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                //Get constante value (to display)
                $this->_item->orderstate = constant('Easysdi_shopTableorder::orderstate_' . $this->_item->orderstate_id);
                $this->_item->ordertype = constant('Easysdi_shopTableorder::ordertype_' . $this->_item->ordertype_id);
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }

            //if a validator is set, loat it
            if (isset($this->_item->validated_by)) {
                $validator = new sdiUser($this->_item->validated_by);
                $this->_item->validator = $validator->name;
            }

            $basket = new sdiBasket();
            $basket->loadOrder($id);

            $this->_item->basket = $basket;
        }

        return $this->_item;
    }

    public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_easysdi_shop.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = $this->getData();

        return $data;
    }

    /**
     * 
     * @param type $id
     * @param type $state
     * @return boolean
     */
    function setOrderState($id, $state) {
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');
        $table = $this->getTable();
        if (!$table->load($id)) {
            return false;
        }

        $table->orderstate_id = $state;
        return $table->store(false);
    }
    
    /**
     * 
     * @param type $id
     * @param type $archived
     * @return boolean
     */
    function archive($id){
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');
        $table = $this->getTable();
        if (!$table->load($id)) {
            return false;
        }

        $table->archived = 1;
        return $table->store(false);
    }

    /**
     * thirdpartyValidation - validation of an order by a thirdparty
     * 
     * @param integer $id
     * @param mixed $reason optional string parameters to attach a message
     * 
     * @return boolean
     * @since 4.3.0
     */
    public function thirdpartyValidation($id, $validatorId, $reason = null) {
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');
        $table = $this->getTable();
        if (!$table->load($id)) {
            return false;
        }

        $table->orderstate_id = Easysdi_shopHelper::ORDERSTATE_SENT;
        $table->validated = true;
        $table->validated_date = date('Y-m-d H:i:s');
        $table->validated_reason = $reason;
        $table->validated_by = $validatorId;

        if (($orderStored = $table->store()) === true) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->update('#__sdi_order_diffusion')
                    ->set('productstate_id=' . Easysdi_shopHelper::PRODUCTSTATE_SENT)
                    ->where('order_id = ' . (int) $id);
            $db->setQuery($query);
            $db->execute();
        }

        return $orderStored;
    }

    /**
     * thirdpartyRejection - rejection of an order by a thirdparty
     * 
     * @param integer $id
     * @param mixed $reason - defined as optional but $reason have to be set !
     * @return boolean
     * @since 4.3.0
     */
    public function thirdpartyRejection($id, $validatorId, $reason = null) {
        $id = (!empty($id)) ? $id : (int) $this->getState('order.id');
        $table = $this->getTable();
        if (!$table->load($id) || is_null($reason)) {
            return false;
        }

        $table->orderstate_id = Easysdi_shopHelper::ORDERSTATE_REJECTED;
        $table->validated = false;
        $table->validated_date = date('Y-m-d H:i:s');
        $table->validated_reason = $reason;
        $table->validated_by = $validatorId;

        $orderStored = $table->store();

        if (($orderStored) === true) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->update('#__sdi_order_diffusion')
                    ->set('productstate_id=' . Easysdi_shopHelper::PRODUCTSTATE_REJECTED_TP)
                    ->where('order_id = ' . (int) $id);
            $db->setQuery($query);
            $db->execute();
        }

        return $orderStored;
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('order.id');

        //check user rights 

        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            return $id;
        } else {
            return false;
        }

        return true;
    }

    function getCategoryName($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
                ->select('title')
                ->from('#__categories')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

}
