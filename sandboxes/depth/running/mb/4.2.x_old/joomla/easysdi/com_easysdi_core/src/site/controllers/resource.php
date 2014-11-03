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

        // Set the id to edit in the session.
        $app->setUserState('com_easysdi_core.edit.resource.id', $editId);
        $app->setUserState('com_easysdi_core.edit.resource.resourcetype.id', $app->input->get('resourcetype', '', 'INT'));

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
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save($andclose = true) {
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
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $return, false));
            return false;
        }


        if (!$andclose) {
            // Redirect back to the edit screen.
            $app->setUserState('com_easysdi_core.edit.resource.data', null);
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resource&layout=edit&id=' . $return, false));
        } else {
            // Check in the profile.
            if ($return) {
                $model->checkin($return);
            }

            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
        }
    }

    function cancel() {
        // Flush the data from the session.
        $this->clearSession();
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

        // Flush the data from the session.
        $this->clearSession();
        
        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));        
    }

    public function getUsers() {
        $jinput = JFactory::getApplication()->input;
        $organism_id = $jinput->get('organism', '0', 'string');

        $all = array();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('uro.role_id, u.id, user.name');
        $query->from('#__sdi_user_role_organism uro');
        $query->innerJoin('#__sdi_user u ON u.id = uro.user_id');
        $query->innerJoin('#__users user ON u.user_id = user.id');
        $query->where('organism_id=' . (int)$organism_id);
        $query->order('user.name');
        
        $db->setQuery($query);
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

    function apply() {
        $this->save(false);
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_core.edit.resource.id', null);
        $app->setUserState('com_easysdi_core.edit.resource.resourcetype.id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_core.edit.resource.ur', null);
        $app->setUserState('com_easysdi_core.edit.resource.data', null);
    }

}