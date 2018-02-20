<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_catalog.
 */
class Easysdi_catalogViewMetadatas extends JViewLegacy
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
        
		Easysdi_catalogHelper::addSubmenu('metadatas');
        
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
		require_once JPATH_COMPONENT.'/helpers/easysdi_catalog.php';

		$state	= $this->get('State');
		$canDo	= Easysdi_catalogHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_EASYSDI_CATALOG_TITLE_METADATAS'), 'metadatas.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/metadata';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('metadata.add','JTOOLBAR_NEW');
		    }

		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('metadata.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('metadatas.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('metadatas.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'metadatas.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('metadatas.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('metadatas.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'metadatas.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('metadatas.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_easysdi_catalog');
		}
        
        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_easysdi_catalog&view=metadatas');
        
        $this->extra_sidebar = '';
        
        
	}
    
	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.created_by' => JText::_('COM_EASYSDI_CATALOG_METADATAS_CREATED_BY'),
		'a.modified_by' => JText::_('COM_EASYSDI_CATALOG_METADATAS_MODIFIED_BY'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.metadatastate_id' => JText::_('COM_EASYSDI_CATALOG_METADATAS_METADATASTATE_ID'),
		'a.checked_out' => JText::_('COM_EASYSDI_CATALOG_METADATAS_CHECKED_OUT'),
		'a.checked_out_time' => JText::_('COM_EASYSDI_CATALOG_METADATAS_CHECKED_OUT_TIME'),
		'a.name' => JText::_('COM_EASYSDI_CATALOG_METADATAS_NAME'),
		'a.lastsynchronization' => JText::_('COM_EASYSDI_CATALOG_METADATAS_LASTSYNCHRONIZATION'),
		'a.synchronized_by' => JText::_('COM_EASYSDI_CATALOG_METADATAS_SYNCHRONIZED_BY'),
		);
	}

    
}
