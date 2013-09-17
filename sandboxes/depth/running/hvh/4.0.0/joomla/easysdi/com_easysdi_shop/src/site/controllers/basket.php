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
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=estimate', false));
    }

    public function order() {
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=order', false));
    }

    public function draft() {
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=draft', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();

        // Populate the data array:
        $authentication = array();
        $authentication['return'] = 'index.php?option=com_easysdi_shop&view=basket&layout=confirm&action=estimate';
        
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
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.basket.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.basket.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=basket' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_easysdi_shop.edit.basket.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_SAVED_SUCCESSFULLY'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.basket.data', null);
    }

}