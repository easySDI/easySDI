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

/**
 * Supports an custom SQL select list
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldResourcetypeSQL extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'ResourcetypeSQL';

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

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;

        //Allowed resourcetype as children
        $id = JFactory::getApplication()->getUserState('com_easysdi_core.edit.version.id');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('rtchild.id as id, rtchild.name as name, rtchild.ordering')
                ->from('#__sdi_resourcetype rtchild')
                ->innerJoin('#__sdi_resourcetypelink rtl ON rtl.child_id = rtchild.id')
                ->innerJoin('#__sdi_resourcetype rt ON rt.id= rtl.parent_id')
                ->innerJoin('#__sdi_resource r ON r.resourcetype_id = rt.id')
                ->innerJoin('#__sdi_version v ON v.resource_id = r.id')
                ->where('v.id = ' . (int) $id)
                ->where('rtchild.state = 1')
                ->order('ordering')
        ;

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                $options[] = JHtml::_('select.option', '', null);
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
