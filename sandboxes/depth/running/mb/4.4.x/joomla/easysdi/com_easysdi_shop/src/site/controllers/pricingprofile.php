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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';
require_once JPATH_COMPONENT . '/models/order.php';

/**
 * Order controller class.
 */
class Easysdi_shopControllerPricingProfile extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        $editId = JFactory::getApplication()->input->getInt('id', null, 'int');
        $organismId = JFactory::getApplication()->input->getInt('organism', null, 'int');

        // Set the id to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.pricingprofile.id', $editId);
        $app->setUserState('com_easysdi_shop.edit.pricingprofile.organism_id', $organismId);

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingprofile&layout=edit', false));
    }

    public function save($andclose = true) {
        $model = $this->getModel('PricingProfile', 'Easysdi_shopModel');
        $pricingProfile = $model->getTable();
        $app = JFactory::getApplication();
        $inputs = $app->input;

        $id = $inputs->get('id');

        $pricingProfile->load($id);

        $data = $inputs->get('jform', array(), 'array');
        if (empty($id))
            $data['organism_id'] = $inputs->get('organism_id');

        $dataProfile = $data;
        unset($dataProfile['categories']);
        $dataCategories = $data['categories'];

        $ex = false;
        $db = JFactory::getDbo();
        try {

            try {
                $db->transactionStart();
            } catch (Exception $exc) {
                $db->connect();
                $driver_begin_transaction = $db->name . '_begin_transaction';
                $driver_begin_transaction($db->getConnection());
            }

            //save pricing profile
            foreach ($dataProfile as $prop => $value)
                $pricingProfile->$prop = $value;

            $pricingProfile->check();
            $pricingProfile->store();

            //save pricing profile categories rebate
            $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__sdi_pricing_profile_category_pricing_rebate'))
                    ->where('pricing_profile_id=' . (int) $pricingProfile->id);
            $db->setQuery($query);
            $delete = $db->execute();

            $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__sdi_pricing_profile_category_pricing_rebate'))
                    ->columns($db->quoteName('pricing_profile_id') . ', ' . $db->quoteName('category_id'));
            $doInsert = false;
            if (!empty($dataCategories)) {
                foreach ($dataCategories as $category_id => $isFree) {
                    if ((bool) $isFree) {
                        $doInsert = true;
                        $query->values($pricingProfile->id . ',' . $category_id);
                    }
                }
            }

            if ($doInsert) {
                $db->setQuery($query);
                $insert = $db->execute();
            }

            $db->transactionCommit();
        } catch (Exception $ex) {
            $db->transactionRollback();
        }

        // Check for errors.
        if ($ex !== false) { //if there is an Exception, there is an error !!
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('Save failed<br>%s', $ex->getMessage()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingprofile&layout=edit', false));
            return false;
        }


        if (!$andclose) {
            // Redirect back to the edit screen.
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.data', null);
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.id', $pricingProfile->id);
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.organism_id', $pricingProfile->organism_id);
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingprofile&layout=edit', false));
        } else {
            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the pricingorganism screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $pricingProfile->organism_id, false));
        }
    }

    public function apply() {
        $this->save(false);
    }

    public function cancel() {

        $organism_id = JFactory::getApplication()->input->get('organism_id', 0, 'int');
        // Flush the data from the session.
        $this->clearSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $organism_id, false));
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_shop.edit.pricingprofile.id', null);
        $app->setUserState('com_easysdi_shop.edit.pricingprofile.organism_id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.pricingprofile.data', null);
    }

    function delete() {
        $app = JFactory::getApplication();
        $inputs = $app->input;

        $profile_id = $inputs->getInt('id');

        $organism_id = $app->getUserState('com_easysdi_shop.edit.pricingprofile.organism_id');

        $db = JFactory::getDbo();

        //Avoid removal of an affected profile
        $query = $db->getQuery(true);
        $query->select('count(id)');
        $query->from('#__sdi_diffusion');
        $query->where('pricing_profile_id=' . (int) $profile_id);
        $query->where('state = 1');
        $query->where('hasextraction = 1');
        $db->setQuery($query);
        $countDIffusionWithProfile = (int) $db->loadResult();
        if ($countDIffusionWithProfile > 0) {
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_PRICINGPROFILE_UNABLE_TO_DELETE_WITH_DIFFUSION'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $organism_id, false));
            return;
        }


        $query = $db->getQuery(true);
        $query->delete('#__sdi_pricing_profile');
        $query->where('id=' . (int) $profile_id);

        $db->setQuery($query);

        if ($db->execute() !== false) {
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $organism_id, false));
        } else {
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_DELETED_FAIL'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $organism_id, false));
        }
    }

}
