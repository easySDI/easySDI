<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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

        // Get the user object to verify permissions
        $user = JFactory::getUser();

        Easysdi_coreHelper::addSubmenu();

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
