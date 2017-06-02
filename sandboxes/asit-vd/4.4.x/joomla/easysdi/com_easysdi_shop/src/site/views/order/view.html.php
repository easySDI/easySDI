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

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_shopViewOrder extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    protected $user;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_shop');
        $this->paramsarray = $this->params->toArray();
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        //use direct access validation link, needs validation token
        if ($this->state->get('validation.manager')) {
            $validation_token = $this->state->get('validation_token');
            if (strlen($validation_token) < 64 || $this->item->validation_token != $validation_token) {
                //on fail, return to home
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php', false));
                return false;
            }
            //token is OK, set validation user
            $this->user = sdiFactory::getSdiUser($this->state->get('validation.manager'));
        }
        //client direct access with token
        elseif ($this->state->get('access_token')) {
            $access_token = $this->state->get('access_token');
            if (strlen($access_token) < 64 || $this->item->access_token != $access_token) {
                //on fail, return to home
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php', false));
                return false;
            }
            //token is OK, set user = client
            $this->user = sdiFactory::getSdiUser($this->item->user_id);
        }
        //connected user, use current sdiUser
        else {
            $this->user = sdiFactory::getSdiUser();
        }

        if (!$this->user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }

        $this->isValidationManager = in_array($this->item->thirdparty_id, $this->user->getOrganisms(array(sdiUser::validationmanager), true));

        if ($this->user->id != $this->item->user_id // current user is not the orderer
                && !$this->isValidationManager // current user is not validation manager
                && !$this->user->isOrganismManager($this->item->user_id, 'user') // current user is not organism manager for the orderer's user
                && !$this->user->isOrganismManager($this->item->thirdparty_id, 'organism') // current user is not organism manager for the thirdparty organism
        ) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }

        //get the contact address of the user
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_contact/tables/address.php';
        $tableAddress = JTable::getInstance('Address', 'Easysdi_contactTable', array());
        $tableAddress->loadByUserID($this->item->basket->sdiUser->id, 1);
        $this->item->basket->sdiUser->contactAddress = $tableAddress;

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('com_easysdi_shop_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

    function getToolbar() {
        $bar = new JToolBar('toolbar');

        if ($this->state->get('layout.validation')) {
            if ($this->item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_VALIDATION && $this->isValidationManager) {
                $bar->appendButton('Standard', 'apply', JText::_('COM_EASYSDI_SHOP_ORDER_VALIDATE'), 'order.validate', false);
                $bar->appendButton('Separator');
                $bar->appendButton('Standard', 'delete', JText::_('COM_EASYSDI_SHOP_ORDER_REJECT'), 'order.reject', false);
                $bar->appendButton('Separator');
            }
        } else {
            //display the load draft button only if order not sent
            $this->item = $this->get('Data');
            if (($this->item->orderstate_id == Easysdi_shopHelper::ORDERSTATE_SAVED)) {
                $loadbutton = '<a onclick="acturl=\'' . JRoute::_('index.php?option=com_easysdi_shop&task=basket.load&id=' . $this->item->id) . '\';getBasketContent(\'addOrderToBasket\');" class="btn btn-small btn-success" aria-invalid="false">
                               <span class="icon-cart icon-white"></span> ' . JText::_('COM_EASYSDI_SHOP_ORDERS_LOAD_DRAFT_INTO_BASKET') . '</a>';
                $bar->appendButton('Custom', $loadbutton, JText::_('COM_EASYSDI_SHOP_ORDERS_LOAD_DRAFT_INTO_BASKET'));
                $bar->appendButton('Separator');
            }
            if ($this->get('Data')->orderstate_id != Easysdi_shopHelper::ORDERSTATE_SAVED) {
                $copybutton = '<a onclick="acturl=\'' . JRoute::_('index.php?option=com_easysdi_shop&task=basket.copy&id=' . $this->item->id) . '\';getBasketContent(\'addOrderToBasket\');" class="btn btn-small btn-success" aria-invalid="false">
                               <span class="icon-cart icon-white"></span> ' . JText::_('COM_EASYSDI_SHOP_ORDERS_COPY_ORDER_INTO_BASKET') . '</a>';
                $bar->appendButton('Custom', $copybutton, JText::_('COM_EASYSDI_SHOP_ORDERS_COPY_ORDER_INTO_BASKET'));
                $bar->appendButton('Separator');
            }
        }

        $bar->appendButton('Standard', 'cancel', JText::_('JCancel'), 'order.cancel', false);

        //generate the html and return
        return $bar->render();
    }

}
