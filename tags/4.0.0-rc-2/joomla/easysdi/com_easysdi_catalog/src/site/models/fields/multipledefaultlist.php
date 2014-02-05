<?php



/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
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
