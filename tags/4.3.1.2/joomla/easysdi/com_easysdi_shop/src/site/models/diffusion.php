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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

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
                        $this->_item->perimeter [$perimeter->perimeter_id] = $perimeter->buffer;
                    }
                }
                //Parse fileurl to retrieve user/pwd
                $url = parse_url($this->_item->fileurl);
                            
                $this->_item->userurl = isset($url['user']) ? $url['user'] : '';
                $this->_item->passurl = isset($url['pass']) ? $url['pass'] : '';
                unset($url['user'], $url['pass']);

                $this->_item->fileurl = Easysdi_shopHelper::unparse_url($url);
                
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
                if ($perimeter == -1) {
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
                ($perimeter == 1) ? $array['buffer'] = 0 : $array['buffer'] = 1;
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
                ->where('v.id = ' . (int)$version_id);
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
                    ->where('diffusion_id =' . (int)$id);
        } else {
            $query->delete($table)
                    ->where('diffusion_id =' . (int)$id);
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

}