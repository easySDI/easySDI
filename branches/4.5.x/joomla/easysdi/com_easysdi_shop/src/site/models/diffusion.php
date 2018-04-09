<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/resource.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/version.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/userroleresource.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_contact/tables/role.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

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
                $_properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($_properties, 'JObject');

                //Load accessscope
                $this->_item->organisms = sdiModel::getAccessScopeOrganism($this->_item->guid);
                $this->_item->users = sdiModel::getAccessScopeUser($this->_item->guid);
                $this->_item->categories = sdiModel::getAccessScopeCategory($this->_item->guid);
                //Load notified user
                $diffusionnotifieduser = JTable::getInstance('diffusionnotifieduser', 'Easysdi_shopTable');
                $this->_item->notifieduser_id = $diffusionnotifieduser->loadBydiffusionID($this->_item->id);
                //Load perimeter available for extraction
                $diffusionperimeter = JTable::getInstance('diffusionperimeter', 'Easysdi_shopTable');
                $perimeters = $diffusionperimeter->loadBydiffusionID($this->_item->id);
                $this->_item->perimeter = array();
                if ($perimeters) {
                    foreach ($perimeters as $perimeter) {
                        array_push($this->_item->perimeter, $perimeter->perimeter_id);
                    }
                }
                //Parse fileurl/packageurl to retrieve user/pwd
                if (isset($this->_item->fileurl)) {
                    $this->_item->fileurl = $this->unparseurl($this->_item->fileurl);
                } elseif (isset($this->_item->packageurl)) {
                    $this->_item->packageurl = $this->unparseurl($this->_item->packageurl);
                }

                //Load properties and properties values
                $diffusionpropertyvalue = JTable::getInstance('diffusionpropertyvalue', 'Easysdi_shopTable');
                $properties = $diffusionpropertyvalue->loadBydiffusionID($this->_item->id);
                $this->_item->property = array();
                if ($properties) {
                    foreach ($properties as $property) {
                        $this->_item->property [$property->property_id][] = $property->propertyvalue_id;
                    }
                }
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        //Load linked object to fill field(s)
        $version = JTable::getInstance('version', 'Easysdi_coreTable');
        $version->load($this->_item->version_id);
        $this->_item->version_id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.diffusionversion.id');
        $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
        $resource->load($version->resource_id);
        $this->_item->resource_id = $resource->id;

        if (empty($id)) {
            $this->_item->name = $resource->name;
        }
        $this->_item->organism_id = $resource->organism_id;

        //Load extraction managers
        $userroleresource = JTable::getInstance('userroleresource', 'Easysdi_coreTable');
        $this->_item->managers_id = $userroleresource->loadByResourceId($version->resource_id, 'role_id = ' . Easysdi_shopHelper::ROLE_EXTRACTIONRESPONSIBLE);



        return $this->_item;
    }

    private function unparseurl($sourceurl) {
        //Parse fileurl to retrieve user/pwd
        if (!$url = parse_url($sourceurl)) {
            return false;
        }
        $this->_item->userurl = isset($url['user']) ? $url['user'] : '';
        $this->_item->passurl = isset($url['pass']) ? $url['pass'] : '';
        unset($url['user'], $url['pass']);

        return Easysdi_shopHelper::unparse_url($url);
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

        if (!sdiFactory::getSdiUser()->authorizeOnVersion($form->getData()->get('version_id'), sdiUser::diffusionmanager)) {
            foreach ($form->getFieldsets() as $fieldset) {
                foreach ($form->getFieldset($fieldset->name) as $field) {
                    $form->setFieldAttribute($field->fieldname, 'readonly', 'true');
                    $form->setFieldAttribute($field->fieldname, 'disabled', 'true');
                }
            }
        }

        $form->setFieldAttribute('notifieduser_id', 'query', $this->getNotifieduserListQuery());

        $form->setFieldAttribute('managers_id', 'query', $this->getExtractionManagerListQuery());

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.diffusion.data', array());
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
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('diffusion.id');

        //Check the user right
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI || !$user->authorizeOnVersion($data['version_id'], sdiUser::diffusionmanager)) {
            //Try to save a diffusion but not a diffusion manager for the related resource
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        //Save
        $table = $this->getTable();

        if ($table->save($data) === true) {
            $data['guid'] = $table->guid;
            $id = $table->id;
            if (!sdiModel::saveAccessScope($data))
                return false;

            //User
            $ids = '';
            if (!empty($data['notifieduser_id'])):
                foreach ($data['notifieduser_id'] as $user) {
                    $diffusionnotifieduser = JTable::getInstance('diffusionnotifieduser', 'Easysdi_shopTable');
                    $keys = array("diffusion_id" => $id, "user_id" => $user);
                    $diffusionnotifieduser->load($keys);
                    $diffusionnotifieduser->save($keys);
                    if (strlen($ids) > 0 && !empty($diffusionnotifieduser->id))
                        $ids .= ',';
                    if (!empty($diffusionnotifieduser->id))
                        $ids .= $diffusionnotifieduser->id;
                }
            endif;
            //Delete entries no more usefull
            if (!$this->cleanTable($id, '#__sdi_diffusion_notifieduser', $ids))
                return false;

            //Perimeter
            $ids = '';
            foreach ($data['perimeter'] as $key => $perimeter) {
                $diffusionperimeter = JTable::getInstance('diffusionperimeter', 'Easysdi_shopTable');
                $keys = array("diffusion_id" => $id, "perimeter_id" => $key);
                $diffusionperimeter->load($keys);
                if ($perimeter == 0) {
                    try {
                        $diffusionperimeter->delete();
                    } catch (Exception $e) {
                        //item didn't exist, keep going on
                    }
                    continue;
                }

                $array = array();
                $array['diffusion_id'] = $id;
                $array['perimeter_id'] = $key;
                if (!$diffusionperimeter->save($array))
                    return false;

                if (strlen($ids) > 0 && !empty($diffusionperimeter->id))
                    $ids .= ',';
                if (!empty($diffusionperimeter->id))
                    $ids .= $diffusionperimeter->id;
            }
            //Delete entries no more usefull
            if (!$this->cleanTable($id, '#__sdi_diffusion_perimeter', $ids))
                return false;


            //Property
            $ids = '';
            foreach ($data['property'] as $key => $property) {
                if (is_array($property)) {
                    foreach ($property as $value) {
                        if (!$this->saveDiffusionPropertyValue($id, $value, $ids))
                            return false;
                    }
                } else {
                    if (!$this->saveDiffusionPropertyValue($id, $property, $ids))
                        return false;
                }
            }
            //Delete entries no more usefull
            if (!$this->cleanTable($id, '#__sdi_diffusion_propertyvalue', $ids))
                return false;

            //Extraction Manager
            $userroleresource = JTable::getInstance('userroleresource', 'Easysdi_coreTable');
            $userroleresource->deleteByResourceId($data['resource_id'], Easysdi_shopHelper::ROLE_EXTRACTIONRESPONSIBLE);
            $users = $data['managers_id'];
            foreach ($users as $user) {
                $userroleresource = JTable::getInstance('userroleresource', 'Easysdi_coreTable');
                $userroleresource->user_id = $user;
                $userroleresource->role_id = Easysdi_shopHelper::ROLE_EXTRACTIONRESPONSIBLE;
                $userroleresource->resource_id = $data['resource_id'];
                $userroleresource->store();
            }

            //Update the metadata stored in the remote catalog 
            if (!$this->updateCSWMetadata($table->version_id)):
                JFactory::getApplication()->enqueueMessage('Update CSW metadata failed.', 'error');
            endif;

            return $id;
        } else {
            return false;
        }
    }

    private function updateCSWMetadata($version_id) {
        //Update the metadata stored in the remote catalog 
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('m.id')
                ->from('#__sdi_metadata m ')
                ->innerJoin('#__sdi_version v ON v.id = m.version_id')
                ->where('v.id = ' . (int) $version_id);
        $db->setQuery($query);
        $metadata = $db->loadResult();
        $csw = new sdiMetadata((int) $metadata);
        return $csw->updateSDIElement();
    }

    private function saveDiffusionPropertyValue($id, $property, &$ids) {
        $diffusionpropertyvalue = JTable::getInstance('diffusionpropertyvalue', 'Easysdi_shopTable');
        $keys = array("diffusion_id" => $id, "propertyvalue_id" => $property);
        $diffusionpropertyvalue->load($keys);

        if ($property == -1) {
            try {
                $diffusionpropertyvalue->delete();
            } catch (Exception $e) {
                //item didn't exist, keep going on
            }
            return true;
        }

        $array = array();
        $array['diffusion_id'] = $id;
        $array['propertyvalue_id'] = $property;
        if (!$diffusionpropertyvalue->save($array))
            return false;

        if (strlen($ids) > 0 && !empty($diffusionpropertyvalue->id))
            $ids .= ',';
        if (!empty($diffusionpropertyvalue->id))
            $ids .= $diffusionpropertyvalue->id;

        return true;
    }

    private function cleanTable($id, $table, $ids) {
        //Delete entries no more usefull
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if (strlen($ids) > 0) {
            $query->delete($table)
                    ->where('id NOT IN (' . $ids . ')')
                    ->where('diffusion_id =' . (int) $id);
        } else {
            $query->delete($table)
                    ->where('diffusion_id =' . (int) $id);
        }
        $db->setQuery($query);
        if (!$db->execute())
            return false;

        return true;
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('diffusion.id');
        //Check the user right
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI || !$user->authorizeOnVersion($data['version_id'], sdiUser::diffusionmanager)) {
            //Try to save a diffusion but not a diffusion manager for the related resource
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }
        $table = $this->getTable();
        $table->load($id);
        $version_id = $table->version_id;
        if ($table->delete($data['id']) === true) {
            //Update the metadata stored in the remote catalog 
            if (!$this->updateCSWMetadata($version_id)):
                JFactory::getApplication()->enqueueMessage('Update CSW metadata failed.', 'error');
            endif;

            return $id;
        } else {
            return false;
        }

        return true;
    }

    /**
     * 
     * @return type
     */
    private function getExtractionManagerListQuery() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('uro.user_id as id, ju.name as value, o.name as groupname')
                ->from('#__sdi_user_role_organism as uro')
                ->innerJoin('#__sdi_user as u on u.id = uro.user_id')
                ->innerJoin('#__users as ju on ju.id = u.user_id')
                ->innerJoin('#__sdi_user_role_organism m ON u.id = m.user_id AND m.role_id = 1')
                ->innerJoin('#__sdi_organism o ON m.organism_id = o.id')
                ->where('uro.organism_id=' . (int) $this->_item->organism_id)
                ->where('uro.role_id = ' . (int) Easysdi_shopHelper::ROLE_EXTRACTIONRESPONSIBLE)
                ->order('o.name')
                ->order('ju.name');

        return $query->__toString();
    }

    /**
     * 
     * Get the sql query for Notifieduser_id field
     * 
     * @return string
     */
    private function getNotifieduserListQuery() {
        $user = sdiFactory::getSdiUser();
        $data = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.diffusion.data', array());
        $version_id = (isset($this->_item)) ? $this->_item->version_id : $data['version_id'];

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('o.organism_id');
        $query->from('#__sdi_user AS u');
        $query->innerJoin('#__sdi_user_role_organism AS o ON u.id = o.user_id');
        $query->where('u.id=' . (int) $user->id);
        $query->group('o.organism_id');

        $db->setQuery($query);
        $user_organisms = $db->loadColumn();


        $query_resource = $db->getQuery(true);
        $query_resource->select('uro.user_id AS id');
        $query_resource->from('#__sdi_version AS v');
        $query_resource->innerJoin('#__sdi_resource AS r ON v.resource_id = r.id');
        $query_resource->innerJoin('#__sdi_user_role_organism AS uro ON r.organism_id = uro.organism_id');
        $query_resource->innerJoin('#__sdi_metadata AS m ON m.version_id = v.id');
        $query_resource->where('m.id = ' . $version_id);
        $query_resource->group('uro.user_id');

        $db->setQuery($query_resource);
        $users_resource = $db->loadColumn();

        $query_user = $db->getQuery(true);
        $query_user->select('u.id');
        $query_user->from('#__users AS ju');
        $query_user->innerJoin('#__sdi_user AS u ON u.user_id = ju.id');
        $query_user->innerJoin('#__sdi_user_role_organism AS o ON u.id = o.user_id');
        $query_user->where('o.organism_id IN (' . implode(",", $user_organisms) . ')');
        $query_user->group('u.id');

        $db->setQuery($query_user);
        $users_user = $db->loadColumn();

        $query_all = $db->getQuery(true);
        $query_all->select('u.id, ju.name as value, o.name as groupname');
        $query_all->from('#__sdi_user u');
        $query_all->innerJoin('#__users ju ON ju.id=u.user_id');
        $query_all->innerJoin('#__sdi_user_role_organism m ON u.id = m.user_id AND m.role_id = 1');
        $query_all->innerJoin('#__sdi_organism o ON m.organism_id = o.id');
        $query_all->where('u.id IN (' . implode(',', array_unique(array_merge($users_user, $users_resource))) . ')');
        $query_all->order('o.name');
        $query_all->order('ju.name');

        return $query_all->__toString();
    }

}
