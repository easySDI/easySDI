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

require_once JPATH_COMPONENT . "/libraries/easysdi/sdiBasket.php";
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/controllers/sheet.php';

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelBasket extends JModelLegacy {

    var $_item = null;
    
    // ORDERSTATE
    const ORDERSTATE_ARCHIVED           = 1;
    const ORDERSTATE_HISTORIZED         = 2;
    const ORDERSTATE_FINISH             = 3;
    const ORDERSTATE_AWAIT              = 4;
    const ORDERSTATE_PROGRESS           = 5;
    const ORDERSTATE_SENT               = 6;
    const ORDERSTATE_SAVED              = 7;
    const ORDERSTATE_VALIDATION         = 8;
    const ORDERSTATE_REJECTED           = 9; // rejected by thirdparty
    const ORDERSTATE_REJECTED_SUPPLIER  = 10; // rejected by supplier
    
    // ORDERTYPE
    const ORDERTYPE_ORDER       = 1;
    const ORDERTYPE_ESTIMATE    = 2;
    const ORDERTYPE_DRAFT       = 3;
    
    // ROLE
    const ROLE_MEMBER                   = 1;
    const ROLE_RESOURCEMANAGER          = 2;
    const ROLE_METADATARESPONSIBLE      = 3;
    const ROLE_METADATAEDITOR           = 4;
    const ROLE_DIFFUSIONMANAGER         = 5;
    const ROLE_PREVIEWMANAGER           = 6;
    const ROLE_EXTRACTIONRESPONSIBLE    = 7;
    const ROLE_PRICINGMANAGER           = 9;
    const ROLE_VALIDATIONMANAGER        = 10;
    
    // PRODUCTSTATE
    const PRODUCT_AVAILABLE         = 1;
    const PRODUCT_AWAIT             = 2;
    const PRODUCT_SENT              = 3;
    const PRODUCT_VALIDATION        = 4;
    const PRODUCT_REJECTED          = 5; // rejected by thirdparty
    const PRODUCT_REJECTED_SUPPLIER = 6; // rejected by supplier

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
        if (empty($basket))
            return false;

        $data = array();
        
        //Save order object
        if (empty($basket->id))
            $data['id'] = 0;
        else
            $data['id'] = $basket->id;

        if (empty($basket->name)):
            $data['name'] = JFactory::getUser()->name . ' - ' . JFactory::getDate();            
        else:
            $data['name'] = $basket->name;
        endif;
        
        $data['sent'] = date('Y-m-d H:i:s');

        $data['created'] = $basket->created;
        $data['created_by'] = $basket->created_by;
        $data['buffer'] = $basket->buffer;
        $data['surface'] = $basket->extent->surface;
        $data['thirdparty_id'] = (($basket->thirdparty != -1)&&($basket->thirdparty != ""))? $basket->thirdparty : NULL;
        switch (JFactory::getApplication()->input->get('action', 'save', 'string')) {
            case 'order':
                $data['ordertype_id'] = self::ORDERTYPE_ORDER;
                $data['orderstate_id'] = ($data['thirdparty_id'] !== NULL) ? self::ORDERSTATE_VALIDATION : self::ORDERSTATE_SENT;
                break;
            case 'estimate':
                $data['ordertype_id'] = self::ORDERTYPE_ESTIMATE;
                $data['orderstate_id'] = self::ORDERSTATE_SENT;
                break;
            case 'draft':
                $data['ordertype_id'] = self::ORDERTYPE_DRAFT;
                $data['orderstate_id'] = self::ORDERSTATE_SAVED;
                break;
        }
        $data['user_id'] = sdiFactory::getSdiUser()->id;


        $table = $this->getTable();
        if ($table->save($data) === true) {
            $basketData = array(
                'orderstate_id' => $table->orderstate_id,
                'diffusions'    => array(),
                'order_id'      => $table->id,
                'thirdparty_id' => $table->thirdparty_id,
                'order_name'    => $table->name
            );
            
            if (!empty($basket->id)) {
                $this->cleanTables($basket->id);
            }

            //Save diffusions
            foreach ($basket->extractions as $diffusion):
                $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
                $od = array();
                $od['order_id'] = $table->id;
                $od['diffusion_id'] = $diffusion->id;
                $od['productstate_id'] = ($table->orderstate_id == self::ORDERSTATE_VALIDATION) ? self::PRODUCT_VALIDATION : self::PRODUCT_SENT;
                
                $orderdiffusion->save($od);
                array_push($basketData['diffusions'], $orderdiffusion->diffusion_id);
                
                //Save properties
                foreach ($diffusion->properties as $property):
                    foreach ($property->values as $value):
                        $orderpropertyvalue = JTable::getInstance('orderpropertyvalue', 'Easysdi_shopTable');
                        $v = array();
                        $v['orderdiffusion_id'] = $orderdiffusion->id;
                        $v['property_id'] = $property->id;
                        $v['propertyvalue_id'] = $value->id;
                        $v['propertyvalue'] = $value->value;
                        $orderpropertyvalue->save($v);
                    endforeach;
                endforeach;
            endforeach;
            
            $session =& JFactory::getSession();
            $session->set('basketData', $basketData);

            //Save perimeters
            if (is_array($basket->extent->features)):
                foreach ($basket->extent->features as $feature):
                    $orderperimeter = JTable::getInstance('orderperimeter', 'Easysdi_shopTable');
                    $op = array();
                    $op['order_id'] = $table->id;
                    $op['perimeter_id'] = $basket->extent->id;
                    $op['value'] = $feature->id;
                    $op['text'] = $feature->name;
                    $orderperimeter->save($op);
                endforeach;
            else:
                $orderperimeter = JTable::getInstance('orderperimeter', 'Easysdi_shopTable');
                $op = array();
                $op['order_id'] = $table->id;
                $op['perimeter_id'] = $basket->extent->id;
                $op['value'] = $basket->extent->features;
                $orderperimeter->save($op);
            endif;
            
            // PRICING
            // rebuild extractions array to allow by supplier grouping
            Easysdi_shopHelper::extractionsBySupplierGrouping($basket);

            // calculate price for the current basket (only if surface is defined)
            Easysdi_shopHelper::basketPriceCalculation($basket);
            
            $pricing = $basket->pricing;
            
            // sdi_pricing_order
            $pricingOrder = $this->getTable('PricingOrder', 'Easysdi_shopTable');
            $pricingOrderData = array(
                'order_id'                      => $table->id,
                'cfg_vat'                       => $pricing->cfg_vat,
                'cfg_currency'                  => $pricing->cfg_currency,
                'cfg_rounding'                  => $pricing->cfg_rounding,
                'cfg_overall_default_fee'       => $pricing->cfg_overall_default_fee,
                'cfg_free_data_fee'             => $pricing->cfg_free_data_fee,
                'cal_total_amount_ti'           => $pricing->cal_total_amount_ti,
                'cal_fee_ti'                    => $pricing->cal_fee_ti,
                'ind_lbl_category_order_fee'    => $pricing->ind_lbl_category_order_fee
            );
            
            if($pricingOrder->save($pricingOrderData) === true){
                $this->saveSuppliers($basket, $pricing, $pricingOrder);
            }
            // ENDOF PRICING
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
    private function saveSuppliers($basket, $pricing, $pricingOrder){
        $session =& JFactory::getSession();
        $basketProcess = array(
            'treated'   => 0,
            'total'     => $basket->extractionsNb,
            'rate'      => 0
        );
        $session->set('basketProcess', $basketProcess);
        $session->set('basketProducts', array());

        // sdi_pricing_order_supplier
        foreach($pricing->suppliers as $supplierId => $supplier){
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
    private function saveSupplier($supplierId, $supplier, $pricingOrder){
        $pricingOrderSupplier = $this->getTable('PricingOrderSupplier', 'Easysdi_shopTable');
        $pricingOrderSupplierData = array(
            'pricing_order_id'          => $pricingOrder->id,
            'supplier_id'               => $supplierId,
            'supplier_name'             => $supplier->name,
            'cfg_internal_free'         => $supplier->cfg_internal_free,
            'cfg_fixed_fee_ti'          => $supplier->cfg_fixed_fee_ti,
            'cfg_data_free_fixed_fee'   => $supplier->cfg_data_free_fixed_fee,
            'cal_total_amount_ti'       => $supplier->cal_total_amount_ti,
            'cal_fee_ti'                => $supplier->cal_fee_ti,
            'cal_total_rebate_ti'       => $supplier->cal_total_rebate_ti
        );

        if($pricingOrderSupplier->save($pricingOrderSupplierData) === true){
            $session =& JFactory::getSession();
            $basketProducts = $session->get('basketProducts');
            // sdi_pricing_order_supplier_product
            foreach($supplier->products as $productId => $product){
                array_push($basketProducts, array(
                    'productId'             => $productId,
                    'product'               => $product,
                    'pricingOrderSupplier_id'  => $pricingOrderSupplier->id
                ));
            }
            $session->set('basketProducts', $basketProducts);
        }
    }
    
    /**
     * saveProduct - save a product
     * 
     * @return void
     * @since 4.3.0
     */
    public function saveProduct(){
        $session =& JFactory::getSession();
        $basketProducts = $session->get('basketProducts');
        $basketProcess = $session->get('basketProcess');
        $currentProduct = $basketProducts[$basketProcess['treated']];
        extract($currentProduct);
        
        // reset time limit to avoid crash - files creation can take a while
        set_time_limit(30);

        $pricingOrderSupplierProduct = $this->getTable('PricingOrderSupplierProduct', 'Easysdi_shopTable');
        $pricingOrderSupplierProductData = array(
            'pricing_order_supplier_id'             => $pricingOrderSupplier_id,
            'product_id'                            => $productId,
            'pricing_id'                            => $product->cfg_pricing_type,
            'cfg_pct_category_supplier_discount'    => $product->cfg_pct_category_supplier_discount,
            'ind_lbl_category_supplier_discount'    => $product->ind_lbl_category_supplier_discount,
            'cal_amount_data_te'                    => $product->cal_amount_data_te,
            'cal_total_amount_te'                   => $product->cal_total_amount_te,
            'cal_total_amount_ti'                   => $product->cal_total_amount_ti,
            'cal_total_rebate_ti'                   => $product->cal_total_rebate_ti
        );

        if($pricingOrderSupplierProduct->save($pricingOrderSupplierProductData) === true && $pricingOrderSupplierProduct->pricing_id == 3){

            // sdi_pricing_order_supplier_product_profile
            $pricingOrderSupplierProductProfile = $this->getTable('PricingOrderSupplierProductProfile', 'Easysdi_shopTable');
            $pricingOrderSupplierProductProfileData = array(
                'pricing_order_supplier_product_id' => $pricingOrderSupplierProduct->id,
                'pricing_profile_id'                => $product->cfg_profile_id,
                'pricing_profile_name'              => $product->cfg_profile_name,
                'cfg_fixed_fee'                     => $product->cfg_fixed_fee,
                'cfg_surface_rate'                  => $product->cfg_surface_rate,
                'cfg_min_fee'                       => $product->cfg_min_fee,
                'cfg_max_fee'                       => $product->cfg_max_fee,
                'cfg_pct_category_profile_discount' => $product->cfg_pct_category_profile_discount,
                'ind_lbl_category_profile_discount'  => $product->ind_lbl_category_profile_discount
            );

            $pricingOrderSupplierProductProfile->save($pricingOrderSupplierProductProfileData);
            $pricingOrderSupplierProductProfile = false;
        }

        // Generate XML and PDF files
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('m.guid')
                ->from('#__sdi_diffusion d')
                ->innerJoin('#__sdi_version v on d.version_id = v.id')
                ->innerJoin('#__sdi_resource r on r.id = v.resource_id')
                ->innerJoin('#__sdi_metadata m on m.version_id = v.id')
                ->where('d.id='.(int)$productId);
        $db->setQuery($query);
        $guid = $db->loadResult();

        // Sheet used to generate XML and PDF files
        $sheet = new Easysdi_catalogControllerSheet();
        $requestFolder = JPATH_BASE . JComponentHelper::getParams('com_easysdi_shop')->get('orderrequestFolder').'/';
        if(!file_exists($requestFolder))
            mkdir($requestFolder, 0755, true);
        
        file_put_contents($requestFolder.$pricingOrderSupplierProduct->guid.'.xml', $sheet->exportXML($guid, FALSE));
        file_put_contents($requestFolder.$pricingOrderSupplierProduct->guid.'.pdf', $sheet->exportPDF($guid, FALSE));
        
        // Update session data
        $basketProcess['treated']++;
        $basketProcess['rate'] = intval($basketProcess['treated']/$basketProcess['total']*100);
        $session->set('basketProcess', $basketProcess);
    }

    private function cleanTables($order_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Select orderdiffusion_id
        $query->select('id')
                ->from('#__sdi_order_diffusion')
                ->where('order_id = ' . (int)$order_id);
        $db->setQuery($query);
        $orderdiffusion = $db->loadColumn();

        foreach ($orderdiffusion as $id):
            $query = $db->getQuery(true);
            $query->delete('#__sdi_order_propertyvalue')
                    ->where('orderdiffusion_id =' . (int)$id);
            $db->setQuery($query);
            $db->execute();
        endforeach;

        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_diffusion')
                ->where('order_id = ' . (int)$order_id);

        $db->setQuery($query);
        if (!$db->execute())
            return false;

        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_perimeter')
                ->where('order_id = ' . (int)$order_id);

        $db->setQuery($query);
        if (!$db->execute())
            return false;

        return true;
    }

    public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

}