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

require_once JPATH_COMPONENT . '/libraries/easysdi/FormGenerator.php';


jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelAjax extends JModelForm {

    var $_item = null;
    /**
     *
     * @var JDatabaseDriver 
     */
    var $db = null;
    
    /**
     *
     * @var DOMDocument 
     */
    private $_structure;
    /**
     *
     * @var string 
     */
    private $_ajaxXpath;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_catalog');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.metadata.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            if(isset($id)){
                JFactory::getApplication()->setUserState('com_easysdi_catalog.edit.metadata.id', $id);
            }
        }
        $this->setState('metadata.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('metadata.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    public function getStructure() {
        return $this->_structure;
    }
    
    public function getAjaxXpath(){
        return $this->_ajaxXpath;
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
                $id = $this->getState('metadata.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                //When saving metadata, getData is called once with an empty id.  Authorization doesn't have to be checked in this case.
                if ($id) {
                    try {
                        $user = sdiFactory::getSdiUser();
                    } catch (Exception $e) {
                        //Not an EasySDI user = not allowed
                        JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                        JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                        return false;
                    }

                    if (!$user->authorizeOnMetadata($id, sdiUser::metadataeditor) || !$user->authorizeOnMetadata($id, sdiUser::metadataresponsible)) {
                        //Try to update a resource but not its resource manager
                        JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                        JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                        return false;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                if ($id) {
                    //Load the CSW metadata
                    $CSWmetadata = new sdiMetadata($this->_item->id);
                    if ($result = $CSWmetadata->load())
                        $this->_item->csw = $result;
                }
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Metadata', $prefix = 'Easysdi_catalogTable', $config = array()) {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('metadata.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('metadata.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
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
        $formGenerator = new FormGenerator();

        $form = $this->loadForm('com_easysdi_catalog.metadata', $formGenerator->getForm(), array('control' => 'jform', 'load_data' => $loadData, 'file' => FALSE));

        $this->_structure = $formGenerator->structure;
        $this->_ajaxXpath = $formGenerator->ajaxXpath;

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
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.metadata.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        (empty($data['id']) ) ? $new = true : $new = false;
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('metadata.id');

        try {
            $user = sdiFactory::getSdiUser();
        } catch (Exception $e) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!empty($id) && (!$user->authorizeOnMetadata($id, sdiUser::metadataeditor) || !$user->authorizeOnMetadata($id, sdiUser::metadataresponsible))) {
            //Try to update a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            $CSWmetadata = new sdiMetadata($table->id);
            if ($new) {
                if (!$CSWmetadata->insert()) {
                    $table->delete();
                    return false;
                }
            } else {
                if (!$CSWmetadata->update()) {
                    return false;
                }
            }

            return $id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('metadata.id');

        try {
            $user = sdiFactory::getSdiUser();
        } catch (Exception $e) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!$user->authorizeOnMetadata($id, sdiUser::resourcemanager)) {
            //Try to update a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            return $id;
        } else {
            return false;
        }

        return true;
    }

}