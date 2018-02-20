<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
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
class Easysdi_serviceViewVirtualservice extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        JHtml::_('bootstrap.framework');

        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        if ($this->item->layout)
            $this->setLayout($this->item->layout);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

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
        $this->canDo = Easysdi_serviceHelper::getActionsVirtualService();

        JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_HEADER_VIRTUALSERVICE'), 'virtualservice.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create')))) {

            JToolBarHelper::apply('virtualservice.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('virtualservice.save', 'JTOOLBAR_SAVE');
        }
        if (!$checkedOut && ($this->canDo->get('core.create'))) {
            JToolBarHelper::custom('virtualservice.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        // If an existing item, can save to a copy.
        if (!$isNew && $this->canDo->get('core.create')) {
            JToolBarHelper::custom('virtualservice.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('virtualservice.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('virtualservice.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
