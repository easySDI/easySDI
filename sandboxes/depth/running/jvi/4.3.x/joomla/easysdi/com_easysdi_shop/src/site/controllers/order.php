<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';
require_once JPATH_COMPONENT . '/models/order.php';

/**
 * Order controller class.
 */
class Easysdi_shopControllerOrder extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_shop.edit.order.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.order.id', $editId);

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=edit', false));
    }
    
    public function validate(){
        $app = JFactory::getApplication();
        $validateId = $app->input->getInt('id', 0, 'int');
        
        if($validateId == 0){
            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_NO_ID'));
        }
        else{
            $model = $this->getModel('Order', 'Easysdi_shopModel');
            $model->checkout($validateId);

            $model->thirdpartyValidation($validateId, $app->input->get('reason', null, 'html'));

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATED_SUCCESSFULLY'));
        }
        
        // Redirect to the list screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation', false));
    }
    
    public function reject(){
        $app = JFactory::getApplication();
        $validateId = $app->input->getInt('id', 0, 'int');
        $reason = $app->input->get('reason', null, 'html');
        
        if($validateId == 0 || $reason == ''){
            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_REJECTION_NO_ID_OR_REASON'));
        }
        else{
            $model = $this->getModel('Order', 'Easysdi_shopModel');
            $model->checkout($validateId);

            $model->thirdpartyRejection($validateId, $reason);

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_REJECTED_SUCCESSFULLY'));
        }
        
        // Redirect to the list screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation', false));
    }

    /**
     * 
     */
    function download() {
        $diffusion_id = JFactory::getApplication()->input->getInt('id', null, 'int');
        $order_id = JFactory::getApplication()->input->getInt('order', null, 'int');

        if (empty($diffusion_id)):
            $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_EMPTY_ID');
            echo json_encode($return);
            die();
        endif;

        //Check user right on this order
        $order = JTable::getInstance('order', 'Easysdi_shopTable');
        $order->load($order_id);
        if ($order->user_id != sdiFactory::getSdiUser()->id):
            $return['ERROR'] = JText::_('JERROR_ALERTNOAUTHOR');
            echo json_encode($return);
            die();
        endif;

        //Load order response
        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = $order_id;
        $keys['diffusion_id'] = $diffusion_id;
        $orderdiffusion->load($keys);

        $folder = JFactory::getApplication()->getParams('com_easysdi_shop')->get('orderresponseFolder');
        $file = JPATH_BASE. '/' .$folder.'/'.$order_id.'/'.$diffusion_id.'/'.$orderdiffusion->file;


        error_reporting(0);

        ini_set('zlib.output_compression', 0);
        header('Pragma: public');
        header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: none');
        header("Content-Length: " . filesize($file));
        header('Content-Type: application/octetstream; name="' . $orderdiffusion->file . '"');
        header('Content-Disposition: attachement; filename="' . $orderdiffusion->file . '"');

        readfile($file);
        die();
    }

    function cancel() {
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.order.id', null);
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
    }

    public function remove() {
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        $id = JFactory::getApplication()->input->getInt('id', null, 'array');

       if(empty($id)):
           // Redirect back to the list screen.
           $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDERS_ERROR_MSG_CANT_REMOVE'), 'error');
           $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
           return false;
       endif;

        // Attempt to save the data.
        $return = $model->delete(array('id'=> $id));

        // Check for errors.
        if ($return === false) {
            // Redirect back to the list screen.
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }

        // Check in.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.order.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));

    }
    
    function archive(){
        $this->saveState(Easysdi_shopModelOrder::ARCHIVED);        
    }
    
    function saveState($state){
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        $id = JFactory::getApplication()->input->getInt('id', null, 'array');

       if(empty($id)):
           // Redirect back to the list screen.
           $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDERS_ERROR_MSG_CANT_ARCHIVE'), 'error');
           $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
           return false;
       endif;

        // Attempt to save order state.
        $return = $model->setOrderState($id, $state );

        // Check for errors.
        if ($return === false) {
            // Redirect back to the list screen.
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }

        // Clear the profile id from the session.
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.order.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_ARCHIVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
    }

}