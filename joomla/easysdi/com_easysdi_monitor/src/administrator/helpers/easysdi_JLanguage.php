<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_monitor
 * @copyright	
 * @license		
 * @author		
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
