<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/resource.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/version.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelDiffusion extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_shop');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.diffusion.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            
            JFactory::getApplication()->setUserState('com_easysdi_shop.edit.diffusion.id', $id);
        }
        $this->setState('diffusion.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('diffusion.id', $params_array['item_id']);
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
                $id = $this->getState('diffusion.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                //Load accessscope
                $this->_item->organisms = sdiModel::getAccessScopeOrganism($this->_item->guid);
                $this->_item->users = sdiModel::getAccessScopeUser($this->_item->guid);
                //Load perimeter available for extraction
                
                //Load properties and properties values
                
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }
        if (empty($id)) {
            $this->_item->version_id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.diffusionversion.id');

            $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
            $version = JTable::getInstance('version', 'Easysdi_coreTable');
            $version->load($this->_item->version_id);
            $resource->load($version->resource_id);
            $this->_item->name = $resource->name;
        }

        return $this->_item;
    }

    public function getTable($type = 'Diffusion', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
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
        $id = (!empty($id)) ? $id : (int) $this->getState('diffusion.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('diffusion.id');

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
        // Get the form.
        $form = $this->loadForm('com_easysdi_shop.diffusion', 'diffusion', array('control' => 'jform', 'load_data' => $loadData));
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
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('diffusion.id');
        $state = (!empty($data['state'])) ? 1 : 0;

        
        //Check the user right
        try {
            $user = sdiFactory::getSdiUser();
            if (!$user->authorizeOnVersion($data['version_id'], sdiUser::diffusionmanager)) {
                //Try to save a diffusion but not a diffusion manager for the related resource
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        } catch (Exception $e) {
            //Not an EasySDI user = not allowed
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        //Clean data
        (empty($data['hasdownload']))? $data['hasdownload'] = "0" :  $data['hasdownload'] = "1";
        (empty($data['hasextraction']))? $data['hasextraction'] = "0" :  $data['hasextraction'] = "1";
        if($data['hasdownload'] == 0){
            $data['productstorage_id']=null;
            $data['file']=null;
            $data['file_hidden']=null;
            $data['fileurl']=null;
            $data['perimeter_id']=null;
        }else{
            switch ($data['productstorage_id']){
                case 1:
                    $data['fileurl']=null;
                    $data['perimeter_id']=null;
                    break;
                case 2:
                    $data['file']=null;
                    $data['file_hidden']=null;
                    $data['perimeter_id']=null;
                    break;
                case 3:
                    $data['file']=null;
                    $data['file_hidden']=null;
                    $data['fileurl']=null;
                    break;
            }
        }
        if($data['hasextraction'] == 0){
            $data['surfacemin']=null;
            $data['surfacemax']=null;
            $data['productmining_id']=null;
            $data['deposit']=null;
            $data['deposit_hidden']=null;
        }
        if($data['pricing_id'] == 2){
            $data['hasdownload'] = "0";
            $data['productstorage_id']=null;
            $data['file']=null;
            $data['file_hidden']=null;
            $data['fileurl']=null;
            $data['perimeter_id']=null;
        }
        $table = $this->getTable();
        if ($table->save($data) === true) {
            if (!sdiModel::saveAccessScope($data))
                return false;
            return $id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('diffusion.id');
        //Check the user right
        try {
            $user = sdiFactory::getSdiUser();
            if (!$user->authorizeOnVersion($data['version_id'], sdiUser::diffusionmanager)) {
                //Try to save a diffusion but not a diffusion manager for the related resource
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        } catch (Exception $e) {
            //Not an EasySDI user = not allowed
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
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

    function getCategoryName($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
                ->select('title')
                ->from('#__categories')
                ->where('id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

}