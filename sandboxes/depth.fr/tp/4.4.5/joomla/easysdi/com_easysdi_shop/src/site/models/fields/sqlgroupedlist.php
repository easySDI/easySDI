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

/**
 * Supports an custom SQL select list, with groups
 * Query MUST order elements first.
 */
class JFormFieldSqlgroupedlist extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'sqlgroupedlist';

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     * Query MUST order elements.
     * 
     *
     * @return  array  The field option objects.
     *
     */
    protected function getOptions() {
        $options = array();

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $groupname = $this->element['groupname_field'] ? (string) $this->element['groupname_field'] : (string) $this->element['groupname'];
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        $query = (string) $this->element['query'];

        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        $prevGroup = null;
        

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                //group change
                if ($prevGroup != $item->$groupname) {
                    
                    //if not the first, close the previous group
                    if ($prevGroup != null) {
                        $options[] = JHtml::_('select.optgroup', $prevGroup);
                    }
                    //open new group, and save current
                    $options[] = JHtml::_('select.optgroup', $item->$groupname);
                    $prevGroup = $item->$groupname;
                }
                //Add element
                if ($translate == true) {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                } else {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
            //Close last group
            $options[] = JHtml::_('select.optgroup', $prevGroup);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
