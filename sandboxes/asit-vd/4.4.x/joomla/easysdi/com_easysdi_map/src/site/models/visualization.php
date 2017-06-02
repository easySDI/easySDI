<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';

/**
 * Easysdi_map model.
 */
class Easysdi_mapModelVisualization extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_map');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_map.edit.visualization.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
//            JFactory::getApplication()->setUserState('com_easysdi_map.edit.visualization.id', $id);
        }
        $this->setState('visualization.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('visualization.id', $params_array['item_id']);
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
                $id = $this->getState('visualization.id');
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

                //Adapt service reference
            //    ($this->_item->wmsservicetype_id == 1) ? $this->_item->wmsservice_id = 'physical_' . $this->_item->wmsservice_id : $this->_item->wmsservice_id = 'virtual_' . $this->_item->wmsservice_id;
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        if (empty($id)) {
            $this->_item->version_id = JFactory::getApplication()->getUserState('com_easysdi_map.edit.visualizationversion.id');

            $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
            $version = JTable::getInstance('version', 'Easysdi_coreTable');
            $version->load($this->_item->version_id);
            $resource->load($version->resource_id);
            $this->_item->name = $resource->name;
            $this->_item->alias = $resource->alias;
        }

        return $this->_item;
    }

    public function getAuthorizedLayers($visualization_id) {
        $db = JFactory::getDbo();
        $sdiUser = sdiFactory::getSdiUser();

        $cls = '(ml.accessscope_id = 1 OR ((ml.accessscope_id = 4) AND (' . (int)$sdiUser->id . ' IN (select a.user_id from #__sdi_accessscope a where a.entity_guid = ml.guid)))';
        $organisms = $sdiUser->getMemberOrganisms();
        $cls .= 'OR ((ml.accessscope_id = 3) AND (';
        $cls .= $organisms[0]->id . ' in (select a.organism_id from #__sdi_accessscope a where a.entity_guid = ml.guid)';
        $cls .= '))';
        $cls .= ')';
        
        //TODO add accessscope for category organism

        if (!empty($visualization_id)):
            $exclusioncls = 'ml.id NOT IN (SELECT v.maplayer_id FROM #__sdi_visualization v WHERE v.id <> ' . (int)$visualization_id . ' AND v.maplayer_id IS NOT NULL)';
        else:
            $exclusioncls = 'ml.id NOT IN (SELECT v.maplayer_id FROM #__sdi_visualization v WHERE v.maplayer_id IS NOT NULL)';
        endif;
        
        //Exclude layers from de Bing, Google et OSM
        $exclusionbgo = 'ml.id NOT IN (select ml.id from #__sdi_maplayer ml, #__sdi_physicalservice as ps WHERE ml.service_id = ps.id AND ml.service_id = ps.id and ml.servicetype = '. $db->quote('physical') .' and serviceconnector_id IN (12,13,14))';

        
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_maplayer ml')
                ->where($cls)
                ->where('ml.state = 1')
                ->where($exclusioncls)
                ->where($exclusionbgo);



        $db->setQuery($query);
        $layers = $db->loadObjectList();

        return $layers;
    }

    public function getTable($type = 'Visualization', $prefix = 'Easysdi_mapTable', $config = array()) {
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
        $id = (!empty($id)) ? $id : (int) $this->getState('visualization.id');

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
        $id = (!empty($id)) ? $id : (int) $this->getState('visualization.id');

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
        $form = $this->loadForm('com_easysdi_map.visualization', 'visualization', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        
        if(!sdiFactory::getSdiUser()->authorizeOnVersion($this->_item->version_id, sdiUser::viewmanager)){
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
        $data = JFactory::getApplication()->getUserState('com_easysdi_map.edit.visualization.data', array());
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
        $table = $this->getTable();

        
        
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('visualization.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = JFactory::getUser();

        //Check the user right
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI || !$user->authorizeOnVersion($data['version_id'], sdiUser::viewmanager)) {
            //Try to save a diffusion but not a diffusion manager for the related resource
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }


        $poswms = strstr($_REQUEST['jform']['wmsservice_id'], 'physical_');
        $data['wmsservice_id'] = substr($_REQUEST['jform']['wmsservice_id'], strrpos($_REQUEST['jform']['wmsservice_id'], '_') + 1);
        if ($poswms) : $data['wmsservicetype_id'] = 1;
        else :$data['wmsservicetype_id'] = 2;
        endif;


        
        if ($table->save($data) === true) {
            $data['guid'] = $table->guid;
            if (!sdiModel::saveAccessScope($data))
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

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('visualization.id');

        //Check the user right
        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI || !$user->authorizeOnVersion($data['version_id'], sdiUser::viewmanager)) {
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

}