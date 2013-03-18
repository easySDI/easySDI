<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewPolicies extends JViewLegacy
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
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		Easysdi_serviceHelper::addSubmenu('policies');
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
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		
		$virtualserviceModel 	= JModelLegacy::getInstance('virtualservices', 'Easysdi_serviceModel', array());
		$virtualserviceList 	= $virtualserviceModel->getItemsRestricted();
		
		$state	= $this->get('State');
		$canDo	= Easysdi_serviceHelper::getActionsPolicy();

		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_HEADER_POLICIES'), 'policies.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'policy';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
		    	//Create custom button with a dropdown list allowing virtual service selection for new policy action
		    	$dropdown = '<button class="btn dropdown-toggle btn-small btn-success" data-toggle="dropdown"><i class="icon-new icon-white"> '.JText::_('JTOOLBAR_NEW').'</i></button>
		    	<ul class="dropdown-menu">';
		    	foreach ($virtualserviceList as $virtualservice){
		    		$dropdown .= '<li><a href="index.php?option=com_easysdi_service&view=policy&layout='.$virtualservice->connector.'&virtualservice_id='.$virtualservice->id.'">'.$virtualservice->name.'</a></li>';
		    	}
		    	$dropdown .= '</ul>';
		    
		    	$bar = JToolbar::getInstance('toolbar');
		    	$bar->appendButton('Custom',$dropdown, 'new');
		    }

		    if ($canDo->get('core.edit')) {
			    JToolBarHelper::editList('policy.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('policies.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('policies.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'policies.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('policies.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('policies.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}
        
        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'policies.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('policies.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_easysdi_service');
		}
		
		JToolBarHelper::divider();
		JToolBarHelper::custom('easySDIHome', 'home.png', 'home_f2.png', 'COM_EASYSDI_SERVICE_TOOLBAR_HOME', false);
		
		//Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_easysdi_service&view=policies');
		$this->extra_sidebar = '';
		
		JHtmlSidebar::addFilter(
				JText::_('COM_EASYSDI_SERVICE_POLICIES_SELECT_VIRTUALSERVICE'),
				'filter_virtualservice',
				JHtml::_('select.options', $virtualserviceList, "id", "name", $this->state->get('filter.virtualservice'), true)
		);
		JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);
		JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_ACCESS'),
				'filter_access',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);
	}
	
	protected function getSortFields()
	{
		return array(
				'a.id' => JText::_('JGRID_HEADING_ID'),
				'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
				'a.state' => JText::_('JSTATUS'),
				'a.name' => JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_NAME'),
				'a.virtualservice_id' => JText::_('COM_EASYSDI_SERVICE_FORM_LBL_POLICY_VIRTUALSERVICE'),
				'a.access' => JText::_('JGRID_HEADING_ACCESS'),
		);
	}
}
