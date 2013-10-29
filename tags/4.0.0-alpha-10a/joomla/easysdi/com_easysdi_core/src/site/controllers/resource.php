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

/**
 * Resource controller class.
 */
class Easysdi_coreControllerResource extends Easysdi_coreController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_core.edit.resource.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_core.edit.resource.id', $editId);

        // Get the model.
        $model = $this->getModel('Resource', 'Easysdi_coreModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }
        
        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&resourcetype='.$app->input->get('resourcetype', '', 'INT'), false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Resource', 'Easysdi_coreModel');

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
            $app->setUserState('com_easysdi_core.edit.resource.data', JRequest::getVar('jform'), array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.resource.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.resource.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.resource.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_easysdi_core.edit.resource.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
        

        // Flush the data from the session.
        $app->setUserState('com_easysdi_core.edit.resource.data', null);
    }

    function cancel() {
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    public function remove() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Resource', 'Easysdi_coreModel');

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
            $app->setUserState('com_easysdi_core.edit.resource.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.resource.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_core.edit.resource.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_core.edit.resource.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_easysdi_core.edit.resource.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_core.edit.resource.data', null);
    }
    
    public function  getUsers(){
        $jinput = JFactory::getApplication()->input;
        $organism_id = $jinput->get('organism', '0', 'string');

        $all = array();
        
        $db = JFactory::getDbo();
        $db->setQuery('SELECT uro.role_id, u.id, user.name
                        FROM #__sdi_user_role_organism uro
                        INNER JOIN #__sdi_user u ON u.id = uro.user_id
                        INNER JOIN #__users user ON u.user_id = user.id
                        WHERE organism_id=' . $organism_id );
        $users = $db->loadObjectList();
        
        foreach ($users as $user) :
            if (!isset($all[$user->role_id]))
                $all[$user->role_id] = array();
            $u = new stdClass();
            $u->id = $user->id;
            $u->name = $user->name;
            array_push($all[$user->role_id], $u);
        endforeach;
        
        
        echo json_encode($all);
        die();
    }

}