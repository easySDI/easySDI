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

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';
require_once JPATH_COMPONENT . '/models/order.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';

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

            $model->checkout($validateId);

            $model->thirdpartyValidation($validateId, $validatorId, $app->input->get('reason', null, 'html'));

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Notify notifiedusers and extractionresponsible for each orderdiffusion of the current order
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('diffusion_id as id')
                    ->from('#__sdi_order_diffusion')
                    ->where('order_id=' . (int) $validateId);
            $db->setQuery($query);
            $diffusions = $db->loadObjectList();
            foreach ($diffusions as $diffusion) {
                Easysdi_shopHelper::notifyNotifiedUsers($diffusion->id);
                Easysdi_shopHelper::notifyExtractionResponsible($diffusion->id);
            }

            //Notify validation managers
            Easysdi_shopHelper::notifyAfterValidationManager($validateId, $model->getData($validateId)->thirdparty_id, Easysdi_shopHelper::ORDERSTATE_VALIDATION);

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

            $model->checkout($validateId);

            $model->thirdpartyRejection($validateId, $validatorId, $reason);

            $model->checkin($validateId);

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.order.id', null);

            // Set message
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ORDER_REJECTED_SUCCESSFULLY'));

            //Notify validation managers
            Easysdi_shopHelper::notifyAfterValidationManager($validateId, $model->getData($validateId)->thirdparty_id, Easysdi_shopHelper::ORDERSTATE_REJECTED);
        }

        // Redirect to the list screen. (if user is logged in)
        if (!JFactory::getUser()->guest) {
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders&layout=validation', false));
        } else {
            $this->setRedirect(JURI::base());
        }
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

        //remote stroage, use curl
        if ($orderdiffusion->storage_id == Easysdi_shopHelper::EXTRACTSTORAGE_REMOTE) {

            $curlHelper = new CurlHelper(true);

            $curldata['url'] = $orderdiffusion->file;
            $pos = strrpos($url, '.');
            $extension = ($pos) ? substr($url, $pos) : null;
            if ($extension) {
                $curldata['fileextension'] = $extension;
            }
            $curldata['filename'] = $orderdiffusion->displayName;
            return $curlHelper->get($curldata);
        }
        //local storage
        else {
        $folder = JFactory::getApplication()->getParams('com_easysdi_shop')->get('orderresponseFolder');
        $file = JPATH_BASE . '/' . $folder . '/' . $order_id . '/' . $diffusion_id . '/' . $orderdiffusion->file;

        error_reporting(0);
        
        $chunk = 8 * 1024 * 1024; // bytes per chunk (10 MB)
        
        $size = filesize($file); 
        if ($size > $chunk) 
        { 
        
            set_time_limit(0);
            ignore_user_abort(false);
            ini_set('output_buffering', 0);
            ini_set('zlib.output_compression', 0);

            $fh = fopen($file, "rb");

            if ($fh === false) { 
                $this->setMessage(JText::_('RESOURCE_LOCATION_UNAVAILABLE'), 'error');
                die();
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Accept-Ranges: bytes"); 
            header('Content-Disposition: attachment; filename="' . $orderdiffusion->file . '"'); 
            header('Expires: -1');
            header('Cache-Control: no-cache');
            header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0"); 
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            // Repeat reading until EOF
            while (!feof($fh)) { 
                $buffer = fread($fh, $chunk);
                echo $buffer;
                ob_flush();  // flush output
                //flush();
            }
        }else{
            ini_set('zlib.output_compression', 0);
            header('Pragma: public');
            header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
            header('Content-Transfer-Encoding: none');
            header("Content-Length: " . filesize($file));
            header('Content-Type: application/octetstream; name="' . $orderdiffusion->file . '"');
            header('Content-Disposition: attachement; filename="' . $orderdiffusion->file . '"');

            readfile($file);
        }
        
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
        $this->saveState(Easysdi_shopHelper::ORDERSTATE_ARCHIVED);
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

}
