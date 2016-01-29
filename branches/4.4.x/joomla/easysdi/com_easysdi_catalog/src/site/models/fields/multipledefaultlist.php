<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
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
