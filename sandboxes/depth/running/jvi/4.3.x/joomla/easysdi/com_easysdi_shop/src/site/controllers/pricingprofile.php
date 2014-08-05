<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
    
    public function save($andclose = true){
        $app = JFactory::getApplication();
        $inputs = $app->input;
        
        $id = $inputs->get('id', 0, 'int');
        $originalId = $id; // use in case of rollback
        $organism_id = $inputs->get('organism_id', 0, 'int');
        $data = $inputs->get('jform', array(), 'array');
        
        $dataProfile = $data;
        unset($dataProfile['categories']);
        $dataCategories = $data['categories'];
        
        $ex = false;
        $db = JFactory::getDbo();
        try{
            $db->transactionStart();
            
            //save pricing profile
            if($id == 0){
                $query = $db->getQuery(true)
                            ->insert($db->quoteName('#__sdi_pricing_profile'));
                foreach($dataProfile as $prop => $val)
                    $query->set("`{$prop}`='{$val}'");

                $query->set('`organism_id`='.(int)$organism_id);
                $db->setQuery($query);
                $insert = $db->execute();
                $id = $db->insertid();
            }
            else{
                $query = $db->getQuery(true)
                            ->update($db->quoteName('#__sdi_pricing_profile').' as pp');
                foreach($dataProfile as $prop => $val)
                    $query->set("`{$prop}`='{$val}'");

                $query->where('pp.id='.(int)$id . ' AND pp.organism_id='.(int)$organism_id);
                $db->setQuery($query);
                $update = $db->execute();
            }

            //save pricing profile free categories
            $query = $db->getQuery(true)
                        ->delete($db->quoteName('#__sdi_pricing_profile_category_free'))
                        ->where('pricing_profile_id='.(int)$id);
            $db->setQuery($query);
            $delete = $db->execute();
            
            $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__sdi_pricing_profile_category_free'))
                        ->columns('`pricing_profile_id`, `category_id`');
            $doInsert = false;
            foreach($dataCategories as $category_id => $isFree){
                if((bool)$isFree){
                    $doInsert = true;
                    $query->values($id.','.$category_id);
                }
            }
            
            if($doInsert){
                $db->setQuery($query);
                $insert = $db->execute();
            }
            
            $db->transactionCommit();
        } catch (Exception $ex) {
            $db->transactionRollback();
            $id = $originalId;
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
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.id', $id);
            $app->setUserState('com_easysdi_shop.edit.pricingprofile.organism_id', $organism_id);
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingprofile&layout=edit', false));
        } else {
            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the pricingorganism screen.
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=pricingorganism&layout=edit&id=' . $organism_id, false));
        }
    }
    
    public function apply(){
        $this->save(false);
    }
    
    public function cancel(){
        
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

}