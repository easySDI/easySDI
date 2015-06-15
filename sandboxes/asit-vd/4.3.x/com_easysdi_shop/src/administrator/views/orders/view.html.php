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

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_shop.
 */
class Easysdi_shopViewOrders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

		Easysdi_shopHelper::addSubmenu('orders');

		$this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/easysdi_shop.php';

		$state	= $this->get('State');
		$canDo	= Easysdi_shopHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'), 'orders.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/order';
       /* if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('order.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('order.edit','JTOOLBAR_EDIT');
		    }

        }*/

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			   // JToolBarHelper::custom('orders.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			   // JToolBarHelper::custom('orders.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                //JToolBarHelper::deleteList('', 'orders.delete','JTOOLBAR_DELETE');
            }

            // use delete
            JToolBarHelper::deleteList('', 'orders.delete','JTOOLBAR_DELETE');

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('orders.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('orders.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}

        //Show trash and delete for components that uses the state field
       /* if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'orders.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('orders.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }*/

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_easysdi_shop');
		}

        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_easysdi_shop&view=orders');

        $this->extra_sidebar = '';


        // get order model
        $orderModel   = JModelLegacy::getInstance('orders', 'Easysdi_shopModel', array());

        //add Filter ordertype INPUT
        $ordertypeList= $orderModel->getOrderTypes();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_ORDERTYPE'),
            'filter_ordertype',
            JHtml::_('select.options', $ordertypeList, "id", "value", $this->state->get('filter.ordertype'), true)
        );


        //add Filter orderstate INPUT
        $orderstateList= $orderModel->getOrderStates();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_STATE'),
            'filter_orderstate',
            JHtml::_('select.options', $orderstateList, "id", "value", $this->state->get('filter.orderstate'), true)
        );

        //add Filter orderuser INPUT
        $orderuserList= $orderModel->getOrderUsers();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_USER'),
            'filter_orderuser',
            JHtml::_('select.options', $orderuserList, "id", "name", $this->state->get('filter.orderuser'), true)
        );

        //add Filter orderprovider INPUT
        $orderproviderList= $orderModel->getOrderProviders();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_PROVIDER'),
            'filter_orderprovider',
            JHtml::_('select.options', $orderproviderList, "id", "name", $this->state->get('filter.orderprovider'), true)
        );

        //add Filter diffusionName INPUT
        $orderdiffusion= $orderModel->getOrderDiffusion();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_DIFFUSION'),
            'filter_orderdiffusion',
            JHtml::_('select.options', $orderdiffusion, "id", "name", $this->state->get('filter.orderdiffusion'), true)
        );


        //add Filter sent INPUT
        $ordersent= Easysdi_shopHelper::getRangeOptions();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_SENT'),
            'filter_ordersent',
            JHtml::_('select.options', $ordersent, "value", "text", $this->state->get('filter.ordersent'), true)
        );


        //add Filter completed INPUT
        $ordercompleted= Easysdi_shopHelper::getRangeOptions();
        JHtmlSidebar::addFilter(
            JText::_('COM_EASYSDI_SHOP_FILTER_SELECT_ORDERS_COMPLETED'),
            'filter_ordercompleted',
            JHtml::_('select.options', $ordercompleted, "value", "text", $this->state->get('filter.ordercompleted'), true)
        );








	}

	protected function getSortFields()
	{
		return array(
	/*	'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.guid' => JText::_('COM_EASYSDI_SHOP_ORDERS_GUID'),
		'a.alias' => JText::_('COM_EASYSDI_SHOP_ORDERS_ALIAS'),
		'a.created_by' => JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED_BY'),
		'a.created' => JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED'),
		'a.modified_by' => JText::_('COM_EASYSDI_SHOP_ORDERS_MODIFIED_BY'),
		'a.modified' => JText::_('COM_EASYSDI_SHOP_ORDERS_MODIFIED'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		'a.checked_out' => JText::_('COM_EASYSDI_SHOP_ORDERS_CHECKED_OUT'),
		'a.checked_out_time' => JText::_('COM_EASYSDI_SHOP_ORDERS_CHECKED_OUT_TIME'),
		'a.name' => JText::_('COM_EASYSDI_SHOP_ORDERS_NAME'),
		'a.access' => JText::_('COM_EASYSDI_SHOP_ORDERS_ACCESS'),
		'a.asset_id' => JText::_('COM_EASYSDI_SHOP_ORDERS_ASSET_ID'),*/

        'a.name' => JText::_('COM_EASYSDI_SHOP_ORDERS_NAME'),
        'user' => JText::_('COM_EASYSDI_SHOP_ORDERS_USER'),
        'thirdparty' => JText::_('COM_EASYSDI_SHOP_ORDERS_THIRDPARTY'),
        'a.created' => JText::_('COM_EASYSDI_SHOP_ORDERS_CREATED'),
        'a.completed' => JText::_('COM_EASYSDI_SHOP_ORDERS_COMPLETED'),
		);
	}


}
