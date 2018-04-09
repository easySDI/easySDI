<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * 
 */
class Easysdi_errorHelper {
    
    /**
     * If toString is true, return a string of all error messages. If is false return an array of messages.
     * 
     * @param Exception $exc
     * @param boolean $toString
     * @param string $glue
     * @return mixed 
     */
    public static function getAncestors(Exception $exc, $toString = false, $glue='<br/>'){
        $messages = array();
        $messages[] = $exc->getMessage();
        $preview = $exc->getPrevious();
        
        while (isset($preview)) {
            $messages[] = $preview->getMessage();
            $preview = $preview->getPrevious();
        }
        
        if($toString){
            return implode($glue, $messages);
        }  else {
            return $messages;
        }
    }
    
}
