<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';

/**
 * Easysdi_core model.
 */
class Easysdi_coreModelResource extends JModelForm {

    var $_item = null;
    
    const MEMBER = 1;
    const RESOURCEMANAGER = 2;
    const METADATARESPONSIBLE = 3;
    const METADATAEDITOR = 4;
    const DIFFUSIONMANAGER = 5;
    const PREVIEWMANAGER = 6;
    const EXTRACTIONRESPONSIBLE = 7;
    

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_core');
        
        $id = JFactory::getApplication()->getUserState('com_easysdi_core.edit.resource.id');
        $resourcetype_id = JFactory::getApplication()->getUserState('com_easysdi_core.edit.resource.resourcetype.id');
        
        $this->setState('resource.id', $id);
        $this->setState('resourcetype.id', $resourcetype_id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('resource.id', $params_array['item_id']);
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
        $db = JFactory::getDbo();

        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('resource.id');
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
                $this->_item->categories = sdiModel::getAccessScopeCategory($this->_item->guid);
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }

            //Get resourcetype from GET
            $jinput = JFactory::getApplication()->input;
            if (!isset($this->_item->resourcetype_id)) {
                $this->_item->resourcetype_id = $this->getState('resourcetype.id');
            }else{
                JFactory::getApplication()->setUserState('com_easysdi_core.edit.resource.resourcetype.id', $this->_item->resourcetype_id);
            }

            if (!empty($this->_item->resourcetype_id)) {
                require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/resourcetype.php';
                $resourcetype = JTable::getInstance('resourcetype', 'Easysdi_catalogTable');
                $resourcetype->load($this->_item->resourcetype_id);
                $resourcetype->loadLocalname();
                $this->_item->resourcetype = $resourcetype->localname;
                
                $this->_item->resourcerights = array(
                    5 => $resourcetype->diffusion,
                    6 => $resourcetype->view,
                    7 => $resourcetype->diffusion
                );
            }

