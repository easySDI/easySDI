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
 * View class for a list of Easysdi_core.
 */
class Easysdi_contactViewUsers extends JViewLegacy {

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

        // Load the submenu.
        Easysdi_contactHelper::addSubmenu(JRequest::getCmd('view', 'users'));

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
        $state = $this->get('State');
        $canDo = Easysdi_contactHelper::getActions('user', $state->get('filter.category_id'), null);

        JToolBarHelper::title(JText::_('COM_EASYSDI_CONTACT_HEADER_USERS'), 'user.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/user';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('user.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit')) {
                JToolBarHelper::editList('user.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('users.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('users.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);                 
            } 
//            else {
//                //If this component does not use state then show a direct delete button as we can not trash
//                JToolBarHelper::deleteList('', 'users.delete', 'JTOOLBAR_DELETE');
//            }

            JToolBarHelper::deleteList('', 'users.delete', 'JTOOLBAR_DELETE');
             
//            if (isset($this->items[0]->state)) {
//                JToolBarHelper::divider();
//                JToolBarHelper::archiveList('users.archive', 'JTOOLBAR_ARCHIVE');
//            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('users.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        
        //Show trash and delete for components that uses the state field
//        if (isset($this->items[0]->state)) {
//            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
//                JToolBarHelper::deleteList('', 'users.delete', 'JTOOLBAR_EMPTY_TRASH');
//            } else if ($canDo->get('core.edit.state')) {
//                JToolBarHelper::trash('users.trash', 'JTOOLBAR_TRASH');
//            }
//        }

        JToolBarHelper::divider();
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_contact');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_contact&view=users');
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select('id as value, name as text')
                    ->from($db->quoteName('#__sdi_organism'))
                    ->where('state=1')
                    ->order('name');
        $db->setQuery($query);
        $organismsOptions = $db->loadObjectList();
        
        $this->extra_sidebar = 
                '<select name="filter_organism[]" id="filter_organism" class="span12 small" multiple onchange="this.form.submit()" data-placeholder="'.JText::_('JOPTION_SELECT_ORGANISM').'">'
                .JHtml::_('select.options', $organismsOptions, 'value', 'text', $this->state->get('filter.organism'))
                .'</select>';
        
        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',array('trash' => 0, 'archived' =>0 )), "value", "text", $this->state->get('filter.state'), true)
        );

         
        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
        );
    }

    protected function getSortFields() {
        return array(
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'u.name' => JText::_('COM_EASYSDI_CONTACT_USERS_USER_NAME'),
            'a.access_level' => JText::_('JGRID_HEADING_ACCESS'),
            'a.id' => JText::_('JGRID_HEADING_ID'),
        );
    }

}
