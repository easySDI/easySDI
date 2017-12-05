<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/models/order.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

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

    /**
     * validate - thirdparty validation of an order
     * 
     * @return void
     * @since 4.3.0
     */
    public function validate() {
        $app = JFactory::getApplication();
        $validateId = $app->input->getInt('id', 0, 'int');
        $validatorId = $app->input->getInt('sdiUserId', null, 'int');

        if ($validatorId == 0 || $validatorId == '' || $validatorId == null) {
            //Wrong user id set message
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            return;
        }

        if ($validateId == 0) {
            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_NO_ID'));
        } else {
            $model = $this->getModel('Order', 'Easysdi_shopModel');

            //get validator user
            $validator = sdiFactory::getSdiUser($validatorId);
            if (!in_array($model->getData($validateId)->thirdparty_id, $validator->getOrganisms(array(sdiUser::validationmanager), true))) {
                //is not validator, set message
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                return;
            }

            //ensure the order needs validation/rejection
            if (intval($model->getData($validateId)->orderstate_id) !== Easysdi_shopHelper::ORDERSTATE_VALIDATION) {
                //is not validator, set message
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_NO_POSSIBLE_STATE'), 'error');
                return;
            }

            $model->checkout($validateId);

            $model->thirdpartyValidation($validateId, $validatorId, $app->input->get('reason', null, 'html'));

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Notify notifiedusers and extractionresponsible for each orderdiffusion of the current order
            Easysdi_shopHelper::notifyExtractionResponsibleAndNotifiedUsers($validateId);

            //Notify validation managers
            Easysdi_shopHelper::notifyAfterValidationManager($validateId);

            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATED_SUCCESSFULLY'));
        }

        // Redirect to the list screen. (if user is logged in)
        if (!JFactory::getUser()->guest) {
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation', false));
        } else {
            $this->setRedirect(JURI::base());
        }
    }

    /**
     * reject - thirdparty rejection of an order
     * 
     * @return void
     * @since 4.3.0
     */
    public function reject() {
        $app = JFactory::getApplication();
        $validateId = $app->input->getInt('id', 0, 'int');
        $validatorId = $app->input->getInt('sdiUserId', null, 'int');
        $reason = $app->input->get('reason', null, 'html');

        if ($validatorId == 0 || $validatorId == '' || $validatorId == null) {
            //Wrong user id set message
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            return;
        }

        if ($validateId == 0 || $reason == '') {
            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_REJECTION_NO_ID_OR_REASON'));
        } else {
            $model = $this->getModel('Order', 'Easysdi_shopModel');

            //get validator user
            $validator = sdiFactory::getSdiUser($validatorId);
            if (!in_array($model->getData($validateId)->thirdparty_id, $validator->getOrganisms(array(sdiUser::validationmanager), true))) {
                //is not validator, set message
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                return;
            }

            //ensure the order needs validation/rejection
            if (intval($model->getData($validateId)->orderstate_id) !== Easysdi_shopHelper::ORDERSTATE_VALIDATION) {
                //is not validator, set message
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATION_NO_POSSIBLE_STATE'), 'error');
                return;
            }

            $model->checkout($validateId);

            $model->thirdpartyRejection($validateId, $validatorId, $reason);

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_REJECTED_SUCCESSFULLY'));

            //Notify customer
            Easysdi_shopHelper::notifyCustomerOnOrderUpdate($validateId);
            //Notify validation managers
            Easysdi_shopHelper::notifyAfterValidationManager($validateId);
        }

        // Redirect to the list screen. (if user is logged in)
        if (!JFactory::getUser()->guest) {
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation', false));
        } else {
            $this->setRedirect(JURI::base());
        }
    }

    /**
     * check rights and download an order file
     */
    function download() {
        $diffusion_id = JFactory::getApplication()->input->getInt('id', null, 'int');
        $order_id = JFactory::getApplication()->input->getInt('order', null, 'int');
        $access_token = JFactory::getApplication()->input->getString('a_token');

        if (empty($diffusion_id)):
            $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_EMPTY_ID');
            echo json_encode($return);
            die();
        endif;

        $order = JTable::getInstance('order', 'Easysdi_shopTable');
        $order->load($order_id);

        /////////// Check user right on this order
        $currentUser = sdiFactory::getSdiUser();
        $clientUser = new sdiUser((int) $order->user_id);
        // current user extrations (if is extraction responsible)
        $userExtrationsResponsible = $currentUser->getResponsibleExtraction();
        if (!is_array($userExtrationsResponsible)) {
            $userExtrationsResponsible = array();
        }
        
        $downloadAllowed = false;
        $organisms = $clientUser->getMemberOrganisms();

        //the user is the client
        if ($order->user_id == $currentUser->id):
            $downloadAllowed = true;
        //user has access token from mail
        elseif (strlen($access_token) >= 64 && $order->access_token == $access_token):
            $downloadAllowed = true;
        //the user is extraction responsible of the product
        elseif (in_array($diffusion_id, $userExtrationsResponsible)):
            $downloadAllowed = true;
        //the user is organism manager of the provider's organism
        elseif ($currentUser->isOrganismManager($diffusion_id, 'diffusion')):
            $downloadAllowed = true;
        //the user is organims manager of client's organism
        elseif ($currentUser->isOrganismManager($organisms[0]->id)):
            $downloadAllowed = true;
        endif;

        if (!$downloadAllowed) {
            $return['ERROR'] = JText::_('JERROR_ALERTNOAUTHOR');
            echo json_encode($return);
            die();
        }

        $diffusion = JTable::getInstance('diffusion', 'Easysdi_shopTable');
        $diffusion->load($diffusion_id);
        
        //if file is OTP protected and the user is the customer, disable downnload
        if (($order->user_id == $currentUser->id) && ($diffusion->otp == 1)){
            $return['ERROR'] = JText::_('JERROR_ALERTNOAUTHOR');
            echo json_encode($return);
            die();
        }
        
        //Load order response
        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = $order_id;
        $keys['diffusion_id'] = $diffusion_id;
        $orderdiffusion->load($keys);

        Easysdi_shopHelper::downloadOrderFile($orderdiffusion);
    }
    
    /**
     * download an order file with a OTP
     */
    function downloadOTP() {        
        $diffusion_id = JFactory::getApplication()->input->getInt('diffusion_id', null, 'int');
        $order_id = JFactory::getApplication()->input->getInt('order_id', null, 'int');
        $password = JFactory::getApplication()->input->getString('otp', null, 'int');
        $token = JFactory::getApplication()->input->getString('token', null, 'int');
        
        $order = JTable::getInstance('order', 'Easysdi_shopTable');
        $order->load($order_id);

        /////////// Check user right on this order
        $currentUser = sdiFactory::getSdiUser();
        $clientUser = new sdiUser((int) $order->user_id);
        // current user extrations (if is extraction responsible)
        $userExtrationsResponsible = $currentUser->getResponsibleExtraction();
        if (!is_array($userExtrationsResponsible)) {
            $userExtrationsResponsible = array();
        }

        $downloadAllowed = false;
        $organisms = $clientUser->getMemberOrganisms();

        //the user is the client
        if ($order->user_id == $currentUser->id):
            $downloadAllowed = true;
        //the user is extraction responsible of the product
        elseif (in_array($diffusion_id, $userExtrationsResponsible)):
            $downloadAllowed = true;
        //the user is organism manager of the provider's organism
        elseif ($currentUser->isOrganismManager($diffusion_id, 'diffusion')):
            $downloadAllowed = true;
        //the user is organims manager of client's organism
        elseif ($currentUser->isOrganismManager($organisms[0]->id)):
            $downloadAllowed = true;
        endif;

        if (!$downloadAllowed) {
            $return['status'] = 'ERROR';
            $return['msg'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_OTPAUTH');
            echo json_encode($return);
            die();
        }
        
        if ($token <> '')
        {
            $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
            $keys = array();
            $keys['order_id'] = $order_id;
            $keys['diffusion_id'] = $diffusion_id;
            $orderdiffusion->load($keys);
            if ($token == $orderdiffusion->get('otp')){
                Easysdi_shopHelper::downloadOrderFile($orderdiffusion);
            }else{
                die();
            }
        }else{
            if (empty($diffusion_id)):
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_EMPTY_ID');
                echo json_encode($return);
                die();
            endif;

            $order = JTable::getInstance('order', 'Easysdi_shopTable');
            $order->load($order_id);

            //Load order response
            $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
            $keys = array();
            $keys['order_id'] = $order_id;
            $keys['diffusion_id'] = $diffusion_id;
            $orderdiffusion->load($keys);
            $orderdiffusion->otpchance = (int) $orderdiffusion->get('otpchance')+1;
            $orderdiffusion->store();

            //Check if the password filled by the user is the right one and number of chances not reached         
            if ($orderdiffusion->get('otpchance') > 3){
                //change the status of the orderdiffusion to PRODUCTSTATE_BLOCKED
                $orderdiffusion->productstate_id = Easysdi_shopHelper::PRODUCTSTATE_BLOCKED;
                $orderdiffusion->store();
                //send an email to the extraction manager to unblock the download
                Easysdi_shopHelper::notifyExtractionResponsibleOTPChanceReached($orderdiffusion);
                $return['status'] = 'ERROR_OTPCHANCE';
                $return['msg'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_OTPCHANCEREACHED');
            }else{
                if (md5(trim($password)) == $orderdiffusion->get('otp')){
                    $return['status'] = 'OK';
                    //Reinit password to be used by the token
                    $orderdiffusion->otp = Easysdi_coreHelper::pwd(12);
                    $orderdiffusion->store();
                    $return['token'] = $orderdiffusion->otp;
                }else{
                    $return['status'] = 'ERROR_BADPASSWORD';
                    $return['msg'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_OTPBADPASSWORD');
                }
            }
            echo json_encode($return);
            die();
        }
    }
        
    function cancel() {
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.order.id', null);
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
    }

    public function remove() {
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        $id = JFactory::getApplication()->input->getInt('id', null, 'array');

        if (empty($id)):
            // Redirect back to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDERS_ERROR_MSG_CANT_REMOVE'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        endif;

        // Attempt to save the data.
        $return = $model->delete(array('id' => $id));

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

    function archive() {
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        $id = JFactory::getApplication()->input->getInt('id', null, 'array');

        if (empty($id)):
            // Redirect back to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDERS_ERROR_MSG_CANT_ARCHIVE'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        endif;

        // Attempt to save order state.
        $return = $model->archive($id);

        // Check for errors.
        if ($return === false) {
            // Redirect back to the list screen.
            $this->setMessage(JText::sprintf('COM_EASYSDI_SHOP_ORDER_ARCHIVED_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }

        // Clear the profile id from the session.
        JFactory::getApplication()->setUserState('com_easysdi_shop.edit.order.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_ARCHIVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
    }

    function saveState($state) {
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        $id = JFactory::getApplication()->input->getInt('id', null, 'array');

        if (empty($id)):
            // Redirect back to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDERS_ERROR_MSG_CANT_ARCHIVE'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        endif;

        // Attempt to save order state.
        $return = $model->setOrderState($id, $state);

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
    
    /**
     * generateOTP - check rights, generate a One Time Password, store it in database and notify customer
     * 
     * @return void
     * @since 4.4.2
     */
    function generateOTP()
    {
        $order_id = JFactory::getApplication()->input->getInt('order_id', null, 'array');
        $diffusion_id = JFactory::getApplication()->input->getInt('diffusion_id', null, 'array');
        
        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = (int) $order_id;
        $keys['diffusion_id'] = (int) $diffusion_id;
        $orderdiffusion->load($keys);
        
        $otp = Easysdi_coreHelper::pwd(128);
        
        //Generate the One Time Password
        if ($orderdiffusion->otp == ""){
            $orderdiffusion->otp = md5($otp);
            $orderdiffusion->store();
        
            //Send the password by email
            Easysdi_shopHelper::notifyCustomerOTP($order_id,$otp);
        }
        die();
    }

}
