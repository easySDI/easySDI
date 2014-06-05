<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/metadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * Version controller class.
 */
class Easysdi_coreControllerVersion extends Easysdi_coreController {

    private $versions = array();

    public function remove() {
        // Initialise variables.
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $data = array();
        $data['id'] = JFactory::getApplication()->input->get('id', null, 'int');

        $version = $model->getData($data['id']);

        $versions = $this->getViralVersionnedChild($version);

        //First Delete the metadata
        $metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
        $keys = array("version_id" => $data['id']);

        $metadata->load($keys);
        //Delete the csw metadata in the remote catalog
        $csw = new sdiMetadata($metadata->id);
        if (!$csw->delete()) {
            // Redirect back to the list screen.
            $this->setMessage(JText::_('Metadata can not be deleted from the remote catalog.'), 'error');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
//            return false;
        }

        if (!$metadata->delete($metadata->id)) {
            // Redirect back to the list screen.
            $this->setMessage(JText::_('Metadata can not be deleted.'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        //Check how many versions left on the resource
        $version = $model->getData($data['id']);
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true)
                ->select('count(id)')
                ->from('#__sdi_version')
                ->where('resource_id = ' . (int) $version->resource_id);
        $dbo->setQuery($query);
        $num = $dbo->loadResult();

        // Attempt to delete the version.
        $return = $model->delete($data);
        // Check for errors.
        if ($return === false) {
            // Redirect back to the list screen.
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        //Delete resource if needed
        if ($num == 1) {
            $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
            $resource->load($version->resource_id);
            if (!$resource->delete($resource->id)) {
                $this->setMessage(JText::_('Resource can not be deleted.'), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        }

        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    /*
     * 
     */

    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_core.edit.version.id');

        $metadataId = JFactory::getApplication()->input->getInt('id', null);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('v.id as id')
                ->from('#__sdi_version v')
                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                ->where('m.id = ' . (int) $metadataId);
        $db->setQuery($query);
        $item = $db->loadObject();

        $editId = $item->id;

        // Set session variables
        $app->setUserState('com_easysdi_core.edit.version.metadataid', $metadataId);
        $app->setUserState('com_easysdi_core.edit.version.id', $editId);

        // Get the model.
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
    }

    public function getChildren() {
        $parentId = JFactory::getApplication()->input->getInt('parentId', null);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('v.resource_id');
        $query->from('#__sdi_version v');
        $query->where('v.id = ' . (int) $parentId);

        $db->setQuery($query);
        $resource = $db->loadObject();

        $query = $db->getQuery(true);
        $query->select('vl.id');
        $query->from('#__sdi_versionlink vl');
        $query->where('vl.parent_id = ' . $parentId);

        $db->setQuery($query);
        $childs = $db->loadObjectList();

        $response = array();
        $response['success'] = 'true';
        $response['resource_id'] = $resource->resource_id;
        $response['num'] = count($childs);

        echo json_encode($response);
        die();
    }

    /**
     * 
     */
    public function search() {
        $app = JFactory::getApplication();

        // Get the user data.
        $jform = JFactory::getApplication()->input->get('jform', array(), 'array');

        $app->setUserState('com_easysdi_core.edit.version.data', $jform);
//
//        $searchtype = $jform['searchtype'];
//        $searchid = $jform['searchid'];
//        $searchname = $jform['searchname'];
//        $searchstate = $jform['searchstate'];
//        $searchlast = $jform['searchlast'];
//
        $app->setUserState('com_easysdi_core.edit.version.runsearch', '1');
//        $app->setUserState('com_easysdi_core.edit.version.searchtype', $searchtype);
//        $app->setUserState('com_easysdi_core.edit.version.searchid', $searchid);
//        $app->setUserState('com_easysdi_core.edit.version.searchname', $searchname);
//        $app->setUserState('com_easysdi_core.edit.version.searchstate', $searchstate);
//        $app->setUserState('com_easysdi_core.edit.version.searchlast', $searchlast);
        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
    }

    /**
     * 
     */
    public function clear() {
        $app = JFactory::getApplication();
        $data = $app->getUserState('com_easysdi_core.edit.version.data');
        $data['searchtype'] = null;
        $data['searchid'] = null;
        $data['searchname'] = null;
        $data['searchstate'] = null;
        $data['searchlast'] = null;
        $app->setUserState('com_easysdi_core.edit.version.data', $data);
//        $app->setUserState('com_easysdi_core.edit.version.searchtype', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchid', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchname', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchstate', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchlast', null);
        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
    }

    /**
     * TODO : This piece of code may be moved to an other place ? It can be more clear to have it in a model raither than in a controller.
     * Move it if needed when implementing version surpport.
     */
    function create() {
        $dbo = JFactory::getDbo();

        $resource_id = JFactory::getApplication()->input->get('resource', null, 'int');

        $versions = array();
        if ($lastversion = $this->getLastVersion($resource_id)) {
            $versions = $this->getViralVersionnedChild($lastversion);
        }

        $new_versions = $this->getNewVersions($versions);

        try {
            $dbo->transactionStart();
            $this->saveVersions($new_versions);
            
            //Create the linked metadata
            require_once JPATH_SITE . '/components/com_easysdi_catalog/models/metadata.php';
            $metadata = JModelLegacy::getInstance('metadata', 'Easysdi_catalogModel');
            $model = $this->getModel('Version', 'Easysdi_coreModel');
            
            $metadatas = array();
            foreach ($this->versions as $version) {
                $mddata = array("metadatastate_id" => 1, "accessscope_id" => 1, "version_id" => $version['id']);
                $mddata['id'] = $metadata->save($mddata);
                $metadatas[] = $mddata;

                // Check in the version.
                $model->checkin($version['id']);
            }
            
            $dbo->transactionCommit();
            
        } catch (Exception $exc) {
            // if fail, clean metadata 
            foreach ($metadatas as $mddata) {
                $metadata->delete($mddata);
            }
            $dbo->transactionRollback();
            $model = $this->getModel('Version', 'Easysdi_coreModel');
            $this->setMessage('Save failed: '. $exc->getMessage(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    /**
     * 
     */
    public function save($andclose = true) {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $model->getForm();

        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }

        // Validate the posted data.
        $data = $model->validate($form, $data);

        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.version.data', JRequest::getVar('jform'), array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.version.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.version.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.version.id');
            ($return == false) ? $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning') : $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
            return false;
        }


        // Check in the version.
        if ($return) {
            $model->checkin($return);
        }


        if (!$andclose) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.version.data', $data);
            // Redirect back to the edit screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit', false));
        } else {
            // Redirect to the list screen.
            $this->flushSessionData();
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $back_url = array('root' => 'index.php',
                'option' => 'com_easysdi_core',
                'view' => 'resources',
                'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));
            $this->setRedirect(JRoute::_($back_url, false));
        }
    }

    function cancel() {
        $this->flushSessionData();
        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources',
            'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));
        $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));
    }

    function apply() {
        $this->save(false);
    }

    function flushSessionData() {
        $app = JFactory::getApplication();
        $app->setUserState('com_easysdi_core.edit.version.id', null);
        $app->setUserState('com_easysdi_core.edit.version.metadataid', null);
        $app->setUserState('com_easysdi_core.edit.version.data', null);
        $app->setUserState('com_easysdi_core.edit.version.runsearch', null);
    }

    /**
     * 
     * @param array $versions
     */
    private function getNewVersions($versions) {
        $new_versions = array();

        foreach ($versions as $version) {
            $new_version = array();
            $new_version['resource_id'] = $version->resource_id;
            $new_version['name'] = date("Y-m-d H:i:s");

            if (!empty($version->children)) {
                $new_version['children'] = $this->getNewVersions($version->children);
            }

            $new_versions[] = $new_version;
        }

        return $new_versions;
    }

    private function deleteVersions($versions) {
        
    }

    /**
     * Save versions recursivly
     * 
     * @throws RuntimeException
     * @param array $versions
     */
    private function saveVersions($versions) {
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        $response = array('selected_children' => array(), 'success' => true);
        $selected_children = array();
        $success = true;

        try {
            foreach ($versions as $version) {
                if (array_key_exists('children', $version)) {
                    $version['selectedchildren'] = $this->saveVersions($version['children']);
                }

                // Attempt to save the data.
                $return = $model->save($version);
                $version['id'] = $return;
                $this->versions[] = $version;
                $selected_children[] = $version['id'];
            }
            return json_encode($selected_children);
        } catch (RuntimeException $exc) {
            throw $exc;
        }
    }

    /**
     * Check recursively if version has viral versionning child
     * 
     * @param stdClass $version
     * @return stdClass[] if has child or false
     */
    private function getViralVersionnedChild($version) {
        $all_versions = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cv.id, cv.name AS version_name, cr.name AS resource_name, cr.id AS resource_id');
        $query->from('#__sdi_versionlink vl');
        $query->innerJoin('#__sdi_version pv ON vl.parent_id = pv.id');
        $query->innerJoin('#__sdi_resource pr ON pv.resource_id = pr.id');
        $query->innerJoin('#__sdi_resourcetypelink rtl ON pr.resourcetype_id = rtl.parent_id');
        $query->innerJoin('#__sdi_version cv ON vl.child_id = cv.id');
        $query->innerJoin('#__sdi_resource cr ON cv.resource_id = cr.id AND cr.resourcetype_id = rtl.child_id');
        $query->where('rtl.viralversioning = 1');
        $query->where('vl.parent_id = ' . (int) $version->id);

        $db->setQuery($query);
        $childs = $db->loadObjectList('id');

        if (count($childs) > 0) {
            $version->children = $childs;
            $all_versions[$version->id] = $version;
            $all_versions[$version->id]->children = $childs;

            foreach ($childs as $key => $child) {
                $this->getViralVersionnedChild($child);
            }
        }

        return $all_versions;
    }

    /**
     * Get last resource version
     * 
     * @param int $resource_id
     * @return stdClass or false  The last version
     */
    private function getLastVersion($resource_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('v.id, v.resource_id');
        $query->from('#__sdi_version v');
        $query->where('v.resource_id = ' . (int) $resource_id);
        $query->order('v.id DESC');

        $db->setQuery($query);
        $version = $db->loadObject();

        if (!empty($version)) {
            return $version;
        } else {
            return false;
        }
    }

}
