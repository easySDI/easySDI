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
class Easysdi_shopModelRequest extends JModelForm {

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

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.request.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_shop.edit.request.id', $id);
        }
        $this->setState('request.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('request.id', $params_array['item_id']);
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
                $id = $this->getState('request.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                //Get user informations
                $this->_item->client = sdiFactory::getSdiUser($this->_item->user_id);

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
        $id = (!empty($id)) ? $id : (int) $this->getState('request.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('request.id');

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
        $form = $this->loadForm('com_easysdi_shop.request', 'request', array('control' => 'jform', 'load_data' => $loadData));
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
     * Method to save a product (diffusion) from request form
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function saveproduct($data) {

        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('request.id');

        //Check the user can edit this request (resource extraction responsible)
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
            return false;
        }

        $authorizeddiffusion = $user->getResponsibleExtraction();

        //Save order_diffusion
        $app = JFactory::getApplication();
        $files = $app->input->files->get('jform', null, 'raw');

        $diffusion_id = (int) $data['current_product'];

        if (!in_array($diffusion_id, $authorizeddiffusion)):
            //This user is not supposed to access this diffusion
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
            return false;
        endif;

        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = (int) $id;
        $keys['diffusion_id'] = (int) $diffusion_id;
        $orderdiffusion->load($keys);
        $filter = JFilterInput::getInstance();
        $orderdiffusion->remark = $filter->clean($data['remark'][$diffusion_id], 'string');
        $orderdiffusion->storage_id = (int) Easysdi_shopHelper::EXTRACTSTORAGE_LOCAL;
        if (!empty($files['file'][$diffusion_id][0]['name'])):
            //get clean filename for storage
            $storeFileName = Easysdi_shopHelper::getCleanFilename($files['file'][$diffusion_id][0]['name']);
            //Save uploaded file 
            $folder = $app->getParams('com_easysdi_shop')->get('orderresponseFolder');
            $orderdiffusion->size = $files['file'][$diffusion_id][0]['size'];
            $orderdiffusion->file = $storeFileName;
            $orderdiffusion->displayName = $storeFileName;
            $extractsFilesPath = JPATH_BASE . '/' . $folder . '/' . $id . '/' . $diffusion_id;
            if (!file_exists($extractsFilesPath)) {
                if (!mkdir($extractsFilesPath, 0755, true)) {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_CREATE_FOLDER_ERROR_MESSAGE'), 'error');
                }
            }
            $file = $files['file'][$diffusion_id][0]['tmp_name'];

            $newfile = $extractsFilesPath . '/' . $storeFileName;
            if (!move_uploaded_file($file, $newfile)):

                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_COPY_FILE_ERROR_MESSAGE'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&id=' . $id, false));
                return false;
            endif;
        endif;
        if (!empty($orderdiffusion->remark) || !empty($files['file'][$diffusion_id][0]['name']) || !empty($data['fee'][$diffusion_id])):
            $orderdiffusion->completed = date('Y-m-d H:i:s');
            $orderdiffusion->productstate_id = Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE;
        endif;
        $orderdiffusion->created_by = (int) sdiFactory::getSdiUser()->juser->id;
        $orderdiffusion->store();

        //store pricing in pricing tables if pricing is enabled
        $pricingisActivated = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');
        if ($pricingisActivated) {
            $inputFee = str_replace(',', '.', $data['fee'][$diffusion_id]);
            $floatFee = floatval($inputFee);
            $po = Easysdi_shopHelper::getPricingOrder((int) $id);
            $posp = Easysdi_shopHelper::getPricingOrderSupplierProduct($diffusion_id, $po->id);
            $pos = Easysdi_shopHelper::getPricingOrderSupplier($posp->pricing_order_supplier_id);

            $cfg_rounding = (float) JComponentHelper::getParams('com_easysdi_shop')->get('rounding', 0.05);

            $posp->cal_total_amount_ti = Easysdi_shopHelper::rounding($floatFee, $cfg_rounding);

            Easysdi_shopHelper::updatePricing($posp, $pos, $po);
        }

        return $this->save($data);
    }

    /**
     * Method to reject a product
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function rejectproduct($data) {

        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('request.id');

        //Check the user can edit this request (resource extraction responsible)
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
            return false;
        }

        $authorizeddiffusion = $user->getResponsibleExtraction();

        //Save order_diffusion
        $app = JFactory::getApplication();

        $diffusion_id = (int) $data['current_product'];

        if (!in_array($diffusion_id, $authorizeddiffusion)):
            //This user is not supposed to access this diffusion
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
            return false;
        endif;

        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = (int) $id;
        $keys['diffusion_id'] = (int) $diffusion_id;
        $orderdiffusion->load($keys);
        $filter = JFilterInput::getInstance();
        $orderdiffusion->remark = $filter->clean($data['rejectionremark'], 'string');
        $orderdiffusion->completed = date('Y-m-d H:i:s');
        $orderdiffusion->productstate_id = Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER;
        $orderdiffusion->created_by = (int) sdiFactory::getSdiUser()->id;

        $orderdiffusion->store();

        //store pricing in pricing tables if pricing is enabled
        $pricingisActivated = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');
        if ($pricingisActivated) {
            $po = Easysdi_shopHelper::getPricingOrder((int) $id);
            $posp = Easysdi_shopHelper::getPricingOrderSupplierProduct($diffusion_id, $po->id);
            $pos = Easysdi_shopHelper::getPricingOrderSupplier($posp->pricing_order_supplier_id);
            $posp->cal_total_amount_ti = 0.0;
            $posp->cal_total_rebate_ti = 0.0;
            Easysdi_shopHelper::updatePricing($posp, $pos, $po);
        }

        return $this->save($data);
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {

        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('request.id');

        $table = $this->getTable();
        $table->load($id);

        $newOrderstate = Easysdi_shopHelper::getNewOrderState($id);
        if (isset($newOrderstate)) {
            $table->orderstate_id = $newOrderstate;
            if ($newOrderstate == Easysdi_shopHelper::ORDERSTATE_FINISH) {
                $table->completed = date('Y-m-d H:i:s');
            }
        }

        if ($table->store(false) === true) {
            return $id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('request.id');
        if (JFactory::getUser()->authorise('core.delete', 'com_easysdi_shop.request.' . $id) !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
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
                ->where('id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

}
