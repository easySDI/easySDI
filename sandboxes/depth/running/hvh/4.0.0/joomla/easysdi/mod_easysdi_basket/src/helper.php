<?php

/**
 * Helper class for Hello World! module
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://dev.joomla.org/component/option,com_jd-wiki/Itemid,31/id,tutorials:modules/
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class modEasysdiBasketHelper {

    /**
     * Retrieves the hello message
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
    public static function getBasketContent($params) {
        $content = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        
        if (!empty($content)){
            return count($content->extractions);
        }
        else {
            return 'Empty!';
        }
    }

}

?>