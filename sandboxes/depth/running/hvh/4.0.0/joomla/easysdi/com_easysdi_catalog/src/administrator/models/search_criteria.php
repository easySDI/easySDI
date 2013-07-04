<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelsearch_criteria extends sdiModel {

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
    public function getTable($type = 'Search_criteria', $prefix = 'Easysdi_catalogTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_catalog.search_criteria', 'search_criteria', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.search_criteria.data', array());

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
            $app = JFactory::getApplication('administrator');
            $catalog = $app->getUserStateFromRequest('com_easysdi_catalog.search_criterias.filter.catalog', 'filter_catalog');
            if (isset($catalog)) {
                //Merge search_criteria object with catalog_searchcriteria object
                $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
                $keys = array();
                $keys['catalog_id'] = $catalog;
                $keys['searchcriteria_id'] = $item->id;
                $catalogsearchcriteria->load($keys);
                $item->catalogsearchcriteria_id = $catalogsearchcriteria->id;
                $item->searchtab_id = $catalogsearchcriteria->searchtab_id;
                $item->defaultvalue = $catalogsearchcriteria->defaultvalue;
                $item->from = $catalogsearchcriteria->defaultvaluefrom;
                $item->to = $catalogsearchcriteria->defaultvalueto;

                //Load translations
                $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
                $rows = $translationtable->loadAll($catalogsearchcriteria->guid);
                if (is_array($rows)) {
                    if (isset($rows['text1']))
                        $item->text1 = $rows['text1'];
                    if (isset($rows['text2']))
                        $item->text2 = $rows['text2'];
                }

                if ($item->id == 2) {
                    //Resource type criteria
                    $item->resourcetype_id = json_decode($catalogsearchcriteria->defaultvalue, true);
                }
                
                if ($item->id == 3) {
                    //Version criteria
                    $item->version = $catalogsearchcriteria->defaultvalue;
                }
                
                if ($item->id == 7) {
                    //Resource type criteria
                    $item->organism_id = json_decode($catalogsearchcriteria->defaultvalue, true);
                }
                
                if ($item->id == 9 || $item->id == 10 || $item->id == 11) {
                    //Version criteria
                    $item->is = $catalogsearchcriteria->defaultvalue;
                }
                
                if ($item->criteriatype_id == 2) {
                    //Search criteria on a relation
                    $relation = JTable::getInstance('relation', 'Easysdi_catalogTable');
                    $relation->load($item->relation_id);
                    $attribute = JTable::getInstance('attribute', 'Easysdi_catalogTable');
                    $attribute->load($relation->attributechild_id);
                    $item->attributestereotype_id = $attribute->stereotype_id;
                    if ($item->attributestereotype_id == 6) {
                        //Retreive attribute list values
                        $db = JFactory::getDbo();
                        $db->setQuery('SELECT av.id , av.value
                        FROM #__sdi_attributevalue av 
                        WHERE av.attribute_id=' . $attribute->id . ' 
                        ORDER BY av.value');
                        $item->attributevalues = $db->loadObjectList();
                        //Decode defaultvalue
                        $item->defaultvalues = json_decode($catalogsearchcriteria->defaultvalue, true);
                    }
                }
            }
        }
        if ($item->criteriatype_id == NULL) {
            $item->criteriatype_id = 3;
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
                $db->setQuery('SELECT MAX(ordering) FROM #__sdi_searchcriteria');
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

            //Save default value in catalogsearchcriteria object
            $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
            $catalogsearchcriteria->load($data['catalogsearchcriteria_id']);

            $array = array();
            $array['id'] = $data['catalogsearchcriteria_id'];
            $array['searchtab_id'] = $data['searchtab_id'];

            if (isset($data['defaultvalues']))
                $array['defaultvalue'] = json_encode($data['defaultvalues']);
            else if (isset($data['resourcetype_id']))
                $array['defaultvalue'] = json_encode($data['resourcetype_id']);
            else if (isset($data['organism_id']))
                $array['defaultvalue'] = json_encode($data['organism_id']);
            else if (isset($data['version']))
                $array['defaultvalue'] = $data['version'];
            else if (isset($data['is']))
                $array['defaultvalue'] = $data['is'];
            else if (isset($data['defaultvalue']))
                $array['defaultvalue'] = $data['defaultvalue'];
            else
                $array['defaultvalue'] = null;
            if (isset($data['from']))
                $array['defaultvaluefrom'] = $data['from'];
            else
                $array['defaultvaluefrom'] = null;
            if (isset($data['to']))
                $array['defaultvalueto'] = $data['to'];
            else
                $array['defaultvalueto'] = null;
            $catalogsearchcriteria->save($array);

            //Save translations
            $translationtable = $this->getTable('Translation', 'Easysdi_catalogTable', array());
            $data['guid'] = $catalogsearchcriteria->guid;
            if (!$translationtable->saveAll($data)) {
                $this->setError($translationtable->getError());
                return false;
            }

            return true;
        }

        return false;
    }

}