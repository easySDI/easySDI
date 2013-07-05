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
class Easysdi_catalogModelrelation extends sdiModel {

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
    public function getTable($type = 'Relation', $prefix = 'Easysdi_catalogTable', $config = array()) {
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
        $form = $this->loadForm('com_easysdi_catalog.relation', 'relation', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.relation.data', array());

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
            $relationprofile = JTable::getInstance('relationprofile', 'Easysdi_catalogTable');
            $item->profile_id = $relationprofile->loadByRelationID($item->id);

            $searchcriteria = JTable::getInstance('search_criteria', 'Easysdi_catalogTable');
            $searchcriteria_id = $searchcriteria->loadByRelationID($item->id);

            $searchfilter = JTable::getInstance('searchfilter', 'Easysdi_catalogTable');
            $rowsfilter = $searchfilter->loadAll($searchcriteria_id[0]);
            if (is_array($rowsfilter)) {
                $item->searchfilter = $rowsfilter['searchfilter'];
            }

            $searchcriteriacatalog = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
            $item->catalog_id = $searchcriteriacatalog->loadBySearchCriteriaID($searchcriteria_id[0]);
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
                $db->setQuery('SELECT MAX(ordering) FROM #__sdi_relation');
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }

        if($table->childtype_id == 1){
            $table->attributechild_id =null;
            $table->childresourcetype_id = null;
            $table->rendertype_id = null;
        }
        elseif($table->childtype_id == 2){
            $table->classchild_id =null;
            $table->childresourcetype_id = null;
            $table->relationtype_id = null;
            $table->classassociation_id = null;
            $table->namespace_id = null;
            $table->isocode = null;
        }
        elseif($table->childtype_id == 3){
            $table->attributechild_id =null;
            $table->classchild_id =null;
            $table->rendertype_id = null;
        }
        if ($table->classchild_id === '')
            $table->classchild_id = null;
        if ($table->rendertype_id === '')
            $table->rendertype_id = null;
        if ($table->attributechild_id === '')
            $table->attributechild_id = null;
        if ($table->relationtype_id === '')
            $table->relationtype_id = null;
        if ($table->namespace_id === '')
            $table->namespace_id = null;
        if ($table->classassociation_id === '')
            $table->classassociation_id = null;
        if ($table->childresourcetype_id === '')
            $table->childresourcetype_id = null;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     *
     * @since   11.1
     */
    public function save($data) {
        if (parent::save($data)) {
            $item = parent::getItem($data['id']);
            //Delete existing links for this relation
            $relationprofile = JTable::getInstance('relationprofile', 'Easysdi_catalogTable');
            $relationprofile->deleteByRelationId($item->id);
            if (is_array($data['profile_id'])) {
                //Insert/update relation profile links
                foreach ($data['profile_id'] as $profile) {
                    $array = array();
                    $array['relation_id'] = $item->id;
                    $array['profile_id'] = $profile;
                    $relationprofile = JTable::getInstance('relationprofile', 'Easysdi_catalogTable');
                    $keys = array();
                    $keys['relation_id'] = $item->id;
                    $keys['profile_id'] = $profile;
                    $relationprofile->load($keys);
                    $relationprofile->save($array);
                }
            }

            //Delete searchcriteria
                $searchcriteria = JTable::getInstance('search_criteria', 'Easysdi_catalogTable');
                $searchcriteria->deleteByRelationId($item->id);
                 //Delete existing searcriteriafilter with SQL constraints
                //Delete existing catalogsearchcriteria with SQL constraints
                
            if ($data['issearchfilter'] == 1) {
                
                //Save search criteria
                $array = array();
                $array['relation_id'] = $item->id;
                $array['rendertype_id'] = $data['rendertype_id'];
                $array['criteriatype_id'] = '2';
                $array['name'] = $data['name'];
                $array['issystem'] = '0';
                $searchcriteria = JTable::getInstance('search_criteria', 'Easysdi_catalogTable');
                $keys = array();
                $keys['relation_id'] = $item->id;
                $searchcriteria->load($keys);
                if(!$searchcriteria->save($array)){
                    $this->setError($searchcriteria->getError());
                    return false;
                }

                //Save CSW search fields
                $searchfilter = JTable::getInstance('searchfilter', 'Easysdi_catalogTable');
                $data['searchcriteria_id'] = $searchcriteria->id;
                if (!$searchfilter->saveAll($data)) {
                    $this->setError($searchfilter->getError());
                    return false;
                }
               

                if (is_array($data['catalog_id'])) {
                    //Insert/update searchcriteria catalog links
                    foreach ($data['catalog_id'] as $catalog) {
                        $array = array();
                        $array['searchcriteria_id'] = $searchcriteria->id;
                        $array['catalog_id'] = $catalog;
                        $array['searchtab_id'] = 1;
                        $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
                        $keys = array();
                        $keys['searchcriteria_id'] = $searchcriteria->id;
                        $keys['catalog_id'] = $catalog;
                        $catalogsearchcriteria->load($keys);
                        $catalogsearchcriteria->save($array);
                    }
                }
            }else{
                //Delete search filter
            }

            return true;
        }
        return false;
    }

}