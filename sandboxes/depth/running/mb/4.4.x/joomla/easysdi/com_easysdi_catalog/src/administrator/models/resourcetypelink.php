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

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelresourcetypelink extends JModelAdmin {

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
    public function getTable($type = 'Resourcetypelink', $prefix = 'Easysdi_catalogTable', $config = array()) {
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

        // Get the form.
        $form = $this->loadForm('com_easysdi_catalog.resourcetypelink', 'resourcetypelink', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.resourcetypelink.data', array());

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
            //Load resourcetypelinkinheritance
            $resourcetypelinkinheritance = JTable::getInstance('resourcetypelinkinheritance', 'Easysdi_catalogTable');
            $item->resourcetypelinkinheritance = $resourcetypelinkinheritance->loadByResourceTypeLinkID($item->id);
        }

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
                $query->from('#__sdi_resourcetypelink');
                
                $db->setQuery($query);
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   12.2
     */
    public function save($data) {

        if (parent::save($data)) {
            
            $item = parent::getItem($data['id']);
            $data['id'] = $item->id;
 
            //Delete existing inheritance
            $resourcetypelinkinheritance = JTable::getInstance('resourcetypelinkinheritance', 'Easysdi_catalogTable');
            $resourcetypelinkinheritance->deleteByResourceTypeLinkId($data['id']);

            $jinput = JFactory::getApplication()->input;
            $jform = $jinput->get('jform', null, 'array');

            if ($data['inheritance'] == 1 && is_array($jform['xpath'])) {
                //Insert/update catalog resourcetype links
                foreach ($jform['xpath'] as $xpath) {
                    $array = array();
                    $array['resourcetypelink_id'] = $data['id'];
                    $array['xpath'] = $xpath;
                    $resourcetypelinkinheritance = JTable::getInstance('resourcetypelinkinheritance', 'Easysdi_catalogTable');

                    $resourcetypelinkinheritance->save($array);
                }
            }

            return true;
        }

        return false;
    }

}