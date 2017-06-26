<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * Group controller class.
 */
class Easysdi_mapControllerVisualization extends Easysdi_mapController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_map.edit.visualization.id');

        $metadataId = JFactory::getApplication()->input->getInt('id', null, 'array');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('d.id, v.id as version_id')
                ->from('#__sdi_version v')
                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                ->leftJoin('#__sdi_visualization d ON d.version_id = v.id')
                ->where('m.id = ' . (int) $metadataId);
        $db->setQuery($query);
        $item = $db->loadObject();

        $editId = $item->id;

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_map.edit.visualizationmetadata.id', $metadataId);
        $app->setUserState('com_easysdi_map.edit.visualization.id', $editId);
        $app->setUserState('com_easysdi_map.edit.visualizationversion.id', $item->version_id);

        // Get the model.
        $model = $this->getModel('Visualization', 'Easysdi_mapModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit', false));
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
        $model = $this->getModel('Visualization', 'Easysdi_mapModel');

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
            $app->setUserState('com_easysdi_map.edit.visualization.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_map.edit.visualizationmetadata.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_map.edit.visualization.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_map.edit.visualizationmetadata.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit&id=' . $id, false));
            return false;
        }

        if (!$andclose) {
            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_map.edit.visualizationmetadata.id');
            $app->setUserState('com_easysdi_map.edit.visualization.data', null);
            $this->setMessage(JText::_('COM_EASYSDI_MAP_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit&id=' . $id, false));
        } else {
            // Check in the profile.
            if ($return) {
                $model->checkin($return);
            }

            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_MAP_ITEM_SAVED_SUCCESSFULLY'));
            $back_url = array('root' => 'index.php',
                'option' => 'com_easysdi_core',
                'view' => 'resources',
                'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));
            $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));
        }
    }

    function apply() {
        $this->save(false);
    }

    function cancel() {
        $model = $this->getModel('Visualization', 'Easysdi_mapModel');
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        $model->checkin($data['id']);
        $this->clearSession();

        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources',
            'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));
        $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));
    }

    public function remove() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Visualization', 'Easysdi_mapModel');

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
            $app->setUserState('com_easysdi_map.edit.visualization.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_map.edit.visualizationmetadata.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_map.edit.visualization.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_map.edit.visualizationmetadata.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_map&view=visualization&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        $this->clearSession();

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_MAP_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_map.edit.visualizationmetadata.id', null);
        $app->setUserState('com_easysdi_map.edit.visualization.id', null);
        $app->setUserState('com_easysdi_map.edit.visualizationversion.id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_map.edit.visualization.data', null);
    }

}
