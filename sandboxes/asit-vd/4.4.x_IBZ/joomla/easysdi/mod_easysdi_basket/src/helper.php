<?php
/**
 * @version     4.4.5
 * @package     mod_easysdi_basket
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once JPATH_SITE. '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';

class modEasysdiBasketHelper {

    /**
     * Retrieves the hello message
     *
     * @param array $params An object containing the module parameters
     * @access public
     */
    public static function getBasketContent($params) {
        $content = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        
        if ($content && !empty($content) && !empty($content->extractions)){
            return count($content->extractions);
        }
        else {
            return '0';
        }
    }

}

?>