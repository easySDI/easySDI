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

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/controllers/sheet.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelBasket extends JModelLegacy {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $content = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        $this->setState('basket.content', $content);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($content = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($content)) {
                $content = $this->getState('basket.content');
            }

            $this->_item = unserialize($content);
        }

        return $this->_item;
    }

    /**
     * Method to save the data.
     *
     * @param	sdiBasket		The data.
     * @return	mixed		false on failure.
     * @since	1.6
     */
    public function save($basket) {
        if (empty($basket)) {
            return false;
        }

        $data = array();

        //Save order object
        if (empty($basket->id)):
            $data['id'] = 0;
        else:
            $data['id'] = $basket->id;
        endif;

        if (empty($basket->name)):
            $systemConfig = JFactory::getConfig();
            $joomlaUser = JFactory::getUser();
            $dateForName = JFactory::getDate();
            $dateForName->setTimeZone(new DateTimeZone($joomlaUser->getParam('timezone', $systemConfig->get('offset'))));
            $data['name'] = JFactory::getUser()->name . JHtml::date($dateForName, JText::_('COM_EASYSDI_SHOP_BASKET_DATE_IN_NAME_FORMAT'));
            ;
        else:
            $data['name'] = $basket->name;
        endif;

        $data['sent'] = '0000-00-00 00:00:00';
        $data['created'] = $basket->created;
        $data['created_by'] = $basket->created_by;
        $data['surface'] = $basket->extent->surface;
        $data['level'] = $basket->extent->level;
        $data['thirdparty_id'] = (($basket->thirdparty != -1) && ($basket->thirdparty != "")) ? $basket->thirdparty : NULL;
        $data['mandate_ref'] = $basket->mandate_ref;
        $data['mandate_contact'] = $basket->mandate_contact;
        $data['mandate_email'] = $basket->mandate_email;
        $data['freeperimetertool'] = $basket->freeperimetertool;
        $data['access_token'] = JUserHelper::genRandomPassword(64);
        $data['validation_token'] = JUserHelper::genRandomPassword(64);
        switch (JFactory::getApplication()->input->get('action', 'save', 'string')) {
            case 'order':
                $data['ordertype_id'] = Easysdi_shopHelper::ORDERTYPE_ORDER;
                $data['orderstate_id'] = ($data['thirdparty_id'] !== NULL) ? Easysdi_shopHelper::ORDERSTATE_VALIDATION : Easysdi_shopHelper::ORDERSTATE_SENT;
                break;
            case 'estimate':
                $data['ordertype_id'] = Easysdi_shopHelper::ORDERTYPE_ESTIMATE;
                $data['orderstate_id'] = Easysdi_shopHelper::ORDERSTATE_SENT;
                break;
            case 'draft':
                $data['ordertype_id'] = Easysdi_shopHelper::ORDERTYPE_DRAFT;
                $data['orderstate_id'] = Easysdi_shopHelper::ORDERSTATE_SAVED;
                break;
        }
        $data['user_id'] = sdiFactory::getSdiUser()->id;

        $table = $this->getTable();
        $table->load($basket->id, false);
        if ($table->save($data) !== true) {
            return false;
        }

        $basketData = array(
            'orderstate_id' => $table->orderstate_id,
            'ordertype_id' => $table->ordertype_id,
            'diffusions' => array(),
            'order_id' => $table->id,
            'thirdparty_id' => $table->thirdparty_id,
            'order_name' => $table->name
        );

        if (!empty($basket->id)) {
            $this->cleanTables($basket->id);
        }

        // Pricing treatment : rebuild extractions array to allow by supplier grouping
        Easysdi_shopHelper::extractionsBySupplierGrouping($basket);
        // calculate price for the current basket (only if surface is defined)
        Easysdi_shopHelper::basketPriceCalculation($basket);

        //Save diffusions
        $products = array();
        $count_available = 0;
        foreach ($basket->extractions as $supplier):
            foreach ($supplier->items as $diffusion):
                $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
                $od = array();
                $od['order_id'] = $table->id;
                $od['diffusion_id'] = $diffusion->id;
                switch ($table->ordertype_id):
                    case Easysdi_shopHelper::ORDERTYPE_ESTIMATE: //Some diffusions may already have an estimation (free or with an automatic profile)
                        //A price could have been calculated even for a PRICING_FEE_WITHOUT_PROFILE, eg : free for organism member, then the diffusion is available
                        $cal_price = $basket->pricing->suppliers[$supplier->id]->products[$diffusion->id]->cal_total_amount_ti;
                        if ($diffusion->pricing == Easysdi_shopHelper::PRICING_FEE_WITHOUT_PROFILE && !isset($cal_price)):
                            $od['productstate_id'] = Easysdi_shopHelper::PRODUCTSTATE_SENT;
                        else:
                            $od['productstate_id'] = Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE;
                            $count_available++;
                        endif;
                        break;
                    case Easysdi_shopHelper::ORDERTYPE_DRAFT: //Set a state await for a draft
                        $od['productstate_id'] = Easysdi_shopHelper::PRODUCTSTATE_AWAIT;
                        break;
                    case Easysdi_shopHelper::ORDERTYPE_ORDER: //
                        $od['productstate_id'] = ($table->orderstate_id == Easysdi_shopHelper::ORDERSTATE_VALIDATION) ? Easysdi_shopHelper::PRODUCTSTATE_VALIDATION : Easysdi_shopHelper::PRODUCTSTATE_SENT;
                        break;
                endswitch;

                $od['created_by'] = JFactory::getUser()->id;
                $orderdiffusion->save($od);
                array_push($basketData['diffusions'], array($orderdiffusion->diffusion_id, $orderdiffusion->productstate_id));
                array_push($products, $orderdiffusion);

                //Save properties
                foreach ($diffusion->properties as $property):
                    foreach ($property->values as $value):
                        $orderpropertyvalue = JTable::getInstance('orderpropertyvalue', 'Easysdi_shopTable');
                        $v = array();
                        $v['orderdiffusion_id'] = $orderdiffusion->id;
                        $v['property_id'] = $property->id;
                        $v['propertyvalue_id'] = $value->id;
                        $v['propertyvalue'] = $value->value;
                        $v['created_by'] = JFactory::getUser()->id;
                        $orderpropertyvalue->save($v);
                    endforeach;
                endforeach;
            endforeach;
        endforeach;

        //In the case of an ESTIMATE, some estimations can be available directly (free and with profile),
        //the order status has to be updated accordingly
        if ($count_available > 0) {
            if ($count_available == $basket->extractionsNb) {
                $table->orderstate_id = Easysdi_shopHelper::ORDERSTATE_FINISH;
            } else {
                $table->orderstate_id = Easysdi_shopHelper::ORDERSTATE_PROGRESS;
            }
            $table->store(false);
            //Update the session data
            $basketData['orderstate_id'] = $table->orderstate_id;
        }

        $session = JFactory::getSession();
        $session->set('basketData', $basketData);

        //Save perimeters
        //unserialize if necessary
        if (!is_array($basket->extent->features)) {
            $features = json_decode($basket->extent->features);
        } else {
            $features = $basket->extent->features;
        }

        if (is_array($features)):
            foreach ($features as $feature):
                $orderperimeter = JTable::getInstance('orderperimeter', 'Easysdi_shopTable');
                $op = array();
                $op['order_id'] = $table->id;
                $op['perimeter_id'] = $basket->extent->id;
                $op['value'] = $feature->id;
                $op['text'] = $feature->name;
                $op['created_by'] = JFactory::getUser()->id;
                $orderperimeter->save($op);
            endforeach;
        else:
            $orderperimeter = JTable::getInstance('orderperimeter', 'Easysdi_shopTable');
            $op = array();
            $op['order_id'] = $table->id;
            $op['perimeter_id'] = $basket->extent->id;
            $op['value'] = $basket->extent->features;
            $op['created_by'] = JFactory::getUser()->id;
            $orderperimeter->save($op);
        endif;

        $session = JFactory::getSession();
        $basketProcess = array(
            'treated' => 0,
            'total' => $basket->extractionsNb,
            'rate' => 0
        );
        $session->set('basketProcess', $basketProcess);
        $session->set('basketProducts', array());

        if ($basket->pricing->isActivated) {
            $pricing = $basket->pricing;

            // sdi_pricing_order
            $pricingOrder = $this->getTable('PricingOrder', 'Easysdi_shopTable');
            $pricingOrderData = array(
                'order_id' => $table->id,
                'cfg_vat' => $pricing->cfg_vat,
                'cfg_currency' => $pricing->cfg_currency,
                'cfg_rounding' => $pricing->cfg_rounding,
                'cfg_overall_default_fee_te' => $pricing->cfg_overall_default_fee_te,
                'cfg_fee_apply_vat' => $pricing->cfg_fee_apply_vat,
                'cfg_free_data_fee' => $pricing->cfg_free_data_fee,
                'cal_total_amount_ti' => $pricing->cal_total_amount_ti,
                'cal_fee_ti' => $pricing->cal_fee_ti,
                'ind_lbl_category_order_fee' => $pricing->ind_lbl_category_order_fee
            );

            if ($pricingOrder->save($pricingOrderData) === true) {
                $this->saveSuppliers($basket, $pricing, $pricingOrder);
            }
        } else {
            $this->pushProductsToSession($products);
        }

        return true;
    }

    /**
     * saveSuppliers - save the suppliers of a basket
     * 
     * @param sdiBasket $basket
     * @param stdClass $pricing
     * @param sdiPricingOrder $pricingOrder
     * 
     * @return void
     * @since 4.3.0
     */
    private function saveSuppliers($basket, $pricing, $pricingOrder) {
        // sdi_pricing_order_supplier
        foreach ($pricing->suppliers as $supplierId => $supplier) {
            $this->saveSupplier($supplierId, $supplier, $pricingOrder);
        }
    }

    /**
     * saveSupplier - save one supplier
     * 
     * @param integer $supplierId
     * @param stdClass $supplier
     * @param sdiPricingOrder $pricingOrder
     * 
     * @return void
     * @since 4.3.0
     */
    private function saveSupplier($supplierId, $supplier, $pricingOrder) {
        $pricingOrderSupplier = $this->getTable('PricingOrderSupplier', 'Easysdi_shopTable');
        $pricingOrderSupplierData = array(
            'pricing_order_id' => $pricingOrder->id,
            'supplier_id' => $supplierId,
            'supplier_name' => $supplier->name,
            'cfg_internal_free' => $supplier->cfg_internal_free,
            'cfg_fixed_fee_te' => $supplier->cfg_fixed_fee_te,
            'cfg_fixed_fee_apply_vat' => $supplier->cfg_fixed_fee_apply_vat,
            'cfg_data_free_fixed_fee' => $supplier->cfg_data_free_fixed_fee,
            'cal_total_amount_ti' => $supplier->cal_total_amount_ti,
            'cal_fee_ti' => $supplier->cal_fee_ti,
            'cal_total_rebate_ti' => $supplier->cal_total_rebate_ti
        );

        if ($pricingOrderSupplier->save($pricingOrderSupplierData) === true)
            $this->pushProductsToSession($supplier->products, $pricingOrderSupplier->id);
    }

    /**
     * Save the sent date when all items, perimeter and pricing have been saved
     * https://forge.easysdi.org/issues/1252
     * @param array $basketData Session basket data
     * @return boolean Save state
     */
    public function finalSave($basketData) {

        //when everything is saved, change the sent date, to the order can be grabbed by services
        $data['sent'] = JFactory::getDate()->toSql();
        $table = $this->getTable();
        $table->load($basketData['order_id'], false);
        return $table->save($data);
    }

    /**
     * Push back product list to session
     * 
     * @param array $products
     * @param type $posId pricingOrderSupplier item ID
     */
    private function pushProductsToSession($products, $posId = null) {
        $session = JFactory::getSession();
        $basketProducts = $session->get('basketProducts');

        if ($posId === null) { // when pricing is not activated
            foreach ($products as $product) {
                array_push($basketProducts, array(
                    'product' => $product,
                    'productId' => $product->diffusion_id
                ));
            }
        } else { // sdi_pricing_order_supplier_product
            foreach ($products as $productId => $product) {
                array_push($basketProducts, array(
                    'productId' => $productId,
                    'product' => $product,
                    'pricingOrderSupplier_id' => $posId
                ));
            }
        }

        $session->set('basketProducts', $basketProducts);
    }

    /**
     * saveProduct - save a product
     * 
     * @return void
     * @since 4.3.0
     */
    public function saveProduct() {
        $session = JFactory::getSession();
        $basketProducts = $session->get('basketProducts');
        $basketProcess = $session->get('basketProcess');
        $basketData = $session->get('basketData');
        $currentProduct = $basketProducts[$basketProcess['treated']];
        extract($currentProduct);

        // reset time limit to avoid crash - files creation can take a while
        set_time_limit(30);

        if ((bool) JComponentHelper::getParams('com_easysdi_shop')->get('is_activated')) {
            $pricingOrderSupplierProduct = $this->getTable('PricingOrderSupplierProduct', 'Easysdi_shopTable');
            $pricingOrderSupplierProductData = array(
                'pricing_order_supplier_id' => $pricingOrderSupplier_id,
                'product_id' => $productId,
                'pricing_id' => $product->cfg_pricing_type,
                'cfg_pct_category_supplier_discount' => $product->cfg_pct_category_supplier_discount,
                'ind_lbl_category_supplier_discount' => $product->ind_lbl_category_supplier_discount,
                'cal_amount_data_te' => $product->cal_amount_data_te,
                'cal_total_amount_te' => $product->cal_total_amount_te,
                'cal_total_amount_ti' => $product->cal_total_amount_ti,
                'cal_total_rebate_ti' => $product->cal_total_rebate_ti
            );

            if ($pricingOrderSupplierProduct->save($pricingOrderSupplierProductData) === true && $pricingOrderSupplierProduct->pricing_id == 3) {
                // sdi_pricing_order_supplier_product_profile
                $pricingOrderSupplierProductProfile = $this->getTable('PricingOrderSupplierProductProfile', 'Easysdi_shopTable');
                $pricingOrderSupplierProductProfileData = array(
                    'pricing_order_supplier_product_id' => $pricingOrderSupplierProduct->id,
                    'pricing_profile_id' => $product->cfg_profile_id,
                    'pricing_profile_name' => $product->cfg_profile_name,
                    'cfg_fixed_fee_te' => $product->cfg_fixed_fee_te,
                    'cfg_apply_vat' => $product->cfg_apply_vat,
                    'cfg_surface_rate' => $product->cfg_surface_rate,
                    'cfg_min_fee' => $product->cfg_min_fee,
                    'cfg_max_fee' => $product->cfg_max_fee,
                    'cfg_pct_category_profile_discount' => $product->cfg_pct_category_profile_discount,
                    'ind_lbl_category_profile_discount' => $product->ind_lbl_category_profile_discount
                );

                $pricingOrderSupplierProductProfile->save($pricingOrderSupplierProductProfileData);
                $pricingOrderSupplierProductProfile = false;
            }
        }

        // Generate XML and PDF files
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('m.guid')
                ->from('#__sdi_diffusion d')
                ->innerJoin('#__sdi_version v on d.version_id = v.id')
                ->innerJoin('#__sdi_resource r on r.id = v.resource_id')
                ->innerJoin('#__sdi_metadata m on m.version_id = v.id')
                ->where('d.id=' . (int) $productId);
        $db->setQuery($query);
        $guid = $db->loadResult();


        // Sheet used to generate XML and PDF files
        $sheet = new Easysdi_catalogControllerSheet();

        $requestFolder = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderrequestFolder') . '/' . $basketData['order_id'];
        if (!file_exists($requestFolder)) {
            mkdir($requestFolder, 0755, true);
        }

        file_put_contents($requestFolder . '/' . $productId . '.xml', $sheet->exportXML($guid, FALSE));
        file_put_contents($requestFolder . '/' . $productId . '.pdf', $sheet->exportPDF($guid, FALSE));

        // Update session data
        $basketProcess['treated'] ++;
        $basketProcess['rate'] = intval($basketProcess['treated'] / $basketProcess['total'] * 100);
        $session->set('basketProcess', $basketProcess);
    }

    /**
     * Clean old items in tables
     * @param int $order_id id of the order to clenup
     * @return boolean
     */
    private function cleanTables($order_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Select orderdiffusion_id
        $query->select('id')
                ->from('#__sdi_order_diffusion')
                ->where('order_id = ' . (int) $order_id);
        $db->setQuery($query);
        $orderdiffusion = $db->loadColumn();

        //Delete linked property values
        foreach ($orderdiffusion as $id):
            $query = $db->getQuery(true);
            $query->delete('#__sdi_order_propertyvalue')
                    ->where('orderdiffusion_id =' . (int) $id);
            $db->setQuery($query);
            $db->execute();
        endforeach;

        //Delete linked diffusion
        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_diffusion')
                ->where('order_id = ' . (int) $order_id);
        $db->setQuery($query);
        if (!$db->execute())
            return false;

        //Delete linked perimeters
        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_perimeter')
                ->where('order_id = ' . (int) $order_id);
        $db->setQuery($query);
        if (!$db->execute())
            return false;

        //Delete Pricing :
        //Foreign keys with "cascade on delete" constraints, allow
        //deletion of all the pricing data tree in one query.
        //Tables concerning by the cascade action are :
        // - #__sdi_order_supplier
        // - #__sdi_order_supplier_product
        // - #__sdi_order_supplier_product_profile
        $query = $db->getQuery(true);
        $query->delete('#__sdi_pricing_order')
                ->where('order_id = ' . (int) $order_id);
        $db->setQuery($query);
        if (!$db->execute())
            return false;

        return true;
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     */
    public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

}
