<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

abstract class ModEasysdi_adminbuttonHelper {

    public static function getList($params) {
        $context = $params->get('context', 'mod_easysdi_adminbutton');
        JPluginHelper::importPlugin('easysdi_admin_button');
        $app = JFactory::getApplication();
        
        $arrays = (array) $app->triggerEvent('quickButton', array($context));
        
        //Add default values
        $default = array(
            'info' => null,
            'link' => null,
            'text' => null,
            'state' => null,
            'btntooltip' => null,
            'badgetooltip' => null
        );
        foreach ($arrays as &$button) {
            $button = array_merge($default, $button);
        }
        //Return the buttons array
        return $arrays;
    }

}
