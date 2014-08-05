<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelPricingProfile extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_shop');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.pricingprofile.id');
            $organismId = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.pricingprofile.organism_id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            $organismId = JFactory::getApplication()->input->get('organism');
            JFactory::getApplication()->setUserState('com_easysdi_shop.edit.pricingprofile.organism_id', $organismId);
        }
        $this->setState('pricingprofile.id', $id);
        $this->setState('pricingprofile.organism_id', $organismId);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('pricingprofile.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = new stdClass();

            if (empty($id)) {
                $id = $this->getState('pricingprofile.id');
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                        ->select('pp.*')
                        ->from($db->quoteName('#__sdi_pricing_profile').' as pp')
                        ->where('pp.id='.(int)$id);
            $db->setQuery($query);
            $this->_item = $db->loadObject();
            
            $query = $db->getQuery(true)
                        ->select('c.id, c.name, COUNT(ppcf.id) as isFree')
                        ->from($db->quoteName('#__sdi_category').' as c')
                        ->join('LEFT', '#__sdi_pricing_profile_category_free ppcf ON ppcf.category_id=c.id AND ppcf.pricing_profile_id='. (int)$id)
                        ->group('c.id');
            $db->setQuery($query);
            $this->_item->categories = $db->loadObjectList();
        }

        return $this->_item;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_easysdi_shop.pricingprofile', 'pricingprofile', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = $this->getData();

        return $data;
    }

}