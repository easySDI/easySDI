<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MonitorLanguage
 *
 * @author Depth
 */
/**
 * The purpose of this class is to provide access to
 * the internal (protected) array $strings of JLanguage
 */
class easysdi_JLanguage extends JLanguage
{
    public function __construct($lang = null, $debug = false) {
        parent::__construct($lang, $debug);
    }
    
    public function getStrings()
    {
        return $this->strings;
    }//function
    
}//class

?>
