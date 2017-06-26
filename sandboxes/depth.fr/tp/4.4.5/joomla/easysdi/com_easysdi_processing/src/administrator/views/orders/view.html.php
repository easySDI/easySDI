<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_processing.
 */
class Easysdi_processingViewOrders extends JViewLegacy
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

		Easysdi_processingHelper::addSubmenu('orders');

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
		//require_once JPATH_COMPONENT.'/helpers/easysdi_processing.php';

		$state	= $this->get('State');
		$canDo	= Easysdi_processingHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_EASYSDI_PROCESSING_TITLE_ORDERS'), 'orders.png');

            //Check if the form exists before showing the add/edit buttons
            $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/order';
           
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
                            JToolBarHelper::preferences('com_easysdi_processing');
                    }

            //Set sidebar action - New in 3.0
            JHtmlSidebar::setAction('index.php?option=com_easysdi_processing&view=orders');

            $this->extra_sidebar = '';


            // get order model
            $orderModel   = JModelLegacy::getInstance('orders', 'Easysdi_processingModel', array());

            //add Filter ordertype INPUT
            $orderprocessingList= $orderModel->getProcessings();
            JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_PROCESSING_FILTER_SELECT_ORDERS_PROCESSING'),
                'filter_orderprocessing',
                JHtml::_('select.options', $orderprocessingList, "id", "value", $this->state->get('filter.orderprocessing'), true)
            );


            //add Filter orderstate INPUT
            $orderstatusList= $orderModel->getOrderStatus();
            JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_PROCESSING_FILTER_SELECT_ORDERS_STATUS'),
                'filter_orderstatus',
                JHtml::_('select.options', $orderstatusList, "id", "value", $this->state->get('filter.orderstatus'), true)
            );

            //add Filter orderuser INPUT
            $orderuserList= $orderModel->getOrderUsers();
            JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_PROCESSING_FILTER_SELECT_ORDERS_USER'),
                'filter_orderuser',
                JHtml::_('select.options', $orderuserList, "id", "name", $this->state->get('filter.orderuser'), true)
            );

            //add Filter sent INPUT
            $ordersent= Easysdi_processingHelper::getRangeOptions();
            JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_PROCESSING_FILTER_SELECT_ORDERS_SENT'),
                'filter_ordersent',
                JHtml::_('select.options', $ordersent, "value", "text", $this->state->get('filter.ordersent'), true)
            );

            //add Filter completed INPUT
            $ordercompleted= Easysdi_processingHelper::getRangeOptions();
            JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_PROCESSING_FILTER_SELECT_ORDERS_COMPLETED'),
                'filter_ordercompleted',
                JHtml::_('select.options', $ordercompleted, "value", "text", $this->state->get('filter.ordercompleted'), true)
            );

	}

	protected function getSortFields()
	{
            return array(
                'a.name' => JText::_('COM_EASYSDI_PROCESSING_ORDERS_NAME'),
                'user' => JText::_('COM_EASYSDI_PROCESSING_ORDERS_USER'),
                'a.created' => JText::_('COM_EASYSDI_PROCESSING_ORDERS_CREATED'),
                'a.completed' => JText::_('COM_EASYSDI_PROCESSING_ORDERS_COMPLETED'),
            );
	}


}