            //Load rights
            if (isset($this->_item->id)) {
                $query = $db->getQuery(true)
                        ->select('urr.user_id as user_id, urr.role_id as role_id')
                        ->from('#__sdi_user_role_resource urr')
                        ->where('urr.resource_id = ' . (int)$this->_item->id);
                $db->setQuery($query);
                $rows = $db->loadObjectList();
                $this->_item->rights = json_encode($rows);
            } else {
                //Set current user as default selection for all role
                $user = sdiFactory::getSdiUser();
                if (!$user->isEasySDI) {
                    JFactory::getApplication()->enqueueMessage(JText::_("Can't set current user as default for all roles"), 'error');
                    JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                    return;
                }
                $rows = array();
                $row = array("role_id" => "2", "user_id" => $user->id);
                $rows [] = (object) $row;
                $row = array("role_id" => "3", "user_id" => $user->id);
                $rows [] = (object) $row;
                $row = array("role_id" => "4", "user_id" => $user->id);
                $rows [] = (object) $row;
                $row = array("role_id" => "5", "user_id" => $user->id);
                $rows [] = (object) $row;
                $row = array("role_id" => "6", "user_id" => $user->id);
                $rows [] = (object) $row;
                $row = array("role_id" => "7", "user_id" => $user->id);
                $rows [] = (object) $row;
                $this->_item->rights = json_encode($rows);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Resource', $prefix = 'Easysdi_coreTable', $config = array()) {
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
        $id = (!empty($id)) ? $id : (int) $this->getState('resource.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('resource.id');

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
        $form = $this->loadForm('com_easysdi_core.resource', 'Resource', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        
        if(!sdiFactory::getSdiUser()->authorize($form->getData()->get('id'), sdiUser::resourcemanager)){
            foreach($form->getFieldsets() as $fieldset){
                foreach($form->getFieldset($fieldset->name) as $field){
                    $form->setFieldAttribute($field->fieldname, 'readonly', 'true');
                    $form->setFieldAttribute($field->fieldname, 'disabled', 'true');
                }
            }
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_core.edit.resource.data', array());

        if (empty($data)) {
            $data = $this->getData();
        }

        return $data;
    }
    
    public function validate($form, $data, $group = null){
        $return = parent::validate($form, $data, $group);
        
        // validate extra fields
        $jinput = JFactory::getApplication()->input;
        $jform = $jinput->get('jform', '', 'ARRAY');
        
        // --extra fields : resources users rights
        if(!isset($jform[2])){
            $this->setError(JText::_('COM_EASYSDI_CORE_RESOURCES_ITEM_SAVED_ERROR_RESOURCE_MANAGER'));
            return false;
        }
        
        return $return;
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        (empty($data['id'])) ? $new = true : $new = false;

        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('resource.id');

        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        if ($id == 0) {
            if (!$user->isResourceManager()) {
                //Try to create a resource but not a resource manager
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        } else {
            if (!$user->authorize($id, sdiUser::resourcemanager)) {
                //Try to update a resource but not its resource manager
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            //Save accessscope
            JFactory::getApplication()->setUserState('com_easysdi_core.edit.resource.id', $table->id);
            $id = $table->id;
            $data['guid'] = $table->guid;
            if (!sdiModel::saveAccessScope($data))
                return false;

            //Save users rights
            $jinput = JFactory::getApplication()->input;
            $jform = $jinput->get('jform', '', 'ARRAY');

            $userroleresource = JTable::getInstance('userroleresource', 'Easysdi_coreTable');
            $userroleresource->deleteByResourceId($table->id);
            
            for ($index = 2; $index < 8; $index++) { // $index refers to sys_role ID - role with id 8 was removed !
                if (!isset($jform[$index]))
                    continue;

                $users = $jform[$index];
                foreach ($users as $user) {
                    $userroleresource = JTable::getInstance('userroleresource', 'Easysdi_coreTable');
                    $userroleresource->user_id = $user;
                    $userroleresource->role_id = $index;
                    $userroleresource->resource_id = $table->id;
                    $userroleresource->store();
                }
            }

            //If it is a new resource, create the first version and its associated metadata
            if ($new) {
                $version = JTable::getInstance('version', 'Easysdi_coreTable');
//                $version->resource_id = $table->id;
//                $version->name = date("Y-m-d H:i:s");
                $values = array("resource_id" => $table->id, "name" => date("Y-m-d H:i:s") );
                $version->save($values);

                require_once JPATH_SITE . '/components/com_easysdi_catalog/models/metadata.php';
                $metadata = JModelLegacy::getInstance('metadata', 'Easysdi_catalogModel');
                $mddata = array("metadatastate_id" => 1, "accessscope_id" => 1, "version_id" => $version->id);
                if ($metadata->save($mddata) === false) {
                    //Saving metadata in database or metadata in CSW catalog failed
                    //Version and resource must be deleted
                    if (!$version->delete()) {
                        //Can not delete version, it's a mess in the database from now...
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_RESOURCES_ITEM_SAVED_ERROR_ROLLBACK_VERSION_ERROR'), 'error');
                        return false;
                    }
                    if (!$table->delete($table->id)) {
                        //Can not delete resource, it's a mess in the database from now...
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_RESOURCES_ITEM_SAVED_ERROR_ROLLBACK_RESOURCE_ERROR'), 'error');
                        return false;
                    }
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_RESOURCES_ITEM_SAVED_ERROR'), 'error');
                    return false;
                }
            } else {
                //Update the metadata stored in the remote catalog 
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('m.id')
                        ->from('#__sdi_metadata m ')
                        ->innerJoin('#__sdi_version v ON v.id = m.version_id')
                        ->innerJoin('#__sdi_resource r ON r.id = v.resource_id')
                        ->where('r.id = ' . (int)$table->id);
                $db->setQuery($query);
                $metadatas = $db->loadColumn();
                foreach($metadatas as $metadata):
                    $csw = new sdiMetadata((int)$metadata);
                    if (!$csw->updateSDIElement()):
                        JFactory::getApplication()->enqueueMessage('Update CSW metadata failed.', 'error');                        
                    endif;
                endforeach;
            }

            return $id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('resource.id');

        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!$user->authorize($id, sdiUser::resourcemanager)) {
            //Try to delete a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }


        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            sdiModel::deleteAccessScope($data['guid']);

            return $id;
        } else {
            return false;
        }

        return true;
    }

}