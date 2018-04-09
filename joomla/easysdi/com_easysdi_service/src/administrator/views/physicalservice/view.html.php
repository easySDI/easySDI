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
class Easysdi_serviceViewPhysicalService extends JViewLegacy {

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

        $state = $this->get('State');
        $this->canDo = Easysdi_serviceHelper::getActionsPhysicalService($state->get('filter.category_id'), $this->item->id);

        JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_HEADER_PHYSICALSERVICE'), 'links.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ( ($this->canDo->get('core.edit') && !$isNew) || ($this->canDo->get('core.create') && $isNew)) || ($this->canDo->get('core.edit.own') && $this->item->created_by == $user->get('id')
                )
        ) {

            JToolBarHelper::apply('physicalservice.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('physicalservice.save', 'JTOOLBAR_SAVE');
        }
// 		if (!$checkedOut && $this->canDo->get('core.edit')&& $this->canDo->get('core.create')){
// 			JToolBarHelper::custom('physicalservice.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
// 		}

        if (empty($this->item->id)) {
            JToolBarHelper::cancel('physicalservice.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('physicalservice.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}
