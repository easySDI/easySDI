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
 * Supports an custom SQL select list
 *
 */
class JFormFieldSqlperimeterwithaccessscope extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'sqlperimeterwithaccessscope';

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
        $query = (string) $this->element['query'];
        
        // Get the database object.
        $db = JFactory::getDbo();

        //Check access scope
        try {
            $user = sdiFactory::getSdiUser();
            $organisms = $user->getDiffusionManagerOrganisms();
            $query .= ' AND (p.accessscope_id = 1 ';
            $query .= 'OR (p.accessscope_id = 3 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.organism_id = '. (int)$organisms[0]->id.' AND a.entity_guid = p.guid ) = 1)';
            $query .= 'OR (p.accessscope_id = 4 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.user_id = '. (int)$user->id.' AND a.entity_guid = p.guid ) = 1)';
            
            //TODO add category access scope
            $query .= ')';
        } catch (Exception $e) {
            //Can't handle accessscope restriction
            $query .= ' AND (p.accessscope_id = 10) '; //Query with no result at all
        }


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
