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
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewPhysicalServices extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->categories = $this->get('CategoryOrders');
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->connector = $this->get('Connector');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id as value, value as text');
        $query->from('#__sdi_sys_serviceconnector');
        $query->where('state=1');
        
        $db->setQuery($query);
        $this->connectorlist = $db->loadObjectList();

        Easysdi_serviceHelper::addSubmenu('physicalservices');
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
        require_once JPATH_COMPONENT . '/helpers/easysdi_service.php';

        $state = $this->get('State');

        $canDo = Easysdi_serviceHelper::getActionsPhysicalService($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_HEADER_PHYSICALSERVICES'), 'links-cat.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/physicalservice';

        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                // JToolBarHelper::addNew('physicalservice.add','JTOOLBAR_NEW');
                //Create custom button with a dropdown list allowing connector type selection for new virtualservice action
                $dropdown = '<button class="btn dropdown-toggle btn-small btn-success" data-toggle="dropdown"><span class="icon-new icon-white"> </span>' . JText::_('JTOOLBAR_NEW') . '</button>
            	<ul class="dropdown-menu">';
                
                foreach ($this->connector as $connector) {
                    $dropdown .= '<li><a href="index.php?option=com_easysdi_service&view=physicalservice&serviceconnector_id=' . $connector->id . '">' . $connector->value . '</a></li>';
                }
                $dropdown .= '</ul>';

                $bar = JToolbar::getInstance('toolbar');
                $bar->appendButton('Custom', $dropdown, 'new');
            }

            if ($canDo->get('core.edit')) {
                JToolBarHelper::editList('physicalservice.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('physicalservices.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('physicalservices.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'physicalservices.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('physicalservices.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('physicalservices.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'physicalservices.delete', 'JTOOLBAR_EMPTY_TRASH');
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('physicalservices.trash', 'JTOOLBAR_TRASH');
            }
        }

        JToolBarHelper::divider();
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_service');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_service&view=physicalservices');
        $this->extra_sidebar = '';
        JHtmlSidebar::addFilter(
                JText::_('COM_EASYSDI_SERVICE_VIRTUALSERVICES_SELECT_CONNECTOR'), 'filter_connector', JHtml::_('select.options', $this->connector, "id", "value", $this->state->get('filter.connector'), true)
        );
        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
        );
        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_CATEGORY'), 'filter_category_id', JHtml::_('select.options', JHtml::_('category.options', 'com_easysdi_service'), 'value', 'text', $this->state->get('filter.category_id'))
        );
        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
        );
    }

    protected function getSortFields() {
        return array(
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'a.name' => JText::_('COM_EASYSDI_SERVICE_VIRTUALSERVICES_NAME'),
            'serviceconnector' => JText::_('COM_EASYSDI_SERVICE_PHYSICALSERVICES_CONNECTOR'),
            'category_title' => JText::_('JCATEGORY'),
            'a.access' => JText::_('JGRID_HEADING_ACCESS'),
            'a.id' => JText::_('JGRID_HEADING_ID'),
        );
    }

}
