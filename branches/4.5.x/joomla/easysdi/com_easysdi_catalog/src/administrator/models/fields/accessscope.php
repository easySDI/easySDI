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

/**
 * Gives form fields and javascript functions to set access scope on an EasySDI object
 */
class JFormFieldAccessscope extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'Accessscope';

    /**
     * Method to get the field input markup for a generic list.
     * Use the multiple attribute to enable multiselect.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    protected function getInput() {
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';
        $attr .= $this->required ? ' required="required" aria-required="true"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptionsByQuery((string) $this->element['queryaccess']);

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $this->element['readonly'] == 'true') {
            $html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
        }
        // Create a regular list.
        else {
            $html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
        }
        $options = (array) $this->getOptionsByQuery((string) $this->element['queryorganism']);
        $html[] = JHtml::_('select.genericlist', $options, "organisms", trim($attr), 'value', 'text', null, null);

        $options = (array) $this->getOptionsByQuery((string) $this->element['queryuser']);
        $html[] = JHtml::_('select.genericlist', $options, "users", trim($attr), 'value', 'text', null, null);

        $html [] = '
function enableAccessScope(){
    // hide fields
    jQuery("#organisms, #users, #categories").hide();
    
    // public case
    if(jQuery("#jform_accessscope_id").val() == 1){
        // reset fields
        jQuery("#jform_users, #jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // organism case
    else if(jQuery("#jform_accessscope_id").val() == 2){
        jQuery("#organisms").show();
        // reset fields
        jQuery("#jform_users, #jform_categories").val("").trigger("liszt:updated");
    }
    // user case
    else if(jQuery("#jform_accessscope_id").val() == 3){
        jQuery("#users").show();
        // reset fields
        jQuery("#jform_organisms, #jform_categories").val("").trigger("liszt:updated");
    }
    // category case
    else if(jQuery("#jform_accessscope_id").val() == 4){
        jQuery("#categories").show();
        // reset fields
        jQuery("#jform_users, #jform_organisms").val("").trigger("liszt:updated");
    }
}
';
        return implode($html);
    }

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptionsByQuery($query) {
        $options = array();

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;


        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($translate == true) {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                } else {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
