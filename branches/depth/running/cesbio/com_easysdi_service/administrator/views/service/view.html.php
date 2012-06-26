<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_serviceViewService extends JView
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        
        $state	= $this->get('State');
		$this->canDo		= Easysdi_serviceHelper::getActions($state->get('filter.category_id'), $this->item->id);

		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_SERVICE'), 'service.png');
		
		// If not checked out, can save the item.
		if (!$checkedOut && ( 		($this->canDo->get('core.edit') && !$isNew)
				||  ($this->canDo->get('core.create') && $isNew))
				||	($this->canDo->get('core.edit.own') && $this->item->created_by == $user->get('id')
				)
		)
		{

			JToolBarHelper::apply('service.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('service.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && $this->canDo->get('core.edit')&& $this->canDo->get('core.create')){
			JToolBarHelper::custom('service.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		//Alias must be change before saving (unique index) 
// 		if (!$isNew && $canDo->get('core.create')) {
// 			JToolBarHelper::custom('service.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
// 		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('service.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('service.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}
