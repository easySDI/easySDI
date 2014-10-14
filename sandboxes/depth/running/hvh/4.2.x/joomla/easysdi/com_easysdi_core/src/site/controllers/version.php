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
    private $md_rollback = array();
    /** @var Easysdi_coreHelper */
    private $core_helpers;
    
    function __construct() {
        $this->core_helpers = new Easysdi_coreHelper();
        parent::__construct();
        }

    
    public function remove() {
        // Initialise variables.
        $db = JFactory::getDbo();
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $data = array();
        $data['id'] = JFactory::getApplication()->input->get('id', null, 'int');

        $version = $model->getData($data['id']);

        $versions = $this->core_helpers->getViralVersionnedChild($version);

        try {
            try{
                $db->transactionStart();
            }catch (Exception $exc){
                $db->connect();                
                $driver_begin_transaction = $db->name . '_begin_transaction';
                $driver_begin_transaction($db->getConnection());
            }
            $this->deleteMetadatas($versions);
            $this->deleteVersions($versions);
            $db->transactionCommit();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
        } catch (Exception $exc) {
            $db->transactionRollback();
            $this->metadataRollback();
            $this->setMessage(JText::_('Metadata can not be deleted.') . $exc->getMessage(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }
    }

    /*
     * 
     */

    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_core.edit.version.id');

        $versionId = JFactory::getApplication()->input->getInt('id', null);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('v.id as id')
                ->from('#__sdi_version v')
                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                ->where('v.id = ' . (int) $versionId);
        $db->setQuery($query);
        $item = $db->loadObject();

        $editId = $item->id;

        // Set session variables
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

    /**
     * Get children of a metadata
     */
    public function getChildren() {
        $user = new sdiUser();
        
        $parentId = JFactory::getApplication()->input->getInt('parentId', null);
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('r.id')
                ->from('#__sdi_resource r')
                ->innerJoin('#__sdi_user_role_resource urr ON urr.resource_id=r.id')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->innerJoin('#__sdi_metadata m ON m.version_id=v.id')
                ->innerJoin('#__sdi_versionlink vl ON v.id=vl.child_id')
                ->where('vl.parent_id='.(int)$parentId.' AND urr.user_id='.(int)$user->id)
                ->group('r.id')
                ;

        $db->setQuery($query)->execute();
        $cnt = $db->getNumRows();

        $response = array();
        $response['success'] = 'true';
        $response['resource_id'] = $parentId;
        $response['num'] = (int)$cnt;

        echo json_encode($response);
        die();
    }
    
    public function getParent(){
        $user = new sdiUser();
        
        $versionId = JFactory::getApplication()->input->getInt('versionId', null);
        $parentState = JFactory::getApplication()->input->getInt('parentState', null);
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('r.id')
                ->from('#__sdi_resource r')
                ->innerJoin('#__sdi_user_role_resource urr ON urr.resource_id=r.id')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->innerJoin('#__sdi_metadata m ON m.version_id=v.id')
                ->innerJoin('#__sdi_versionlink vl ON v.id=vl.parent_id')
                ->where('vl.child_id='.(int)$versionId.' AND urr.user_id='.(int)$user->id)
                ->group('r.id')
                ;
        
        if(!empty($parentState)){
            $query->where('m.metadatastate_id='.(int)$parentState);
        }

        $db->setQuery($query)->execute();
        $cnt = $db->getNumRows();

        $response = array();
        $response['success'] = 'true';
        $response['resource_id'] = $versionId;
        $response['num'] = (int)$cnt;

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
            $versions = $this->core_helpers->getViralVersionnedChild($lastversion);
        }

        // get version to create
        $new_versions = $this->getNewVersions($versions);

        // try to create the new versions
        try {
            try{
                $dbo->transactionStart();
            }catch (Exception $exc){
                $dbo->connect();                
                $driver_begin_transaction = $dbo->name . '_begin_transaction';
                $driver_begin_transaction($dbo->getConnection());
            }
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
            $this->setMessage('Save failed: ' . $exc->getMessage(), 'error');
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
     * Create an array with the new versions
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

    /**
     * Recursively delete versions.
     * 
     * @param array $versions
     * @return void
     * 
     * @throws Exception
     */
    private function deleteVersions($versions) {

        $model = $this->getModel('Version', 'Easysdi_coreModel');

        foreach ($versions as $version) {
            if (!empty($version->children)) {
                $this->deleteVersions($version->children);
            }

            try {
                // Get the user data.
                $data = array();
                $data['id'] = $version->id;

                //Check how many versions left on the resource
                $dbo = JFactory::getDbo();
                $query = $dbo->getQuery(true)
                        ->select('count(id)')
                        ->from('#__sdi_version')
                        ->where('resource_id = ' . (int) $version->resource_id);
                $dbo->setQuery($query);
                $num = $dbo->loadResult();

                // Attempt to delete the version.
                $return = $model->delete($data);

                //Delete resource if needed
                if ($num == 1) {
                    $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
                    $resource->load($version->resource_id);
                }

                $model->checkin($return);
            } catch (Exception $exc) {
                throw $exc;
            }
        }
    }

    /**
     * Recursively delete metadata.
     * 
     * @param array $versions
     * @return array
     * @throws Exception
     */
    private function deleteMetadatas($versions) {

        $md_rollback = array();

        foreach ($versions as $version) {
            if (!empty($version->children)) {
                $md_rollback = array_merge($md_rollback, $this->deleteMetadatas($version->children));
            }

            $metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
            $keys = array("version_id" => $version->id);

           
            $metadata->load($keys);
            //Delete the csw metadata in the remote catalog
            $csw = new sdiMetadata($metadata->id);
            $this->md_rollback[$metadata->id] = $csw->load();
            if (!$csw->delete()) {
                unset($this->md_rollback[$metadata->id]);
                throw new Exception(JText::_('Metadata can not be deleted from the remote catalog.'));
            }

            $metadata->delete($metadata->id);
            
        }

        return $md_rollback;
    }

    /**
     * Recursively create versions.
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

    /**
     * Reinsert metadata when delete fail
     */
    private function metadataRollback() {
        $csw = new sdiMetadata();
        foreach ($this->md_rollback as $metadata) {
            $csw->insert($metadata);
        }
    }

    /**
     * Get a list of cascading deleting children
     * @deprecated since version 4.2.0 - replaced by getCascadeChild
     */
    public function getCascadeDeleteChild() {
        return $this->getCascadeChild(true);
    }
    
    /**
     * getCascadeChild - get the list of the version's children
     */
    public function getCascadeChild($viralVersioning = false){
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $id = JFactory::getApplication()->input->get('version_id', null, 'int');
        if(empty($id)){
            $jform = JFactory::getApplication()->input->get('jform', array(), 'array');
            $id = $jform['id'];
        }
        $data = array();
        $data['id'] = $id;

        $version = $model->getData($data['id']);
        $version->resource_name = $version->resourcename;
        $version->version_name = $version->name;

        $response = array();
        $response['versions'] = $this->core_helpers->getChildrenVersion($version, $viralVersioning);

        echo json_encode($response);
        die();
    }

    public function getNewVersionRight() {
        $metadata_id = JFactory::getApplication()->input->get('metadata_id', null, 'int');

        // Check if metadata has parent viral versionned version
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('v.resource_id AS id');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON m.version_id = v.id');
        $query->where('m.id = ' . $metadata_id);
        $db->setQuery($query);
        $resource = $db->loadObject();
        
        $query = $db->getQuery(true);
        $query->select('m.id, pv.name AS version_name, pr.name AS resource_name');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_versionlink vl ON m.version_id = vl.child_id');
        $query->innerJoin('#__sdi_version pv ON vl.parent_id = pv.id');
        $query->innerJoin('#__sdi_resource pr ON pv.resource_id = pr.id');
        $query->innerJoin('#__sdi_resourcetypelink rtl ON pr.resourcetype_id = rtl.parent_id');
        $query->innerJoin('#__sdi_version cv ON vl.child_id = cv.id');
        $query->innerJoin('#__sdi_resource cr ON cv.resource_id = cr.id AND cr.resourcetype_id = rtl.child_id');
        $query->where('m.id = ' . $metadata_id);

        $db->setQuery($query);
        $metadatas = $db->loadObjectList();

        $response = array();
        $response['canCreate'] = true;
        $response['resource_id'] = $resource->id;
        if (!empty($metadatas)) {
            $elements = array();
            foreach ($metadatas as $metadata) {
                $elements[] = $metadata->resource_name .': '.$metadata->version_name;
            }
            $response['canCreate'] = false;
            $response['cause'][0]['message'] = JText::_('COM_EASYSDI_CORE_VERSIONS_ERROR_VIRAL_VERSIONNING');
            $response['cause'][0]['elements'] = implode('<br/>', $elements);
        }

        $query = $db->getQuery(true);
        $query->select('m.id, av.name AS version_name, r.name AS resource_name');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON m.version_id = v.id');
        $query->innerJoin('#__sdi_resource r ON v.resource_id = r.id');
        $query->innerJoin('#__sdi_version av ON r.id = av.resource_id');
        $query->innerJoin('#__sdi_metadata am ON av.id = am.version_id');
        $query->where('m.id = ' . $metadata_id);
        $query->where('am.metadatastate_id = ' . sdiMetadata::INPROGRESS);
        $db->setQuery($query);
        $metadatas = $db->loadObjectList();

        if(!empty($metadatas)){
            $elements = array();
            foreach ($metadatas as $metadata) {
                $elements[] = $metadata->resource_name .': '.$metadata->version_name;
            }
            $response['canCreate'] = false;
            $response['cause'][1]['message'] = JText::_('COM_EASYSDI_CORE_VERSIONS_ERROR_INPROGRESS_VERSION');
            $response['cause'][1]['elements'] = implode('<br/>', $elements);
        }
		
		
        echo json_encode($response);
        die();
    }
    
    public function getPublishRight(){
        $metadata_id = JFactory::getApplication()->input->get('metadata_id', null, 'int');

        // Check if metadata has parent viral versionned version
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true)
                ->select('v.id, COUNT(md.id) as canPublish')
                ->from('#__sdi_metadata m')
                ->innerJoin('#__sdi_version v ON m.version_id = v.id')
                ->innerJoin('#__sdi_versionlink vl ON v.id=vl.parent_id')
                ->innerJoin('#__sdi_metadata md ON vl.child_id=md.version_id')
                ->where('m.id = ' . $metadata_id.' AND md.metadatastate_id != '.sdiMetadata::PUBLISHED.' AND md.metadatastate_id != '.sdiMetadata::VALIDATED)
                ->group('v.resource_id, v.id')
                ;
        $result = $db->setQuery($query)->loadAssoc();
        
        echo json_encode($result);
        die();
    }

    /**
     * 
     * @return json Liste of version for the specific resource
     */
    public function getInProgressChildren() {
        $resource_id = JFactory::getApplication()->input->get('resource', null, 'int');

        $versions = array();
        if ($lastversion = $this->getLastVersion($resource_id)) {
            $versions = $this->core_helpers->getViralVersionnedChild($lastversion);
        }

        $inProgress = $this->getChildrenByState($versions, array(sdiMetadata::INPROGRESS, sdiMetadata::VALIDATED));

        $response = array();
        $response['total'] = count($inProgress);
        $response['versions'] = $inProgress;

        echo json_encode($response);
        die();
    }

    /**
     * 
     * @param array $versions
     * @param array $state The states for the filter
     * @return array Array filtered by metadatastate_id 
     */
    private function getChildrenByState($versions, $states) {
        $db = JFactory::getDbo();
        $inProgressChildren = array();

        foreach ($versions as $version) {

            if (!empty($version->children)) {
                $inProgressChildren = array_merge($inProgressChildren, $this->getChildrenByState($version->children, $states));
            }

            $query = $db->getQuery(true);
            $query->select('m.id, m.metadatastate_id, v.name as version_name, r.name as resource_name');
            $query->from('#__sdi_version v');
            $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
            $query->innerJoin('#__sdi_resource r on r.id = v.resource_id');
            $query->where('v.id = ' . $version->id);
            $conditions = array();
            foreach ($states as $state) {
                $conditions[] = 'm.metadatastate_id = ' . $state;
            }
            $condition = implode(' OR ', $conditions);
            $query->where('(' . $condition . ')');

            $db->setQuery($query);
            $metadata = $db->loadObject();

            if (!empty($metadata)) {
                $inProgressChildren[] = $metadata;
            }
        }

        return $inProgressChildren;
    }

}
