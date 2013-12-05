<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

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

                //Get constante value (to display)
                $this->_item->orderstate = constant('Easysdi_shopTableorder::orderstate_' . $this->_item->orderstate_id);
                $this->_item->ordertype = constant('Easysdi_shopTableorder::ordertype_' . $this->_item->ordertype_id);
            } elseif ($error = $table->getError()) {
                $this->setError($error);
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
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
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
        $files = $app->input->files->get('jform');

        foreach ($data['diffusion'] as $diffusion_id):
            if (!in_array($diffusion_id, $authorizeddiffusion)):
                //This user is not supposed to access this diffusion
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
                return false;
            endif;
            $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
            $keys = array();
            $keys['order_id'] = $id;
            $keys['diffusion_id'] = $diffusion_id;
            $orderdiffusion->load($keys);
            $orderdiffusion->fee = $data['fee'][$diffusion_id];
            $orderdiffusion->remark = $data['remark'][$diffusion_id];
            if (!empty($files['file'][$diffusion_id][0]['name'])):
                //Save uploaded file 
                $folder = $app->getParams('com_easysdi_shop')->get('orderresponseFolder');
                $orderdiffusion->size = $files['file'][$diffusion_id][0]['size'];
                $orderdiffusion->file = $files['file'][$diffusion_id][0]['name'];
                mkdir($folder . '/' . $id . '/' . $diffusion_id, 0777, true);
                $file = $files['file'][$diffusion_id][0]['tmp_name'];
                $newfile = $folder . '/' . $id . '/' . $diffusion_id . '/' . $files['file'][$diffusion_id][0]['name'];
                if (!move_uploaded_file($file, $newfile)):
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_COPY_FILE_ERROR_MESSAGE'), 'error');
                    JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&id=' . $id, false));
                    return false;
                endif;                
            endif;
            if(!empty($orderdiffusion->fee) || !empty($orderdiffusion->remark) || !empty($files['file'][$diffusion_id][0]['name'])):
                $orderdiffusion->completed = date('Y-m-d H:i:s');
                $orderdiffusion->productstate_id = 1;
            endif;
            $orderdiffusion->created_by = sdiFactory::getSdiUser()->id;
            $orderdiffusion->store();
        endforeach;

        //Update order state if needed
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
                ->from('#__sdi_order_diffusion')
                ->where('order_id = ' . (int) $id)
                ->where('productstate_id = 2');
        $db->setQuery($query);
        $orderdone = $db->loadResult();

        $sdiUser = sdiFactory::getSdiUser($data['user_id']);
        if ($orderdone == 0):
            $data['orderstate_id'] = 3;
            $data['completed'] = date('Y-m-d H:i:s');
            if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_DONE_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_DONE_BODY', $data['name']))):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            endif;
        elseif ($orderdone < count($data['diffusion'])):
            $data['orderstate_id'] = 5;
            if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_PROGRESS_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_PROGRESS_BODY', $data['name']))):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            endif;
        elseif ($orderdone == count($data['diffusion'])) :
            $data['orderstate_id'] = 4;
            if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_PROGRESS_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_REQUEST_SEND_MAIL_ORDER_PROGRESS_BODY', $data['name']))):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            endif;
        endif;

        if ($data['thirdparty_id'] == '')
            $data['thirdparty_id'] = null;

        $table = $this->getTable();
        if ($table->save($data) === true) {
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