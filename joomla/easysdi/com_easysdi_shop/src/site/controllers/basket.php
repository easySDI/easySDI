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

jimport('joomla.user.authentication');
require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

/**
 * Basket controller class.
 */
class Easysdi_shopControllerBasket extends Easysdi_shopController {
    
    // ORDERSTATE
    const ORDERSTATE_ARCHIVED           = 1;
    const ORDERSTATE_HISTORIZED         = 2;
    const ORDERSTATE_FINISH             = 3;
    const ORDERSTATE_AWAIT              = 4;
    const ORDERSTATE_PROGRESS           = 5;
    const ORDERSTATE_SENT               = 6;
    const ORDERSTATE_SAVED              = 7;
    const ORDERSTATE_VALIDATION         = 8;
    const ORDERSTATE_REJECTED           = 9; // rejected by thirdparty
    const ORDERSTATE_REJECTED_SUPPLIER  = 10; // rejected by supplier
    
    // ORDERTYPE
    const ORDERTYPE_ORDER       = 1;
    const ORDERTYPE_ESTIMATE    = 2;
    const ORDERTYPE_DRAFT       = 3;
    
    // ROLE
    const ROLE_MEMBER                   = 1;
    const ROLE_RESOURCEMANAGER          = 2;
    const ROLE_METADATARESPONSIBLE      = 3;
    const ROLE_METADATAEDITOR           = 4;
    const ROLE_DIFFUSIONMANAGER         = 5;
    const ROLE_PREVIEWMANAGER           = 6;
    const ROLE_EXTRACTIONRESPONSIBLE    = 7;
    const ROLE_PRICINGMANAGER           = 9;
    const ROLE_VALIDATIONMANAGER        = 10;
    
    // PRODUCTSTATE
    const PRODUCT_AVAILABLE         = 1;
    const PRODUCT_AWAIT             = 2;
    const PRODUCT_SENT              = 3;
    const PRODUCT_VALIDATION        = 4;
    const PRODUCT_REJECTED          = 5; // rejected by thirdparty
    const PRODUCT_REJECTED_SUPPLIER = 6; // rejected by supplier
    
    public function load() {
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id', '', 'int');
        $basket = new sdiBasket();
        $basket->loadOrder($id);

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

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        
        if($recalculatePricing){
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
     *  save the basket and prepare session data to save products
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->saveBasketToSession(false);
        $app = JFactory::getApplication();

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
    public function saveProduct(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getModel('Basket', 'Easysdi_shopModel');
        $return = $model->saveProduct();
        
        if($return === false){
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
    private function rollback(){
        $session =& JFactory::getSession();
        $basketData = $session->get('basketData');
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->delete('#__sdi_order')
                ->where('id='.(int)$basketData['order_id']);
        $db->setQuery($query)->execute();
        
        $this->clearSession();
    }
    
    /**
     * finalizeSave - after save is complete, send notifications and clean session
     * 
     * @return void
     * @since 4.3.0
     */
    public function finalizeSave(){
        $session = JFactory::getSession();
        $app = JFactory::getApplication();
        
        /*******************************/
        /** Process all notifications **/
        /*******************************/
        $basketData = $session->get('basketData');
        
        switch($basketData['orderstate_id']){
            case self::ORDERSTATE_SENT:
                // Process notification by diffusion
                foreach($basketData['diffusions'] as $diffusionId){
                    // Notify notifiedusers
                    Easysdi_shopHelper::notifyNotifiedUsers($diffusionId);

                    // Notify the responsible of extraction
                    Easysdi_shopHelper::notifyExtractionResponsible($diffusionId);
                }
                
                break;
            
            case self::ORDERSTATE_VALIDATION:
                $this->notifyTPValidationManager($basketData['order_id'], $basketData['thirdparty_id']);
                break;
        }
        
        // Notify the customer
        Easysdi_shopHelper::notifyCustomer($basketData['order_name']);
        
        /*******************/
        /** Clean session **/
        /*******************/
        $this->clearSession();
        $app->setUserState('com_easysdi_shop.basket.content', null);
        
        // END
        $app->enqueueMessage(JText::_('COM_EASYSDI_SHOP_ITEM_SAVED_SUCCESSFULLY'));
        
        $this->sendJsonResponse(array('redirect' => JRoute::_('index.php?option=com_easysdi_shop&view=orders', false)));
    }
    
    /**
     * clearSession
     * 
     * @return void
     * @since 4.3.0
     */
    private function clearSession(){
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
    private function sendJsonResponse($response){
        /*********************************************************/
        /** HACK TO PASS MESSAGE ACCROSS JAVASCRIPT REDIRECTION **/
        /*********************************************************/
        $app = JFactory::getApplication();
        $messageQueue = $app->getMessageQueue();
        
        if(count($messageQueue)>0){
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
    private function notifyTPValidationManager($orderId, $thirdpartyId){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Select orderdiffusion_id
        $query->select('user_id')
                ->from('#__sdi_user_role_organism')
                ->where('role_id='.self::ROLE_VALIDATIONMANAGER.' AND organism_id='.(int)$thirdpartyId);
        $db->setQuery($query);
        $users_ids = $db->loadColumn();
        
        $url = JRoute::_(JURI::base().'index.php?option=com_easysdi_shop&view=order&layout=validation&id='.$orderId.'&vm=%s&lang=fr');
        
        foreach($users_ids as $user_id){
            $user = sdiFactory::getSdiUser($user_id);
            $url = sprintf($url, $user_id);
            
            if(!$user->sendMail(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_VALIDATIONMANAGER_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_VALIDATIONMANAGER_BODY', $orderId, $url)))
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }

}