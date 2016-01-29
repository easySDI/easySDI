<?php
/**
 * @version		4.4.0
 * @package     mod_easysdi_admininfo
 * @copyright	
 * @license		
 * @author		
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
