<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelattributevalue extends sdiModel {

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_EASYSDI_CATALOG';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Attributevalue', $prefix = 'Easysdi_catalogTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Initialise variables.
        $app = JFactory::getApplication();

        $jform = new DOMDocument('1.0', 'utf-8');
        $jform->load(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/models/forms/attributevalue.xml');

        $item = $this->getItem();
        
        if ($item->stereotype_id == 10) {
            $this->removeNodeByName('value', $jform);
        } else {
            $this->removeNodeByName('text2', $jform);
        }

        // Get the form.
        $form = $this->loadForm('com_easysdi_catalog.attributevalue', $jform->saveXML(), array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.attributevalue.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     * @since	1.6
     */
    public function getItem($pk = null) {

        if ($item = parent::getItem($pk)) {

            //Do any procesing on fields here if needed
        }
        $app = JFactory::getApplication('administrator');
        $item->attribute_id = $app->getUserState('com_easysdi_catalog.attributevalues.filter.attribute', 'filter_attribute');
        $attributetable = JTable::getInstance('attribute', 'Easysdi_catalogTable');
        $attributetable->load($item->attribute_id);
        $item->attributename = $attributetable->name;
        $item->stereotype_id = $attributetable->stereotype_id;
        return $item;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @since	1.6
     */
    protected function prepareTable($table) {
        jimport('joomla.filter.output');

        if (empty($table->id)) {

            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('MAX(ordering)');
                $query->from('#__sdi_attributevalue');

                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * 
     * @param string $name
     * @param DOMDocument $dom
     * @return \DOMDocument
     */
    private function removeNodeByName($name, DOMDocument $dom) {
        $xpath = new DOMXPath($dom);
        $value_node = $xpath->query('//field[@name="' . $name . '"]')->item(0);
        $parent = $value_node->parentNode;
        $parent->removeChild($value_node);

        return $dom;
    }

}
