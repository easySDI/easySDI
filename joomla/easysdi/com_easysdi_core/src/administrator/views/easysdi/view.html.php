<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View 
 */
class Easysdi_coreViewEasysdi extends JViewLegacy {

    protected $form;

    /**
     * Display the view
     */
    function display($tpl = null) {
        // Assign data to the view
        $this->form = $this->get('Form');

        //Check if others easysdi components are installed
        $app = JFactory::getApplication();
        // Get the user object to verify permissions
        $user = JFactory::getUser();


        //home
        JHtmlSidebar::addEntry(
                '<i class="icon-home"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_HOME'), 'index.php?option=com_easysdi_core&view=easysdi',true
        );


        //com_easysdi_user
        if ($app->getUserState('com_easysdi_contact-installed') && $user->authorise('core.manage', 'com_easysdi_contact')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-user"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_CONTACT'), 'index.php?option=com_easysdi_contact'
            );
        }

        //com_easysdi_catalog
        if ($app->getUserState('com_easysdi_catalog-installed') && $user->authorise('core.manage', 'com_easysdi_catalog')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-grid-view"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_CATALOG'), 'index.php?option=com_easysdi_catalog'
            );
        }

        //com_easysdi_shop
        if ($app->getUserState('com_easysdi_shop-installed') && $user->authorise('core.manage', 'com_easysdi_shop')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-basket"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_SHOP'), 'index.php?option=com_easysdi_shop'
            );
        }

        //com_easysdi_service
        if ($app->getUserState('com_easysdi_service-installed') && $user->authorise('core.manage', 'com_easysdi_service')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-wrench"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_SERVICE'), 'index.php?option=com_easysdi_service'
            );
        }

        //com_easysdi_map
        if ($app->getUserState('com_easysdi_map-installed') && $user->authorise('core.manage', 'com_easysdi_map')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-location"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_MAP'), 'index.php?option=com_easysdi_map'
            );
        }

        //com_easysdi_monitor
        if ($app->getUserState('com_easysdi_monitor-installed') && $user->authorise('core.manage', 'com_easysdi_monitor')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-health"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_MONITOR'), 'index.php?option=com_easysdi_monitor'
            );
        }

        //com_easysdi_dashboard
        if ($app->getUserState('com_easysdi_dashboard-installed') && $user->authorise('core.manage', 'com_easysdi_dashboard')) {
            JHtmlSidebar::addEntry(
                    '<i class="icon-dashboard"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_DASHBOARD'), 'index.php?option=com_easysdi_dashboard'
            );
        }

        $this->addToolbar();

        $this->moduleseasysdi_left = JModuleHelper::getModules('easysdi_adm_home_left');
        $this->moduleseasysdi_right = JModuleHelper::getModules('easysdi_adm_home_right');

        // Display the view
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/easysdi_core.php';
        JToolBarHelper::title(JText::_('COM_EASYSDI_CORE_TITLE'), 'easysdi.png');

        $canDo = Easysdi_coreHelper::getActions();

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_core');
        }

        // Render side bar
        $this->sidebar = JHtmlSidebar::render();
    }

}
