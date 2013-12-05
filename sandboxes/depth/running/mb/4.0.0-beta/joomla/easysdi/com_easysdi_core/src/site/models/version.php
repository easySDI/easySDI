<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
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
            JFactory::getApplication()->setUserState('com_easysdi_core.edit.version.id', $id);
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

                //Allowed resourcetype as children
                $query = $db->getQuery(true)
                        ->select('rtchild.id')
                        ->from('#__sdi_resourcetype rtchild')
                        ->innerJoin('#__sdi_resourcetypelink rtl ON rtl.child_id = rtchild.id')
                        ->innerJoin('#__sdi_resourcetype rt ON rt.id= rtl.parent_id')
                        ->innerJoin('#__sdi_resource r ON r.resourcetype_id = rt.id')
                        ->where('r.id = ' . (int) $table->resource_id);
                $db->setQuery($query);
                $resourcetypechild = $db->loadRow();

                //Get parents
                $query = $db->getQuery(true)
                        ->select('v.id as id, v.name as version, r.name as resource, rt.alias as resourcetype, ms.value as state')
                        ->from('#__sdi_version v')
                        ->innerJoin('#__sdi_versionlink vl ON vl.parent_id = v.id')
                        ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                        ->innerJoin('#__sdi_resource r ON r.id = v.resource_id')
                        ->innerJoin('#__sdi_resourcetype rt ON rt.id = r.resourcetype_id')
                        ->innerJoin('#__sdi_sys_metadatastate ms ON ms.id = m.metadatastate_id')
                        ->where('vl.child_id = ' . (int) $table->id)
                ;
                $db->setQuery($query);
                $this->_item->parents = $db->loadObjectList();

                //Get children
                $query = $db->getQuery(true)
                        ->select('v.id as id, v.name as version,r.name as resource, rt.alias as resourcetype, ms.value as state')
                        ->from('#__sdi_version v')
                        ->innerJoin('#__sdi_versionlink vl ON vl.child_id = v.id')
                        ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                        ->innerJoin('#__sdi_resource r ON r.id = v.resource_id')
                        ->innerJoin('#__sdi_resourcetype rt ON rt.id = r.resourcetype_id')
                        ->innerJoin('#__sdi_sys_metadatastate ms ON ms.id = m.metadatastate_id')
                        ->where('vl.parent_id = ' . (int) $table->id)
                ;
                $db->setQuery($query);
                $this->_item->children = $db->loadObjectList();

                //Get session
                $app = JFactory::getApplication();
                $data = $app->getUserState('com_easysdi_core.edit.version.data');
                $this->_item->searchtype = (!empty($data['searchtype']))? $data['searchtype'] : null;
                $this->_item->searchid = (!empty($data['searchid']))?$data['searchid'] : null;
                $this->_item->searchname = (!empty($data['searchname']))?$data['searchname'] : null;
                $this->_item->searchstate = (!empty($data['searchstate']))?$data['searchstate'] : null;
                $this->_item->searchlast = (!empty($data['searchlast']))?$data['searchlast'] : null;

                //Get search result
                if (!empty($resourcetypechild)) {//No resourcetype can be child
                    $run = $app->getUserState('com_easysdi_core.edit.version.runsearch');
                    if (!empty($run)) {
                        $query = $db->getQuery(true)
                                ->select('v.id as id, v.name as version, r.name as resource, rt.alias as resourcetype, ms.value as state')
                                ->from('#__sdi_version v')
                                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                                ->innerJoin('#__sdi_resource r ON r.id = v.resource_id')
                                ->innerJoin('#__sdi_resourcetype rt ON rt.id = r.resourcetype_id')
                                ->innerJoin('#__sdi_sys_metadatastate ms ON ms.id = m.metadatastate_id')
                                ->where(' v.id <> ' . (int) $table->id)
                                ->where('rt.id IN (' . implode(',', $resourcetypechild) . ')')
                        ;
                        if (!empty($this->_item->searchtype)) {
                            $query->where('rt.id = ' . $this->_item->searchtype);
                        }
                        if (!empty($this->_item->searchid)) {
                            $query->where('m.guid = "' . $this->_item->searchid .'"');
                        }
                        if (!empty($this->_item->searchname)) {
                            $query->where('r.name LIKE "%' . $this->_item->searchname .'%"');
                        }
                        if (!empty($this->_item->searchstate)) {
                            $query->where('m.metadatastate_id = ' . $this->_item->searchstate);
                        }
                        if (!empty($this->_item->children)) {
                            $list = array();
                            foreach ($this->_item->children as $child):
                                $list[] = $child->id;
                            endforeach;
                            $query->where('v.id NOT IN (' . implode(',', $list) . ')');
                        }

                        $db->setQuery($query);
                        $result = $db->loadObjectList();
                        $this->_item->availablechildren = $result;
                    }
                }
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

            //Delete recorded children
            $query = $db->getQuery(true)
                    ->delete('#__sdi_versionlink')
                    ->where('parent_id = ' . (int) $table->id)
            ;
            $db->setQuery($query);
            if (!$db->query()):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_MSG_CANT_SAVE_VERSIONLINK'), 'error');
                return false;
            endif;

            //Save selected children
            $selectedchildren = json_decode($data['selectedchildren']);
            if (!empty($selectedchildren)):
                foreach ($selectedchildren as $child):
                    $obj_child = new stdClass();
                    $obj_child->parent_id = (int) $table->id;
                    $obj_child->child_id = $child;

                    // Insert the object into the sdi_versionlink table.
                    if (!JFactory::getDbo()->insertObject('#__sdi_versionlink', $obj_child)):
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CORE_MSG_CANT_SAVE_VERSIONLINK'), 'error');
                        return false;
                    endif;
                endforeach;
            endif;

            return $table->id;
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
                ->where('id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

}