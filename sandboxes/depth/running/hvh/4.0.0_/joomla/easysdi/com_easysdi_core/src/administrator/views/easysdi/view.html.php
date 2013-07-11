<?php

/**
 * @version     4.0.0
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
        $this->buttons = array();

        //com_easysdi_user
        if ($app->getUserState('com_easysdi_contact-installed')) {
            array_push($this->buttons, array(
                'link' => JRoute::_('index.php?option=com_easysdi_contact'),
                'image' => '48-easysdi-contact',
                'text' => JText::_('COM_EASYSDI_CORE_ICON_SDI_CONTACT'),
                'access' => array('core.manage', 'com_easysdi_contact')
            ));
        }

        //com_easysdi_catalog
        if ($app->getUserState('com_easysdi_catalog-installed')) {
            array_push($this->buttons, array(
                'link' => JRoute::_('index.php?option=com_easysdi_catalog'),
                'image' => '48-easysdi-catalog',
                'text' => JText::_('COM_EASYSDI_CORE_ICON_SDI_CATALOG'),
                'access' => array('core.manage', 'com_easysdi_catalog')
            ));
        }

        //com_easysdi_service
        if ($app->getUserState('com_easysdi_service-installed')) {
            array_push($this->buttons, array(
                'link' => JRoute::_('index.php?option=com_easysdi_service'),
                'image' => '../../../templates/hathor/images/header/icon-48-links.png',
                'text' => JText::_('COM_EASYSDI_CORE_ICON_SDI_SERVICE'),
                'access' => array('core.manage', 'com_easysdi_service')
            ));
        }

        //com_easysdi_map
        if ($app->getUserState('com_easysdi_map-installed')) {
            array_push($this->buttons, array(
                'link' => JRoute::_('index.php?option=com_easysdi_map'),
                'image' => '../../../templates/hathor/images/header/icon-48-language.png',
                'text' => JText::_('COM_EASYSDI_CORE_ICON_SDI_MAP'),
                'access' => array('core.manage', 'com_easysdi_map')
            ));
        }

        //com_easysdi_monitor
        if ($app->getUserState('com_easysdi_monitor-installed')) {
            array_push($this->buttons, array(
                'link' => JRoute::_('index.php?option=com_easysdi_monitor'),
                'image' => '../../../templates/hathor/images/header/icon-48-language.png',
                'text' => JText::_('COM_EASYSDI_CORE_ICON_SDI_MONITOR'),
                'access' => array('core.manage', 'com_easysdi_monitor')
            ));
        }

        // Display the view
        $this->addToolbar();
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
    }

}