<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_easysdi_admininfo
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_easysdi_admininfo
 *
 * @package     Joomla.Administrator
 * @subpackage  mod_easysdi_admininfo
 */
abstract class Modeasysdi_admininfoHelper {

    /**
     * Get a list of easysdi_admininfo users.
     *
     * @param   JRegistry  $params  The module parameters.
     *
     * @return  mixed  An array of users, or false on error.
     */
    public static function getList($params) {
        $context = $params->get('context', 'mod_easysdi_admininfo');
        // Include buttons defined by published quickicon plugins
        $test = JPluginHelper::importPlugin('easysdi_admin_info');
        //print_r($test);
        $app = JFactory::getApplication();
        $arrays = (array) $app->triggerEvent('onGetAdminInfos', array($context));
        //print_r($arrays);
        foreach ($arrays as $icon) {
            $default = array(
                'link' => null,
                'image' => 'cog',
                'text' => null,
                'access' => true
            );
            
            $icon = array_merge($default, $icon);
            if (!is_null($icon['link']) && !is_null($icon['text'])) {
                self::$buttons[$key][] = $icon;
            }
        }
        return $arrays;
    }

}
