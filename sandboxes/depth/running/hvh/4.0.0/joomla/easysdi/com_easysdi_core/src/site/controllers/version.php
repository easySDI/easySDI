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

/**
 * Version controller class.
 */
class Easysdi_coreControllerVersion extends Easysdi_coreController {

    public function remove() {
        // Initialise variables.
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $data = array();
        $data['id'] = JFactory::getApplication()->input->get('id', null, 'int');

        //First Delete the metadata
        $metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
        $keys = array("version_id" => $data['id']);
        $metadata->load($keys);
        //Delete the csw metadata in the remote catalog
        $csw = new sdiMetadata($metadata->id);
        if (!$csw->delete()):
        // Redirect back to the list screen.
//            $this->setMessage(JText::_('Metadata can not be deleted from the remote catalog.'), 'error');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
//            return false;
        endif;
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
                ->where('resource_id = ' . $version->resource_id);
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
        $data['searchlast'] = null ;
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
     * 
     */
    public function save($andclose = false) {
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
        if ($return === false || !$andclose) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.version.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.version.id');
            ($return == false) ? $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning') : $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=version&layout=edit' , false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        $this->flushSessionData();
        //Clear the serach parameters
//        $app->setUserState('com_easysdi_core.edit.version.runsearch', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchtype', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchid', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchname', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchstate', null);
//        $app->setUserState('com_easysdi_core.edit.version.searchlast', null);

        
        

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    function cancel() {   
        $this-> flushSessionData();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    function saveandclose() {
        $this->save(true);
    }
    
    function flushSessionData(){
        $app->setUserState('com_easysdi_core.edit.version.id', null);
        $app->setUserState('com_easysdi_core.edit.version.metadataid', null);
        $app->setUserState('com_easysdi_core.edit.version.data', null);
        $app->setUserState('com_easysdi_core.edit.version.runsearch', null);
    }

}