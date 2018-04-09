<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelPricingOrganism extends JModelForm {

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
            $id = JFactory::getApplication()->getUserState('com_easysdi_shop.edit.pricingorganism.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_shop.edit.pricingorganism.id', $id);
        }
        $this->setState('pricingorganism.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('pricingorganism.id', $params_array['item_id']);
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
                $id = $this->getState('pricingorganism.id');
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('o.id, o.name, o.internal_free, o.fixed_fee_te, o.data_free_fixed_fee, o.fixed_fee_apply_vat')
                    ->from($db->quoteName('#__sdi_organism') . ' as o')
                    ->where('o.id=' . (int) $id);
            $db->setQuery($query);
            $this->_item = $db->loadObject();

            $query = $db->getQuery(true)
                    ->select('c.id, c.name, ocpr.rebate')
                    ->from($db->quoteName('#__sdi_category') . ' as c')
                    ->join('LEFT', '#__sdi_organism_category_pricing_rebate ocpr ON ocpr.category_id=c.id AND ocpr.organism_id=' . (int) $id)
                    ->where('c.state = 1')
                    ->where('c.backend_only = 0')
                    ->order('c.ordering');
            $db->setQuery($query);
            $this->_item->categories = $db->loadObjectList();

            $query = $db->getQuery(true)
                    ->select('pp.id, pp.name, pp.fixed_fee, pp.surface_rate, pp.min_fee, pp.max_fee, COUNT(DISTINCT ppcpr.id) as free_category, COUNT(DISTINCT d.id) as count_diffusions')
                    ->from($db->quoteName('#__sdi_pricing_profile') . ' as pp')
                    ->join('LEFT', '#__sdi_pricing_profile_category_pricing_rebate as ppcpr ON ppcpr.pricing_profile_id=pp.id')
                    ->join('LEFT', '#__sdi_diffusion as d on pp.id = d.pricing_profile_id and d.state = 1 and hasextraction = 1')
                    ->where('pp.organism_id=' . (int) $id)
                    ->group('pp.id, pp.name, pp.fixed_fee, pp.surface_rate, pp.min_fee, pp.max_fee')
                    ->having('pp.id IS NOT NULL');
            $db->setQuery($query);
            $this->_item->profiles = $db->loadObjectList();
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
        $form = $this->loadForm('com_easysdi_shop.pricingorganism', 'pricingorganism', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        if (!sdiFactory::getSdiUser()->isPricingManager($this->_item->id)) {
            foreach ($form->getFieldsets() as $fieldset) {
                foreach ($form->getFieldset($fieldset->name) as $field) {
                    $form->setFieldAttribute($field->fieldname, 'readonly', 'true');
                    $form->setFieldAttribute($field->fieldname, 'disabled', 'true');
                }
            }
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
