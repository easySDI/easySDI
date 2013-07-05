<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_catalog.
 */
class Easysdi_catalogViewAttribute_values extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->attributename = $this->get('AttributeName');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        Easysdi_catalogHelper::addSubmenu('attribute_values');
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
        require_once JPATH_COMPONENT . '/helpers/easysdi_catalog.php';

        $state = $this->get('State');
        $canDo = Easysdi_catalogHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_EASYSDI_CATALOG_TITLE_ATTRIBUTE_VALUES') . $this->attributename, 'attribute_values.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/attribute_value';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('attribute_value.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('attribute_value.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('attribute_values.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('attribute_values.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'attribute_values.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('attribute_values.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('attribute_values.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'attribute_values.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('attribute_values.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        JToolBarHelper::divider();
        JToolBarHelper::custom('attributes', 'chevron-left.png', 'chevron-left_f2.png', 'COM_EASYSDI_CATALOG_TOOLBAR_BACK', false);

        JToolBarHelper::divider();
        JToolBarHelper::custom('easySDIHome', 'home.png', 'home_f2.png', 'COM_EASYSDI_CATALOG_TOOLBAR_HOME', false);

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_catalog&view=attribute_values');

        $this->extra_sidebar = '';

        JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
        );
    }

    protected function getSortFields() {
        return array(
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'a.name' => JText::_('COM_EASYSDI_CATALOG_BOUNDARIESCATEGORY_NAME'),
            'a.id' => JText::_('JGRID_HEADING_ID'),
        );
    }

}
