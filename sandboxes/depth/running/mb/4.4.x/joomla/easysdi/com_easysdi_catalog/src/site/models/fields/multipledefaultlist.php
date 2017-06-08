<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldMultipleDefaultList extends JFormFieldList {

    public $type = 'MultipleDefaultList';
    
    protected function getInput() {
        if ($this->multiple) {
            if(strpos($this->value, ',')!==false){
                $this->value = explode(',', $this->value);
            }
        }

        return parent::getInput();
    }

}
