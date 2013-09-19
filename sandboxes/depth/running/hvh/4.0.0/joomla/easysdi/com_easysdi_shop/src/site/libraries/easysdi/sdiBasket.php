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

require_once JPATH_COMPONENT . "/libraries/easysdi/sdiExtraction.php";
require_once JPATH_COMPONENT . "/libraries/easysdi/sdiPerimeter.php";

class sdiBasket {

    var $extractions;
    var $perimeters;
    var $extent;
    var $isrestrictedbyperimeter;

    function __construct($session_content) {
        if (empty($session_content))
            return;

        $this->isrestrictedbyperimeter = false;

        if (!isset($this->extractions))
            $this->extractions = array();

        if (!isset($this->perimeters))
            $this->perimeters = array();

        if (isset($session_content->extractions)) {
            foreach ($session_content->extractions as $extraction):
                $ex = new sdiExtraction($extraction);
                if ($ex->restrictedperimeter == '1')
                    $this->isrestrictedbyperimeter = true;
                $this->extractions[] = $ex;
            endforeach;
        }

        if (isset($session_content->perimeters)) {
            foreach ($session_content->perimeters as $perimeter):
                $this->perimeters[] = new sdiPerimeter($perimeter);
            endforeach;
            
            foreach ($this->perimeters as $perimeter):
                $perimeter->setAllowedBuffer($this->extractions);
            endforeach;
        }
        
        if(isset ($session_content->extent))
            $this->extent = $session_content->extent;
    }

}

?>
