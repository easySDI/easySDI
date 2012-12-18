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
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewPhysicalServices extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
		$this->categories	= $this->get('CategoryOrders');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$db 				= JFactory::getDBO();
		$query				= "SELECT id as value, value as text FROM #__sdi_sys_serviceconnector WHERE state=1" ;
		$db->setQuery($query);
		$this->connectorlist = $db->loadObjectList();
		
		$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		
		$state	= $this->get('State');
		$canDo	= Easysdi_serviceHelper::getActions('physical',$state->get('filter.category_id'));
		
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_PHYSICALSERVICES'), 'links-cat.png');

        //Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'physicalservice';
       
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('physicalservice.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit')) {
			    JToolBarHelper::editList('physicalservice.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('physicalservices.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('physicalservices.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'physicalservices.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('physicalservice.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('physicalservices.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'physicalservices.delete','JTOOLBAR_EMPTY_TRASH');
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('physicalservices.trash','JTOOLBAR_TRASH');
		    }
        }

		JToolBarHelper::divider();
        if ($canDo->get('core.admin')) {
        	JToolBarHelper::preferences('com_easysdi_service');
        }
        
		JToolBarHelper::divider();
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_core');
	}
}
