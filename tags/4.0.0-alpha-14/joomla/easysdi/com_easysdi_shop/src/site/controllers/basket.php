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

/**
 * Basket controller class.
 */
class Easysdi_shopControllerBasket extends Easysdi_shopController {

    public function estimate() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->saveBasketToSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=estimate', false));
    }

    public function order() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->saveBasketToSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=order', false));
    }

    public function draft() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $this->saveBasketToSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=draft', false));
    }

    public function load() {
        $id = JFactory::getApplication()->input->get('id', '', 'int');
        $basket = new sdiBasket();
        $basket->loadOrder($id);

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=edit', false));
    }

    public function saveBasketToSession() {
        $jinput = JFactory::getApplication()->input;
        $buffer = $jinput->get('buffer', '', 'float');
        $ordername = $jinput->get('ordername', '', 'string');
        $thirdparty = $jinput->get('thirdparty', '', 'int');
        $wmc = htmlentities($jinput->get('wmc', '', 'RAW'));

        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $basket->name = $ordername;
        $basket->buffer = $buffer;
        $basket->thirdparty = $thirdparty;
        $basket->wmc = $wmc;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
    }

    /**
     * Method to save 
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();

        $sdiUser = sdiFactory::getSdiUser();
        if ($sdiUser->juser->guest) {
            // Authentication
            // Populate the data array:
            $authentication = array();
            $authentication['return'] = 'index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=' . JFactory::getApplication()->input->get('action', 'save', 'string');

            $jinput = JFactory::getApplication()->input;
            $authentication['username'] = $jinput->get('username', '', 'STRING');
            $authentication['password'] = $jinput->get('password', '', 'STRING');

            // Get the log in options.
            $options = array();
            $options['remember'] = $this->input->getBool('remember', false);
            $options['return'] = $authentication['return'];

            // Get the log in credentials.
            $credentials = array();
            $credentials['username'] = $authentication['username'];
            $credentials['password'] = $authentication['password'];

            // Perform the log in.
            if (true === $app->login($credentials, $options)) {
                // Success
                $this->setMessage('Authentication done', 'info');
                $app->setUserState('users.login.form.data', array());
            } else {
                // Login failed !
                $this->setMessage('Authentication failed', 'error');
                $authentication['remember'] = (int) $options['remember'];
                $app->setUserState('users.login.form.data', $authentication);
                $app->redirect(JRoute::_($authentication['return'], false));
                return;
            }
        }


        // Initialise variables.
        $model = $this->getModel('Basket', 'Easysdi_shopModel');

        // Get the user data.
        $data = $model->getData();

        // Check for errors.
        //??
        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket', false));
            return false;
        }

        // Redirect to the oreder list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_SAVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.basket.content', null);
    }

}