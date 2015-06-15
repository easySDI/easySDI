<?php

/**
 * @version     3.3.0
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_monitor helper.
 */
class Easysdi_monitorHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
                '<i class="icon-home"></i> ' . JText::_('COM_EASYSDI_MONITOR_TITLE_HOME'), 'index.php?option=com_easysdi_core&view=easysdi', $vName == 'easysdi'
        );
        JHtmlSidebar::addEntry(
                JText::_('COM_EASYSDI_MONITOR_TITLE_MAINS'), 'index.php?option=com_easysdi_monitor&view=mains', $vName == 'mains'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_easysdi_monitor';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

}
