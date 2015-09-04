<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.user.authentication');
require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

/**
 * Basket controller class.
 */
class Easysdi_shopControllerBasket extends Easysdi_shopController {

    public function copy() {
        $this->load(true);
    }

    public function load($copy = false) {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id', '', 'int');
        $basket = new sdiBasket();
        $basket->loadOrder($id, $copy);

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=edit', false));
    }

    public function saveBasketToSession($recalculatePricing = true) {
        $jinput = JFactory::getApplication()->input;
        $buffer = $jinput->get('buffer', '', 'float');
        $ordername = $jinput->get('ordername', '', 'string');
        $thirdparty = $jinput->get('thirdparty', '', 'int');
        $mandate_ref = $jinput->get('mandate_ref', null, 'string');
        $mandate_contact = $jinput->get('mandate_contact', null, 'string');
        $mandate_email = $jinput->get('mandate_email', null, 'string');

        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $basket->name = $ordername;
        $basket->buffer = $buffer;
        $basket->thirdparty = $thirdparty;
        $basket->mandate_ref = $mandate_ref;
        $basket->mandate_contact = $mandate_contact;
        $basket->mandate_email = $mandate_email;
        if(is_null($basket->sdiUser->id)){
            $basket->sdiUser = sdiFactory::getSdiUser();
        }

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));

        if ($recalculatePricing) {
            //recalculate pricing if enable
            // rebuild extractions array to allow by supplier grouping
            Easysdi_shopHelper::extractionsBySupplierGrouping($basket);

            // calculate price for the current basket (only if surface is defined)
            Easysdi_shopHelper::basketPriceCalculation($basket);

            $return['pricing'] = $basket->pricing;

            header('content-type: application/json');
            echo json_encode($return);
            die();
        }

        return;
    }

    /**
     * Method to save 
     * save the basket and prepare session data to save products
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->saveBasketToSession(false);

        // Initialise variables.
        $model = $this->getModel('Basket', 'Easysdi_shopModel');

        // Get the user data.
        $data = $model->getData();

        $session = JFactory::getSession();
        $session->set('basketProcess', array('total' => 0));

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            $this->rollback();
            $this->sendJsonResponse(array('error' => JText::sprintf('Save failed', $model->getError())));
        }

        $basketProcess = $session->get('basketProcess');

        $this->sendJsonResponse($basketProcess);
    }

    /**
     * saveProduct - method to save product one by one from session's data
     * 
     * @return void
     * @since 4.3.0
     */
    public function saveProduct() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Basket', 'Easysdi_shopModel');
        $return = $model->saveProduct();

        if ($return === false) {
            $this->rollback();
            $this->sendJsonResponse(array('error' => array(JText::sprintf('Save failed', $model->getError()))));
        }

        $session = JFactory::getSession();
        $basketProcess = $session->get('basketProcess');
        $this->sendJsonResponse($basketProcess);
    }

    /**
     * rollback - remove order all children in case of incomplete save
     * 
     * @return void
     * @since 4.3.0
     */
    private function rollback() {
        $session = & JFactory::getSession();
        $basketData = $session->get('basketData');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->delete('#__sdi_order')
                ->where('id=' . (int) $basketData['order_id']);
        $db->setQuery($query)->execute();

        $this->clearSession();
    }

    /**
     * finalizeSave - after save is complete, send notifications and clean session
     * 
     * @return void
     * @since 4.3.0
     */
    public function finalizeSave() {
        $session = JFactory::getSession();
        $app = JFactory::getApplication();

        /*
         * *****************************
         * * Process all notifications *
         * *************************** */
        $basketData = $session->get('basketData');

        switch ($basketData['orderstate_id']) {
            case Easysdi_shopHelper::ORDERSTATE_SENT:
                // Process notification by diffusion
                foreach ($basketData['diffusions'] as $diffusionId) {
                    // Notify notifiedusers
                    Easysdi_shopHelper::notifyNotifiedUsers($diffusionId);

                    // Notify the responsible of extraction
                    Easysdi_shopHelper::notifyExtractionResponsible($diffusionId);
                }

                break;

            case Easysdi_shopHelper::ORDERSTATE_VALIDATION:
                $this->notifyTPValidationManager($basketData['order_id'], $basketData['thirdparty_id']);
                break;
        }

        // Notify the customer if order is not a draft
        if ($basketData['ordertype_id'] != Easysdi_shopHelper::ORDERTYPE_DRAFT) {
            Easysdi_shopHelper::notifyCustomer($basketData['order_name']);
        }

        /*
         * *****************
         * * Clean session * 
         * *************** */
        $this->clearSession();
        $app->setUserState('com_easysdi_shop.basket.content', null);

        // END
        switch ($basketData['ordertype_id']) {
            //Draft
            case Easysdi_shopHelper::ORDERTYPE_DRAFT:
                $app->enqueueMessage(JText::sprintf('COM_EASYSDI_SHOP_BASKET_DRAFT_SAVED',$basketData['order_id']));
                break;
            //Estimate
            case Easysdi_shopHelper::ORDERTYPE_ESTIMATE:
                $app->enqueueMessage(JText::sprintf('COM_EASYSDI_SHOP_BASKET_ESTIMATE_SENT',$basketData['order_id']));
                break;
            //Order
            default:
                $app->enqueueMessage(JText::sprintf('COM_EASYSDI_SHOP_BASKET_ORDER_SENT',$basketData['order_id']));
                break;
        }

        $this->sendJsonResponse(array('redirect' => JRoute::_('index.php?option=com_easysdi_shop&view=orders', false)));
    }

    /**
     * clearSession
     * 
     * @return void
     * @since 4.3.0
     */
    private function clearSession() {
        $session = JFactory::getSession();
        $session->clear('basketData');
        $session->clear('basketProducts');
        $session->clear('basketProcess');
    }

    /**
     * sendJsonResponse - encode response in json
     * used for ajax call
     * 
     * take back the messageQueue to session to retrieve it after javascript redirection
     * 
     * @param mixed $response
     * @return void
     * @since 4.3.0
     */
    private function sendJsonResponse($response) {
        /*
         * *******************************************************
         * * HACK TO PASS MESSAGE ACCROSS JAVASCRIPT REDIRECTION *
         * ***************************************************** */
        $app = JFactory::getApplication();
        $messageQueue = $app->getMessageQueue();

        if (count($messageQueue) > 0) {
            $session = JFactory::getSession();
            $session->set('application.queue', $messageQueue);
        }
        // ENDOF HACK

        header('content-type: application/json');
        echo json_encode($response);
        JFactory::getApplication()->close();
    }

    /**
     * notifyTPValidationManager - notify the thirdparty validation manager after an order
     * 
     * @param integer $orderId
     * @param integer $thirdpartyId
     * @return void
     * @since 4.3.0
     */
    private function notifyTPValidationManager($orderId, $thirdpartyId) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Select orderdiffusion_id
        $query->select('user_id')
                ->from('#__sdi_user_role_organism')
                ->where('role_id=' . Easysdi_shopHelper::ROLE_VALIDATIONMANAGER . ' AND organism_id=' . (int) $thirdpartyId);
        $db->setQuery($query);
        $users_ids = $db->loadColumn();

        $url = JRoute::_(JURI::base() . 'index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $orderId . '&vm=%s&lang=fr');

        foreach ($users_ids as $user_id) {
            $user = sdiFactory::getSdiUser($user_id);
            $url = sprintf($url, $user_id);

            if (!$user->sendMail(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_VALIDATIONMANAGER_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_VALIDATIONMANAGER_BODY', $orderId, $url)))
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }

    /**
     * Return the current number of product in the session basket
     * @return int
     */
    public static function getBasketContent() {
        $content = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        if ($content && !empty($content) && !empty($content->extractions)) {
            echo count($content->extractions);
        } else {
            echo 0;
        }
        die();
    }

}
