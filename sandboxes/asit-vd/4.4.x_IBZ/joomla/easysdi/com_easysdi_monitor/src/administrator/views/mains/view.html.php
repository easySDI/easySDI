<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_monitor.
 */
class Easysdi_monitorViewMains extends JViewLegacy {

    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $params = JComponentHelper::getParams('com_easysdi_monitor');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        Easysdi_monitorHelper::addSubmenu('mains');

        $this->addToolbar();


        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/easysdi_monitor.php';

        //$state	= $this->get('State');
        $canDo = Easysdi_monitorHelper::getActions('core.admin');

        JToolBarHelper::title(JText::_('COM_EASYSDI_MONITOR_TITLE_MAINS'), 'mains.png');
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/mains';

        //JToolBarHelper::divider();
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_easysdi_monitor');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_easysdi_monitor&view=mains');

        $this->sidebar = JHtmlSidebar::render();
    }

}
