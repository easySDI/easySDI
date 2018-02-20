<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
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
class Easysdi_contactViewUser extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//To avoid error in form but seems weird to have to add this registration here
		JLoader::register('JTableUser', JPATH_LIBRARIES . '/joomla/database/table/user.php');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		
		$this->contactaddressmodel 	= JModelLegacy::getInstance('address', 'easysdi_contactModel');
		$this->contactitem 			= $this->contactaddressmodel->getItemByUserID($this->item->id,1);

		$this->billingaddressmodel 	= JModelLegacy::getInstance('address', 'easysdi_contactModel');
		$this->billingitem 			= $this->billingaddressmodel->getItemByUserID($this->item->id,2);
		
		$this->delivryaddressmodel 	= JModelLegacy::getInstance('address', 'easysdi_contactModel');
		$this->delivryitem 			= $this->delivryaddressmodel->getItemByUserID($this->item->id,3);
		
		$app 				= JFactory::getApplication();
                
                $this->pricing = JComponentHelper::getParams('com_easysdi_shop')->get('is_activated', false);
		
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
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        
        $state	= $this->get('State');
		$this->canDo	= Easysdi_contactHelper::getActions('user',$state->get('filter.category_id'),$this->item->id);
		
		JToolBarHelper::title(JText::_('COM_EASYSDI_CONTACT_HEADER_USER'), 'user-profile.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ( 		($this->canDo->get('core.edit') && !$isNew)
								||  ($this->canDo->get('core.create') && $isNew))
								||	($this->canDo->get('core.edit.own') && $this->item->created_by == $user->get('id')
							)
			)
		{
			JToolBarHelper::apply('user.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('user.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && $this->canDo->get('core.edit')&& $this->canDo->get('core.create')){
			JToolBarHelper::custom('user.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			JToolBarHelper::custom('user.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}
