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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswrecords.php';

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelCatalog extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_catalog');

        $params = JComponentHelper::getParams('com_easysdi_catalog');

        // List state information
        $value = $app->getUserStateFromRequest('global.list.limit', 'limit', $params->get('searchresultpaginationnumber'));
        $limit = $value;
        $this->setState('list.limit', $limit);

        $value = $app->getUserStateFromRequest('com_easysdi_catalog.limitstart', 'limitstart', 1);
        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 1);
        $this->setState('list.start', $limitstart);

//        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $params->get('searchresultpaginationnumber'));
//        $this->setState('list.limit', $limit);
//
//        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 1);
//        $this->setState('list.start', $limitstart);

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.catalog.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_catalog.edit.catalog.id', $id);
        }
        $this->setState('catalog.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('catalog.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('catalog.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if ($table->state != $published) {
                        return $this->_item;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');
               
                if ($this->_item->oninitrunsearch || JFactory::getApplication()->input->get('search', 'false', 'STRING') == 'true') {
                    $this->_item->dom = cswrecords::getRecords($id);
                }
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Catalog', $prefix = 'Easysdi_catalogTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
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
        $data = $this->getData();

        return $data;
    }

    /**
     * Method to get a JPagination object for the data set.
     *
     * @return  JPagination  A JPagination object for the data set.
     *
     * @since   12.2
     */
    public function getPagination() {

        // Create the pagination object.
        $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
        $page = new JPagination(JFactory::getApplication('com_easysdi_catalog')->getUserState('global.list.total'), $this->getState('list.start'), $limit);

        return $page;
    }

}