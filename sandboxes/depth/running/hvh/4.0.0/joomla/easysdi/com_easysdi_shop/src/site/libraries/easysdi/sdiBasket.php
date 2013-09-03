<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT."/libraries/easysdi/sdiExtraction.php";
require_once JPATH_COMPONENT."/libraries/easysdi/sdiPerimeter.php";

class sdiBasket {
    
    var $extractions;    
    var $perimeters;
    var $extent;
    
    function __construct($session_content) {
        if(empty($session_content))
            return;
        
        if (!isset($this->extractions))
            $this->extractions = array();
        
        if (!isset($this->perimeters))
            $this->perimeters = array();
        
        foreach($session_content->extractions as $extraction):
            $this->extractions[] = new sdiExtraction($extraction);
        endforeach;
        
        foreach($session_content->perimeters as $perimeter):
            $this->perimeters[] = new sdiPerimeter($perimeter);
        endforeach;
    }

}

?>
