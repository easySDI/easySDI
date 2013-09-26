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

    public $id;
    public $name;
    public $buffer;
    public $thirdparty;
    public $extractions= array();
    public $perimeters= array();
    public $extent;
    public $isrestrictedbyperimeter = false;
    public $surfacemin;
    public $surfacemax;
    public $free = true;
    public $visualization = '';

    function __construct() {
        $this->sdiUser = sdiFactory::getSdiUser();
    }
    
    function loadOrder ($orderId){
        
    }

    function addExtraction($extraction) {        
        $this->setProperties ($extraction);
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
        $this->free = true;
        $this->visualization = '';
        foreach ($this->extractions as $key => $extraction):
            $this->setProperties ($extraction);
        endforeach;
    }

    function setPerimeters($perimeters) {
        $this->perimeters = $perimeters;
        foreach ($this->perimeters as $perimeter):
            $perimeter->setAllowedBuffer($this->extractions);
        endforeach;
    }
    
    function setProperties ($extraction){
        if (!empty ($extraction->visualization)):
            if ($this->sdiUser->canView($extraction->visualization))
                $this->visualization .= $extraction->visualization .',';
        endif;
        if ($extraction->restrictedperimeter == '1')
            $this->isrestrictedbyperimeter = true;

        if ((empty($this->surfacemin) && !empty($extraction->surfacemin)) || (!empty($extraction->surfacemin) && $extraction->surfacemin > $this->surfacemin))
            $this->surfacemin = $extraction->surfacemin;

        if ((empty($this->surfacemax) && !empty($extraction->surfacemax)) || (!empty($extraction->surfacemax) && $extraction->surfacemax < $this->surfacemax))
            $this->surfacemax = $extraction->surfacemax;
        
        if($extraction->pricing == 2)
            $this->free = false;
    }
}

?>
