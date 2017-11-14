<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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
