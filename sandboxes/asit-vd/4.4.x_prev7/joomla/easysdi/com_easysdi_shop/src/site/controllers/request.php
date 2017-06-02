<?php

/**
 * @version     4.4.4
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

/**
 * Request controller class.
 */
class Easysdi_shopControllerRequest extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_shop.edit.request.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.request.id', $editId);

        // Get the model.
        $model = $this->getModel('Request', 'Easysdi_shopModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&layout=edit', false));
    }

    /**
     * Method to save one product in a request
     *
     * @return	void
     * @since	1.6
     */
    public function saveproduct() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Request', 'Easysdi_shopModel');

        // Get the user data.
        $data = $app->input->get('jform', array(), 'array');

        // Attempt to save the data.
        $return = $model->saveproduct($data);

        // Check for errors.
        if ($return === false) {
            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.request.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&layout=edit&id=' . $id, false));
            return false;
        }

        Easysdi_shopHelper::notifyCustomerOnOrderUpdate($data['id']);


        //check if there is more product for this extraction manager
        if ($this->hasMoreProductsTodo((int) $data['id'])) {

            // Flush the product data from the session.
            $app->setUserState('com_easysdi_shop.edit.request.data', null);

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_PRODUCT_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&layout=edit&id=' . $data['id'] . '#sdi-recap-prod-list', false));
        } else {

            // Check in the request.
            if ($return) {
                $model->checkin($return);
            }

            // Clear the profile id from the session.
            $app->setUserState('com_easysdi_shop.edit.request.id', null);

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_PRODUCT_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));

            // Flush the data from the session.
            $app->setUserState('com_easysdi_shop.edit.request.data', null);
        }
    }

    /**
     * Check if the current user is extraction responsible for at least one product in sent state
     * @param type $orderId the of the order to check
     * @return boolean
     */
    private function hasMoreProductsTodo($orderId) {
        $sdiUser = sdiFactory::getSdiUser();
        $extractionResponsibleDiffusions = (array) $sdiUser->getResponsibleExtraction();
        $sdiBasket = new sdiBasket();
        $sdiBasket->loadOrder((int) $orderId);
        foreach ($sdiBasket->extractions as $extraction) {
            if ($extraction->productstate_id == Easysdi_shopHelper::PRODUCTSTATE_SENT && in_array($extraction->id, $extractionResponsibleDiffusions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reject a product by a provider
     * @return boolean
     */
    public function rejectproduct() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Request', 'Easysdi_shopModel');

        // Get the user data.
        $data = $app->input->get('jform', array(), 'array');


        // Attempt to reject the product
        $return = $model->rejectproduct($data);

        // Check for errors.
        if ($return === false) {
            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.request.id');
            $this->setMessage(JText::sprintf('Reject failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=request&layout=edit&id=' . $id, false));
            return false;
        }

        Easysdi_shopHelper::notifyCustomerOnOrderUpdate($data['id']);

        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_easysdi_shop.edit.request.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_REQUEST_REJECTED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.request.data', null);
    }

    /**
     * Cancel request edition and checkin item
     */
    function cancel() {
        $model = $this->getModel('Request', 'Easysdi_shopModel');
        $id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.request.id');
        $model->checkin($id);
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
    }

}
