<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';

/**
 * Diffusion controller class.
 */
class Easysdi_shopControllerDiffusion extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_shop.edit.diffusion.id');

        $metadataId = JFactory::getApplication()->input->getInt('id', null);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('d.id, v.id as version_id')
                ->from('#__sdi_version v')
                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                ->leftJoin('#__sdi_diffusion d ON d.version_id = v.id')
                ->where('m.id = ' . (int) $metadataId);
        $db->setQuery($query);
        $item = $db->loadObject();

        $editId = $item->id;

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.diffusionmetadata.id', $metadataId);
        $app->setUserState('com_easysdi_shop.edit.diffusion.id', $editId);
        $app->setUserState('com_easysdi_shop.edit.diffusionversion.id', $item->version_id);

        // Get the model.
        $model = $this->getModel('Diffusion', 'Easysdi_shopModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=diffusion&layout=edit', false));
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
        $model = $this->getModel('Diffusion', 'Easysdi_shopModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

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
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', JRequest::getVar('jform'), array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=diffusion&layout=edit&id=' . $id, false));
            return false;
        }

        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }

        //Rebuild complete url if storage is URL or ZONING
        if ($data['productstorage_id'] != 1 && $urlvalue = ($data['productstorage_id'] == 2) ? 'fileurl' : 'packageurl') {
            $temp_urlvalue = Easysdi_shopHelper::unparse_url(parse_url($data[$urlvalue]), array(
                        'user' => $data['userurl'],
                        'pass' => $data['passurl']
            ));
            if (!parse_url($data[$urlvalue]) || !parse_url($temp_urlvalue)) {
                $app->enqueueMessage(JText::sprintf('COM_EASYSDI_SHOP_DOWNLOAD_URL_PARSING_ISSUE', $data[$urlvalue]), 'error');

                // Redirect back to the edit screen.
                $app->setUserState('com_easysdi_shop.edit.diffusion.data', $data);
                $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
                $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $id, false));
                return false;
            }
            $data[$urlvalue] = $temp_urlvalue;
            unset($data['userurl'], $data['passurl']);
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $id, false));
            return false;
        }


        if (!$andclose) {
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', null);

            // Redirect back to the edit screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_SAVED_SUCCESSFULLY'));
            $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $id, false));
        } else {
            // Check in the profile.
            if ($return) {
                $model->checkin($return);
            }

            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_SAVED_SUCCESSFULLY'));
            $back_url = array('root' => 'index.php',
                'option' => 'com_easysdi_core',
                'view' => 'resources',
                'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));

            $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));

            // Flush the data from the session.
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', null);
        }
    }

    function apply() {
        $this->save(false);
    }

    function cancel() {
        $model = $this->getModel('Diffusion', 'Easysdi_shopModel');
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
        $model = $this->getModel('Diffusion', 'Easysdi_shopModel');

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
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.diffusion.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.diffusionmetadata.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&task=diffusion.edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        $this->clearSession();

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.diffusion.data', null);
    }

    public function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_shop.edit.diffusionmetadata.id', null);
        $app->setUserState('com_easysdi_shop.edit.diffusion.id', null);
        $app->setUserState('com_easysdi_shop.edit.diffusionversion.id', null);
    }

    public function testURLAccessibility() {
        $curlHelper = new CurlHelper();
        $curlHelper->URLChecker(JFactory::getApplication()->input);
    }

    public function getAvailableProfiles() {
        $data = JFactory::getApplication()->input->getArray();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('pp.id, pp.name, d.id as affected_diffusion')
                ->from($db->quoteName('#__sdi_version') . ' as v')
                ->join('LEFT', '#__sdi_resource as r ON r.id=v.resource_id')
                ->join('LEFT', '#__sdi_pricing_profile as pp ON pp.organism_id=r.organism_id')
                ->join('LEFT', '#__sdi_diffusion as d ON d.version_id = v.id AND d.pricing_profile_id = pp.id')
                ->where('v.id=' . $data['version_id'])
        ;
        $db->setQuery($query);
        $profiles = $db->loadAssocList();

        //special case for empty results
        if (is_null($profiles[0]["id"])) {
            $profiles = [["id" => "", "name" => JText::_('COM_EASYSDI_SHOP_FORM_ERROR_NO_PRICING_PROFILE_FOUND'), "affected_diffusion" => null]];
        }

        header('Content-type: application/json');
        echo json_encode($profiles);
        die();
    }

}
