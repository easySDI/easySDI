<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_core helper.
 */
class Easysdi_coreHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        JHtmlSidebar::addEntry(
                        JText::_('COM_EASYSDI_CORE_TITLE_NAMESPACES'),
                        "index.php?option=com_easysdi_core&view=namespaces",
                        $vName == 'namespaces'
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

        $assetName = 'com_easysdi_core';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function uuid() {
        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
                mt_rand(0, 65535), // 16 bits for "time_mid"
                mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
                bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
                // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
                // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
                // 8 bits for "clk_seq_low"
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );
    }

}
