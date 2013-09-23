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

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';

class sdiBasket {

    var $id;
    var $name;
    var $buffer;
    var $thirdparty;
    var $extractions;
    var $perimeters;
    var $extent;
    var $isrestrictedbyperimeter;
    var $surfacemin;
    var $surfacemax;
    
    function __construct() {
        $this->extractions = array();
        $this->perimeters = array();
        $this->isrestrictedbyperimeter = false;
    }

    function addExtraction($extraction) {
        if ($extraction->restrictedperimeter == '1')
            $this->isrestrictedbyperimeter = true;

        if ((empty($this->surfacemin) && !empty($extraction->surfacemin)) || (!empty($extraction->surfacemin) && $extraction->surfacemin > $this->surfacemin))
            $this->surfacemin = $extraction->surfacemin;

        if ((empty($this->surfacemax) && !empty($extraction->surfacemax)) || (!empty($extraction->surfacemax) && $extraction->surfacemax < $this->surfacemax))
            $this->surfacemax = $extraction->surfacemax;
        $this->extractions[] = $extraction;
    }

    function removeExtraction($id) {
        foreach ($this->extractions as $key => $extraction):
            if ($extraction->id == $id) {
                unset($this->extractions[$key]);
                break;
            }
        endforeach;

        $this->isrestrictedbyperimeter = false;
        foreach ($this->extractions as $key => $extraction):
            if ($extraction->restrictedperimeter == '1')
                $this->isrestrictedbyperimeter = true;
            
            if ((empty($this->surfacemin) && !empty($extraction->surfacemin)) || (!empty($extraction->surfacemin) && $extraction->surfacemin > $this->surfacemin))
                $this->surfacemin = $extraction->surfacemin;

            if ((empty($this->surfacemax) && !empty($extraction->surfacemax)) || (!empty($extraction->surfacemax) && $extraction->surfacemax < $this->surfacemax))
                $this->surfacemax = $extraction->surfacemax;
        endforeach;
    }

    function setPerimeters($perimeters) {
        $this->perimeters = $perimeters;
        foreach ($this->perimeters as $perimeter):
            $perimeter->setAllowedBuffer($this->extractions);
        endforeach;
    }

}

?>
