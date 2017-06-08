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
class Easysdi_catalogModelcatalog extends sdiModel {

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
    public function getTable($type = 'Catalog', $prefix = 'Easysdi_catalogTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_catalog.catalog', 'catalog', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.catalog.data', array());

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

            //Load searchsort
            $searchsorttable = $this->getTable('Searchsort', 'Easysdi_catalogTable', array());
            $rowssort = $searchsorttable->loadAll($item->id);
            if (is_array($rowssort)) {
                $item->searchsort = $rowssort['searchsort'];
            }

            //Load resourcetype
            $catalogresourcetype = JTable::getInstance('catalogresourcetype', 'Easysdi_catalogTable');
            $item->resourcetype_id = $catalogresourcetype->loadByCatalogID($item->id);
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
                $query->from('#__sdi_catalog');
                
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
        $isNew = ($data['id'] == 0) ? true : false;

        if (parent::save($data)) {
            //Get the element guid
            $item = parent::getItem($data['id']);
            $data['guid'] = $item->guid;
            $data['id'] = $item->id;

            //Save sorting fields
            $searchsorttable = $this->getTable('Searchsort', 'Easysdi_catalogTable', array());
            if (!$searchsorttable->saveAll($data)) {
                $this->setError($searchsorttable->getError());
                return false;
            }

            //Delete existing links for this catalog
            $catalogresourcetype = JTable::getInstance('catalogresourcetype', 'Easysdi_catalogTable');
            $catalogresourcetype->deleteByCatalogId($data['id']);

            if (is_array($data['resourcetype_id'])) {
                //Insert/update catalog resourcetype links
                foreach ($data['resourcetype_id'] as $resourcetype) {
                    $array = array();
                    $array['catalog_id'] = $data['id'];
                    $array['resourcetype_id'] = $resourcetype;
                    $catalogresourcetype = JTable::getInstance('catalogresourcetype', 'Easysdi_catalogTable');
                    $keys = array();
                    $keys['catalog_id'] = $data['id'];
                    $keys['resourcetype_id'] = $resourcetype;
                    $catalogresourcetype->load($keys);
                    $catalogresourcetype->save($array);
                }
            }

            //If it is a new catalog, save the default system search criterias
            if ($isNew) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('id');
                $query->from('#__sdi_searchcriteria');
                $query->where('issystem = 1');
                
                $db->setQuery($query);
                $searchcriterias = $db->loadColumn();
                foreach ($searchcriterias as $searchcriteria):
                    $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
                    $array = array();
                    $array['catalog_id'] = $data['id'];
                    $array['searchcriteria_id'] = $searchcriteria;
                    $array['searchtab_id'] = 4;
                    $array['state'] = 1;
                    $array['ordering'] = $searchcriteria;
                    $catalogsearchcriteria->save($array);
                endforeach;
//                for ($i = 1; $i <= 11; $i++) {
//                    $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
//                    $array = array();
//                    $array['catalog_id'] = $data['id'];
//                    $array['searchcriteria_id'] = $i;
//                    $array['searchtab_id'] = 4;
//                    $array['state'] = 1;
//                    $array['ordering'] = $i;
//                    $catalogsearchcriteria->save($array);
//                }
            }

            return true;
        }

        return false;
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     *
     * @since   12.2
     */
    public function delete(&$pks) {
        $item = parent::getItem($pks[0]);
        $guid = $item->guid;
        $id = $item->id;
        if (parent::delete($pks)) {

            $searchsorttable = $this->getTable('Searchsort', 'Easysdi_catalogTable', array());
            if (!$searchsorttable->deleteAll($id)) {
                $this->setError($searchsorttable->getError());
                return false;
            }
            return true;
        }
        return false;
    }

}