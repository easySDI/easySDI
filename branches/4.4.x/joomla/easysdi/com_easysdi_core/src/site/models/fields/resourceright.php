<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';

/**
 * Supports an custom SQL select list
 *
 */
class JFormFieldResourceRight extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'ResourceRight';

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions() {
        $options = array();
        
        $name = $this->element['name'];
        
        $sessionData = JFactory::getApplication()->getUserState('com_easysdi_core.edit.resource.ur[rights_'.$name.']');
        
        if($sessionData !== null){
            $this->element['data-orig'] = implode(',', $sessionData);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
