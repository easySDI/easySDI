<?php

require_once JPATH_COMPONENT.'\\'.'helpers'.'\\'.'easysdi_JLanguage.php';

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
        $easysdi_Language->load($option, JPATH_ADMINISTRATOR, $easysdi_Language->getDefault(), false, false);
        
        $strings = $easysdi_Language->getStrings();
        
        //print_r($strings);
        
        return $strings;
        
    }
 
}
?>