<?php
/**
 * @version     4.0.0
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

abstract class Modeasysdi_admininfoHelper {

    public static function getList($params) {
        $context = $params->get('context', 'mod_easysdi_admininfo');
        JPluginHelper::importPlugin('easysdi_admin_info');
        $app = JFactory::getApplication();
        $arrays = (array) $app->triggerEvent('onGetAdminInfos', array($context));

        //Add default values
        $default = array(
            'info' => null,
            'text' => null
        );
        foreach ($arrays as &$icon) {           
            $icon = array_merge($default, $icon);
        }
        return $arrays;
    }

}
