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

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        //get the contact address of the user
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_contact/tables/address.php';
        $tableAddress = JTable::getInstance('Address', 'Easysdi_contactTable', array());
        $tableAddress->loadByUserID($this->item->basket->sdiUser->id, 1);
        $this->item->basket->sdiUser->contactAddress = $tableAddress;

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        $canDo = Easysdi_shopAdminHelper::getActions();

        JToolBarHelper::title(JText::_('COM_EASYSDI_SHOP_TITLE_ORDER'), 'order.png');

        /*
          // If not checked out, can save the item.
          if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
          {

          JToolBarHelper::apply('order.apply', 'JTOOLBAR_APPLY');
          JToolBarHelper::save('order.save', 'JTOOLBAR_SAVE');
          }
          if (!$checkedOut && ($canDo->get('core.create'))){
          JToolBarHelper::custom('order.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
          }
          // If an existing item, can save to a copy.
          if (!$isNew && $canDo->get('core.create')) {
          JToolBarHelper::custom('order.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
          }
          if (empty($this->item->id)) {
          JToolBarHelper::cancel('order.cancel', 'JTOOLBAR_CANCEL');
          }
          else {
          JToolBarHelper::cancel('order.cancel', 'JTOOLBAR_CLOSE');
          }
         */
        JToolBarHelper::cancel('order.cancel', 'JTOOLBAR_CLOSE');
    }

}
