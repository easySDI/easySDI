<?php

defined('_JEXEC') or die;

abstract class ModEasysdi_adminbuttonHelper {

    public static function getList($params) {
        $context = $params->get('context', 'mod_easysdi_adminbutton');
        // Include buttons defined by published easysdi_admin_button plugins
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
