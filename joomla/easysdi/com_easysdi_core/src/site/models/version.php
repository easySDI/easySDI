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

/**
 * Easysdi_core model.
 */
class Easysdi_coreModelVersion extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_core.edit.version.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
//            JFactory::getApplication()->setUserState('com_easysdi_core.edit.version.id', $id);
        }
        $this->setState('version.id', $id);
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
                $id = $this->getState('version.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');

                $db = JFactory::getDbo();

                //Is versioning activated on the current resource type
                $query = $db->getQuery(true)
                        ->select('r.name, rt.versioning')
                        ->from('#__sdi_resourcetype rt')
                        ->innerJoin('#__sdi_resource r ON r.resourcetype_id = rt.id')
                        ->where('r.id = ' . (int) $table->resource_id);
                $db->setQuery($query);
                $result = $db->loadObject();
                $this->_item->versioning = $result->versioning;
                $this->_item->resourcename = $result->name;
                
                //Get metadatastate
                $query = $db->getQuery(true)
                        ->select('m.metadatastate_id')
                        ->from('#__sdi_metadata m')
                        ->where('m.version_id='.(int)$this->_item->id);
                $db->setQuery($query);
                $this->_item->metadatastate = (int)$db->loadResult();

                //Allowed resourcetype as children
                $query = $db->getQuery(true)
                        ->select('rtl.child_id')
                        ->from('#__sdi_resourcetypelink rtl')
                        ->innerJoin('#__sdi_resource r ON r.resourcetype_id=rtl.parent_id')
                        ->where('r.id='.(int)$table->resource_id);
                
                $db->setQuery($query);
                
                $resourcetypechild = array();
                foreach($db->loadRowList() as $row){
                    array_push($resourcetypechild, $row[0]);
                }
                $this->_item->resourcetypechild = !empty($resourcetypechild) ? implode(',', $resourcetypechild) : '0';
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Version', $prefix = 'Easysdi_coreTable', $config = array()) {
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
        $id = (!empty($id)) ? $id : (int) $this->getState('version.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('version.id');

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
        $form = $this->loadForm('com_easysdi_core.version', 'version', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_core.edit.version.data', array());
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
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('version.id');

        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        if ($id == 0) {
            if (!$user->isResourceManager()) {
                //Try to manage relation but not a resource manager
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        } else {
            $table = $this->getTable();
            $table->load($id);
            if (!$user->authorize($table->resource_id, sdiUser::resourcemanager)) {
                //Try to manage relation but not its resource manager
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            $db = JFactory::getDbo();
            $error = false;
            
            //Delete selected children
            $childrentoremove = json_decode($data['childrentoremove']);
            if(!empty($childrentoremove)){
                $query = $db->getQuery(true)
                        ->delete('#__sdi_versionlink')
                        ->where('child_id IN ('.implode(',', $childrentoremove).')');
                $db->setQuery($query);
                if(!$db->execute()){
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_MSG_CANT_SAVE_VERSIONLINK'), 'error');
                    $error = true;
                }
            }
            
            //Add selected children
            $childrentoadd = json_decode($data['childrentoadd']);
            if(!empty($childrentoadd)){
                foreach($childrentoadd as $child){
                    $obj_child = new stdClass();
                    $obj_child->parent_id = (int) $table->id;
                    $obj_child->child_id = $child;
                    
                    if(!$db->insertObject('#__sdi_versionlink', $obj_child)){
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_MSG_CANT_SAVE_VERSIONLINK'), 'error');
                        $error = true;
                    }
                }
            }
            
            return $error ? false : $table->id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('version.id');

        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!$user->authorizeOnVersion($id, sdiUser::resourcemanager)) {
            //Try to update a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        $table = $this->getTable();
        if ($table->delete($id) === true) {
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
                ->where('id = ' . (int)$id);
        $db->setQuery($query);
        return $db->loadObject();
    }

}