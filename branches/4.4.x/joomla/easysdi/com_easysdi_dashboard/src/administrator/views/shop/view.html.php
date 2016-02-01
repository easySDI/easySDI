<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_dashboard.
 */
class Easysdi_dashboardViewShop extends JViewLegacy {

    protected $state;
    

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $this->state = $this->get('State');
        
        $params = JComponentHelper::getParams('com_easysdi_dashboard');
        $this->talendenabled =  $params->get('talendenabled');
        $this->birtenabled =  $params->get('birtenabled');
        $this->birturl =  $params->get('birturl');
        $this->graphcolours =  $params->get('graphcolours');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        Easysdi_dashboardHelper::addSubmenu('shop');
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
        require_once JPATH_COMPONENT . '/helpers/easysdi_dashboard.php';

        //$state = $this->get('State');

        $canDo = Easysdi_dashboardHelper::getActions('core.admin');

        JToolBarHelper::title(JText::_('COM_EASYSDI_DASHBOARD_SHOP_HEADER'), 'links-cat.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/shop';


        JToolBarHelper::divider();
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_dashboard');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_dashboard&view=shop');
        $this->extra_sidebar = Easysdi_dashboardHelper::getPseudoFilters();
 
        //print_r($this);
        
    }
}
