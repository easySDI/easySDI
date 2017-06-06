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
class Easysdi_shopControllerPricingOrganism extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the id to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.pricingorganism.id', $editId);

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit', false));
    }

    public function save($andclose = true) {
        $app = JFactory::getApplication();
        $inputs = $app->input;

        $id = $inputs->get('id', 0, 'int');
        $data = $inputs->get('jform', array(), 'array');

        $dataOrganism = $data;
        unset($dataOrganism['categories']);
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

            //save organism's pricing
            $query = $db->getQuery(true)
                    ->update($db->quoteName('#__sdi_organism') );
            foreach ($dataOrganism as $prop => $val)
                $query->set($db->quoteName($prop) . "='{$val}'");

            $query->where('id=' . (int) $id);
            $db->setQuery($query);
            $update = $db->execute();

            //save organism category pricing rebate
            $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__sdi_organism_category_pricing_rebate'))
                    ->where('organism_id=' . (int) $id);
            $db->setQuery($query);
            $delete = $db->execute();

            if(!empty($dataCategories)){
                $concatCategories = implode('', $dataCategories);
                if (!empty($concatCategories)) {
                    $query = $db->getQuery(true)
                            ->insert($db->quoteName('#__sdi_organism_category_pricing_rebate'))
                            ->columns($db->quoteName('organism_id') . ', ' . $db->quoteName('category_id') . ', ' . $db->quoteName('rebate'));
                    foreach ($dataCategories as $category_id => $rebate) {
                        if ("" === $rebate)
                            continue;
                        $query->values("{$id}, {$category_id}, {$rebate}");
                    }

                    $db->setQuery($query);
                    $insert = $db->execute();
                }
            }

            $db->transactionCommit();
        } catch (Exception $ex) {
            $db->transactionRollback();
        }

        // Check for errors.
        if ($ex !== false) { //if there is an Exception, there is an error !!
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.pricingorganism.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('Save failed', $ex->getMessage()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $id, false));
            return false;
        }


        if (!$andclose) {
            // Redirect back to the edit screen.
            $app->setUserState('com_easysdi_shop.edit.pricingorganism.data', null);
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $id, false));
        } else {
            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganisms', false));
        }
    }

    public function apply() {
        $this->save(false);
    }

    public function cancel() {
        // Flush the data from the session.
        $this->clearSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganisms', false));
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_shop.edit.pricingorganism.id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.pricingorganism.data', null);
    }
    
}
