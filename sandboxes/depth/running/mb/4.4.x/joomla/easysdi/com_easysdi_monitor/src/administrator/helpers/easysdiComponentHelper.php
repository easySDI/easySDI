<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once JPATH_COMPONENT.'/'.'helpers'.'/'.'easysdi_JLanguage.php';

class easysdiComponentHelper
{   
    /**
     * 2B implemented in JComponentHelper
     * @see JComponenthelper::renderComponent()
     *
     * This doesn't really render the component =;)
     *
     * @return strings array contains all translation
     */
    public static function getStrings($option, $params = array())
    {

        $easysdi_Language = new easysdi_JLanguage(JFactory::getConfig()->get('language'), true);
 
        //-- Load the javascript language ini file - only to get the keys
        $easysdi_Language->load($option, JPATH_ADMINISTRATOR, JFactory::getConfig()->get('language'), false, false);
        
        $strings = $easysdi_Language->getStrings();
        
        //print_r($strings);
        
        return $strings;
        
    }
    
    
 
}
?>