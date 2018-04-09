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
 * View class for a list of Easysdi_shop.
 */
class Easysdi_shopViewOrders extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        Easysdi_shopAdminHelper::addSubmenu('orders');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

        $state = $this->get('State');
        $canDo = Easysdi_shopAdminHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'), 'orders.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/order';
       
        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                //JToolBarHelper::deleteList('', 'orders.delete','JTOOLBAR_DELETE');
            }

            // use delete
            JToolBarHelper::deleteList('', 'orders.delete', 'JTOOLBAR_DELETE');

            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('orders.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_shop');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_shop&view=orders');
        $this->extra_sidebar = '';

        // get order model
        $orderModel = JModelLegacy::getInstance('orders', 'Easysdi_shopModel', array());

        //add Filter ordertype INPUT
        $ordertypeList = $orderModel->getOrderTypes();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_ORDERTYPE'), 'filter_ordertype', JHtml::_('select.options', $ordertypeList, "id", "value", $this->state->get('filter.ordertype'), true)
        );

        //add Filter orderstate INPUT
        $orderstateList = $orderModel->getOrderStates();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_STATE'), 'filter_orderstate', JHtml::_('select.options', $orderstateList, "id", "value", $this->state->get('filter.orderstate'), true)
        );
        
        //add Filter order archived status INPUT
        $orderarchivedList = $orderModel->getOrderArchived();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_ARCHIVED'), 'filter_orderarchived', JHtml::_('select.options', $orderarchivedList, "id", "value", $this->state->get('filter.orderarchived'), true)
        );

        //add Filter orderuser INPUT
        $orderuserList = $orderModel->getOrderUsers();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_USER'), 'filter_orderuser', JHtml::_('select.options', $orderuserList, "id", "name", $this->state->get('filter.orderuser'), true)
        );
        
        //add Filter orderuserorganism INPUT
        $orderuserorganismList = $orderModel->getOrderUsersOrganisms();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_USER_ORGANISM'), 'filter_orderuserorganism', JHtml::_('select.options', $orderuserorganismList, "id", "name", $this->state->get('filter.orderuserorganism'), true)
        );        

        //add Filter orderprovider INPUT
        $orderproviderList = $orderModel->getOrderProviders();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_PROVIDER'), 'filter_orderprovider', JHtml::_('select.options', $orderproviderList, "id", "name", $this->state->get('filter.orderprovider'), true)
        );

        //add Filter diffusionName INPUT
        $orderdiffusion = $orderModel->getOrderDiffusion();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_DIFFUSION'), 'filter_orderdiffusion', JHtml::_('select.options', $orderdiffusion, "id", "name", $this->state->get('filter.orderdiffusion'), true)
        );

        //add Filter sent INPUT
        $ordersent = Easysdi_shopAdminHelper::getRangeOptions();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_SENT'), 'filter_ordersent', JHtml::_('select.options', $ordersent, "value", "text", $this->state->get('filter.ordersent'), true)
        );

        //add Filter completed INPUT
        $ordercompleted = Easysdi_shopAdminHelper::getRangeOptions();
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_COMPLETED'), 'filter_ordercompleted', JHtml::_('select.options', $ordercompleted, "value", "text", $this->state->get('filter.ordercompleted'), true)
        );
    }

    protected function getSortFields() {
        return array(
            'a.id' => JText::_('COM_EASYSDI_SHOP_ORDERS_ID'),
            'a.name' => JText::_('COM_EASYSDI_SHOP_ORDERS_NAME'),
            'user' => JText::_('COM_EASYSDI_SHOP_ORDERS_USER'),
            'thirdparty' => JText::_('COM_EASYSDI_SHOP_ORDERS_THIRDPARTY'),
            'a.sent' => JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED'),
            'a.completed' => JText::_('COM_EASYSDI_SHOP_ORDERS_COMPLETED'),
        );
    }

}
