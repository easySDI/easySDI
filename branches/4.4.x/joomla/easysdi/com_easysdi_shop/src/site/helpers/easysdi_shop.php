<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;


require_once JPATH_SITE . '/administrator/components/com_easysdi_shop/tables/diffusion.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPricingProfile.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/basket_string_parser.php';
require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';

jimport('joomla.application.component.helper');

abstract class Easysdi_shopHelper {

    // PRICING
    const PRICING_FREE = 1;
    const PRICING_FEE_WITHOUT_PROFILE = 2;
    const PRICING_FEE_WITH_PROFILE = 3;
    //ROLE
    const ROLE_MEMBER = 1;
    const ROLE_RESOURCEMANAGER = 2;
    const ROLE_METADATARESPONSIBLE = 3;
    const ROLE_METADATAEDITOR = 4;
    const ROLE_DIFFUSIONMANAGER = 5;
    const ROLE_PREVIEWMANAGER = 6;
    const ROLE_EXTRACTIONRESPONSIBLE = 7;
    const ROLE_PRICINGMANAGER = 9;
    const ROLE_VALIDATIONMANAGER = 10;
    // ORDERSTATE
    const ORDERSTATE_HISTORIZED = 2;
    const ORDERSTATE_FINISH = 3;
    const ORDERSTATE_AWAIT = 4;
    const ORDERSTATE_PROGRESS = 5;
    const ORDERSTATE_SENT = 6;
    const ORDERSTATE_SAVED = 7;
    const ORDERSTATE_VALIDATION = 8;
    const ORDERSTATE_REJECTED = 9; // rejected by thirdparty
    const ORDERSTATE_REJECTED_SUPPLIER = 10; // rejected by supplier
    // ORDERTYPE
    const ORDERTYPE_ORDER = 1;
    const ORDERTYPE_ESTIMATE = 2;
    const ORDERTYPE_DRAFT = 3;
    //PRODUCTSTATE
    const PRODUCTSTATE_AVAILABLE = 1;
    const PRODUCTSTATE_AWAIT = 2;
    const PRODUCTSTATE_SENT = 3;
    const PRODUCTSTATE_VALIDATION = 4;
    const PRODUCTSTATE_REJECTED_TP = 5; //product rejected by third party
    const PRODUCTSTATE_REJECTED_SUPPLIER = 6; // product rejected by supplier
    const PRODUCTSTATE_DELETED = 7;
    const PRODUCTSTATE_BLOCKED = 8; //too many otp attempts
    //ORDRE VIEWS
    const ORDERVIEW_ORDER = 1; //client
    const ORDERVIEW_REQUEST = 2; //provider -> extraction
    const ORDERVIEW_VALIDATION = 3; // for validation
    const ORDERVIEW_ADMIN = 4; // backend view
    // Order productmining
    const PRODUCTMININGAUTO = 1; //auto
    const PRODUCTMININGMANUAL = 2; //manual
    // Extract storage
    const EXTRACTSTORAGE_LOCAL = 1;
    const EXTRACTSTORAGE_REMOTE = 2;

    /**
     * Add a product to basket in session
     * @param string $item : json {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
     * @param bool $force : default to false, force the product into the basket, even if there's a perimeter incompatibility
     */
    public static function addToBasket($item, $force = false) {
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

        if (empty($item)):
            $return['COUNT'] = 0;
            echo json_encode($return);
            die();
        endif;

        $decoded_item = json_decode($item);

        //If not logged user, check if the extraction is not restricted by user extent
        $user = JFactory::getUser();
        if ($user->guest) {
            $diffusion = JTable::getInstance('diffusion', 'Easysdi_shopTable');
            $diffusion->load($decoded_item->id);
            if ($diffusion->restrictedperimeter == 1) {
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_TRY_TO_ADD_NOT_ALLOWED_DIFFUSION');
                echo json_encode($return);
                die();
            }
        }

        $extraction = new sdiExtraction($decoded_item);

        //Get the session basket content
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        if (empty($basket) || !$basket) {
            $basket = new sdiBasket();
        }

        if (is_null($basket->sdiUser->id) && !$user->guest) {
            $basket->sdiUser = sdiFactory::getSdiUser();
        }

        //Add the new extraction to the basket
        if (count($basket->extractions) == 0):
            //First add
            $basket->addExtraction($extraction);
            $basket->setPerimeters($extraction->perimeters);
        else:
            //There is already extractions in the basket
            //Check if the diffusion is not already in the basket
            foreach ($basket->extractions as $inextraction):
                if ($inextraction->id == $extraction->id):
                    $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_DIFFUSION_ALREADY_IN_BASKET');
                    echo json_encode($return);
                    die();
                endif;
            endforeach;
            //Check if there is at least one common perimeter within all the extractions in the basket
            //Check if buffer is authorized
            $common = array();
            if ($extraction->perimeters):
                foreach ($extraction->perimeters as $perimeter):
                    foreach ($basket->perimeters as $bperimeter):
                        if ($bperimeter->id == $perimeter->id):
                            foreach ($common as $cperimeter):
                                if ($bperimeter->id == $cperimeter->id):
//                                    if ($bperimeter->allowedbuffer == 0 || $perimeter->allowedbuffer == 0):
//                                        $cperimeter->allowedbuffer = 0;
                                    continue 2;
//                                    endif;
                                endif;
                            endforeach;
//                            if ($bperimeter->allowedbuffer == 0):
                            $common[] = $bperimeter;
//                            else:
//                                $common[] = $perimeter;
//                            endif;
                        endif;
                    endforeach;
                endforeach;
            endif;
            if (count($common) == 0):
                //There is no more common perimeter between the extraction in the basket
                //Extraction can not be added, send a message to the user
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_NO_COMMON_PERIMETER');
                echo json_encode($return);
                die();
            endif;
            //If there is already a perimeter defined for the basket, check if this perimeter is allowed for the new extraction
            if (!empty($basket->extent) && !$force):
                $check = false;
                foreach ($common as $p):
                    if ($p->id == $basket->extent->id):
                        //New extraction is compatible with the already defined extent of the basket
                        $check = true;
                    endif;
                endforeach;
                if (!$check):
                    $return['DIALOG'] = JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_EXISTING_EXTENT');
                    JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', $item);
                    echo json_encode($return);
                    die();
                endif;
            elseif (!empty($basket->extent) && $force):
                //Clean session
                JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', null);
                $basket->extent = null;
            endif;
            $basket->addExtraction($extraction);
            $basket->setPerimeters($common);
        endif;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        $return['COUNT'] = count($basket->extractions);
        echo json_encode($return);
        die();
    }

    /**
     * Removes an element from a basket in session
     * @param int $id a product id
     */
    public static function removeFromBasket($id) {
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

        if (empty($id)):
            $return['COUNT'] = 0;
            echo json_encode($return);
            die();
        endif;

        //Get the session basket content
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        if (empty($basket)) :
            $return['COUNT'] = 0;
            echo json_encode($return);
            die();
        endif;

        $basket->removeExtraction($id);

        if (count($basket->extractions) == 0):
            $basket->perimeters = array();
            $basket->extent = null;
        else:
            $common = array();
            foreach ($basket->extractions as $extraction):
                foreach ($extraction->perimeters as $perimeter):
//                    foreach ($common as $cperimeter):
//                        if ($perimeter->id == $cperimeter->id):
//                            if ($perimeter->allowedbuffer == 0 || $cperimeter->allowedbuffer == 0):
//                                $cperimeter->allowedbuffer = 0;
//                                continue 2;
//                            endif;
//                        endif;
//                    endforeach;
                    $common[] = $perimeter;
                endforeach;
            endforeach;

            $basket->setPerimeters($common);
        endif;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
    }

    /**
     * Cancel a addToBasket() process
     */
    public static function abortAdd() {
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', null);
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $return['ABORT'] = count($basket->extractions);
        echo json_encode($return);
        die();
    }

    /**
     * Add an extent to a basket in session
     * @param string $item : json {"id":perimeter_id,"name":perimeter_name,"features":[{"id": feature_id, "name":feature_name, "level":level_code}]}
     */
    public static function addExtentToBasket($item) {
        //add extent if defined
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $basket->extent = empty($item) ? null : json_decode($item);
        $basket->freeperimetertool = empty($item) ? '' : json_decode($item)->freeperimetertool;
        if (is_null($basket->sdiUser->id)) {
            $basket->sdiUser = sdiFactory::getSdiUser();
        }
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        $return = array(
            'MESSAGE' => 'OK',
            'extent' => $basket->extent
        );

        //recalculate pricing if enable
        // rebuild extractions array to allow by supplier grouping
        self::extractionsBySupplierGrouping($basket);

        // calculate price for the current basket (only if surface is defined)
        self::basketPriceCalculation($basket);

        $return['pricing'] = $basket->pricing;

        header('content-type: application/json');
        echo json_encode($return);
        die();
    }

    /**
     * getHTMLOrderPerimeter - returns html content perimeter recap
     * @param order $item
     */
    public static function getHTMLOrderPerimeter($item, $allowDownload = false) {
        $params = JComponentHelper::getParams('com_easysdi_shop');
        $paramsarray = $params->toArray();

        $document = JFactory::getDocument();
        $document->addScript(Juri::root(true) . '/components/com_easysdi_shop/views/order/tmpl/order.js?v=' . sdiFactory::getSdiFullVersion());

        $mapscript = Easysdi_mapHelper::getMapScript($paramsarray['ordermap'], true);
        ?> <div class="row-fluid" >
        <?php
        $surfaceTxt = '';
        if (!empty($item->basket->extent) && !empty($item->basket->extent->surface)) :
            $surfaceTxt.= ' (';
            if (floatval($item->basket->extent->surface) > intval($paramsarray['maxmetervalue'])):
                $surfaceTxt.= round(floatval($item->basket->extent->surface) / 1000000, intval($paramsarray['surfacedigit']));
                $surfaceTxt.= JText::_('COM_EASYSDI_SHOP_BASKET_KILOMETER');
            else:
                $surfaceTxt.= round(floatval($item->basket->extent->surface), intval($paramsarray['surfacedigit']));
                $surfaceTxt.= JText::_('COM_EASYSDI_SHOP_BASKET_METER');
            endif;
            $surfaceTxt.= ')';
        endif;
        ?>

            <div class="shop-perimeter">
                <div class="row-fluid shop-perimeter-title-row">
                    <h2><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER') . $surfaceTxt; ?></h2>
                </div>


                <div class="row-fluid shop-perimeter-map-row" >
                    <div class="map-recap span8" >
                        <div >
                            <?php
                            echo $mapscript;
                            ?>
                        </div>                
                    </div>
                    <div  class="value-recap span4" >
                        <?php if (!empty($item->basket->extent->level)) : ?>
                            <div id="indoor-level" class="row-fluid" >
                                <div><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_LEVEL'); ?></h4>
                                    <span><?php echo json_decode($item->basket->extent->level)->label; ?></span>                            
                                </div>                                
                            </div>
                        <?php endif; ?>

                        <div id="perimeter-recap">
                            <div id="perimeter-recap-details-title">
                                <h4><?php echo JText::_($item->basket->extent->name); ?></h4>
                            </div>
                            <?php
                            if (is_array($item->basket->extent->features)):
                                ?> <div id="perimeter-recap-details" style="overflow-y:auto; height:100px;"> <?php
                                foreach ($item->basket->extent->features as $feature):
                                    ?>
                                        <div><?php echo $feature->name; ?></div>
                                        <?php
                                    endforeach;
                                    ?> </div>   <?php
                            endif;
                            ?>
                            <?php if ($allowDownload): ?>
                                <div id="perimeter-recap-details-download">
                                    <?php echo JText::_('COM_EASYSDI_SHOP_ORDER_DOWNLOAD_PERIMETER_AS'); ?>
                                    <span id ="perimeter-recap-details-download-gml"><a href="#" onclick="downloadPerimeter('GML',<?php echo $item->id; ?>);
                                                        return false;" ><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_DOWNLOAD_PERIMETER_AS_GML'); ?></a>, </span>
                                    <span id ="perimeter-recap-details-download-kml"><a href="#" onclick="downloadPerimeter('KML',<?php echo $item->id; ?>);
                                                        return false;" ><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_DOWNLOAD_PERIMETER_AS_KML'); ?></a>, </span>
                                    <span id ="perimeter-recap-details-download-dxf"><a href="#" onclick="downloadPerimeter('DXF',<?php echo $item->id; ?>);
                                                        return false;" ><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_DOWNLOAD_PERIMETER_AS_DXF'); ?></a>, </span>
                                    <span id ="perimeter-recap-details-download-geojson"><a href="#" onclick="downloadPerimeter('GeoJSON',<?php echo $item->id; ?>);
                                                        return false;" ><?php echo JText::_('COM_EASYSDI_SHOP_ORDER_DOWNLOAD_PERIMETER_AS_GEOJSON'); ?></a></span>                                    
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <?php
    }

    /**
     * Recursively delete a directory that is not empty
     * @param string $dir directory path
     * See : http://php.net/manual/fr/function.rmdir.php
     */
    public static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        Easysdi_shopHelper::rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * 
     * Rebuild url from its different parts - check http_build_url() PHP Manual
     * http_build_url() is part of pecl_http
     * unparse_url() does the work with or without http_build_url()
     * 
     * @param mixed $url | string or array as returned by parse_url()
     * @param mixed $parts | same as $url
     * @return string
     */
    public static function unparse_url($url, $parts = array()) {
        if (function_exists('http_build_url'))
            return http_build_url($url, $parts);

        $parsed_url = array_merge($url, $parts);

        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * rounding - round a value
     * 
     * @param float $value
     * @param float $rounding
     * @return float
     * @since 4.3.0
     */
    public static function rounding($value, $rounding = 0.01) {
        return round($value / $rounding, 0) * $rounding;
    }

    /**
     * extractionsBySupplierGrouping - reword extractions array grouping products by suppliers
     * 
     * @param type $basket
     * @return voide
     * @since 4.3.0
     */
    public static function extractionsBySupplierGrouping(&$basket) {
        $myItems = $basket->extractions;
        $basket->extractionsNb = count($myItems);
        $basket->extractions = array();
        if ($basket->extractionsNb) {
            foreach ($myItems as $myItem) {
                if (!isset($basket->extractions[$myItem->organism_id])) {
                    $basket->extractions[$myItem->organism_id] = new stdClass();
                    $basket->extractions[$myItem->organism_id]->id = $myItem->organism_id;
                    $basket->extractions[$myItem->organism_id]->name = $myItem->organism;
                    $basket->extractions[$myItem->organism_id]->items = array();
                }

                array_push($basket->extractions[$myItem->organism_id]->items, $myItem);
            }
        }
    }

    /**
     * basketReloadSavedPricing - reload pricing from database
     * Do not use it in basket view, is designed for database saved orders (views: order, request, validation)
     * @param type $basket
     * @since 4.3.0
     */
    public static function basketReloadSavedPricing(&$basket) {
        // init prices object
        $prices = new stdClass();
        $prices->isActivated = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');

        // test if pricing is activated
        if ($prices->isActivated) {

            //get pricing config
            $prices->cfg_vat = null;
            $prices->cfg_currency = null;
            $prices->cfg_rounding = null;
            $prices->cfg_overall_default_fee_te = null;
            $prices->cfg_free_data_fee = null;
            $prices->cal_total_amount_ti = null;
            $prices->cal_fee_ti = null;

            $prices->pricing_order_id = null;

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('po.*')
                    ->from('#__sdi_pricing_order po')
                    ->where('po.order_id=' . (int) $basket->id);
            $db->setQuery($query);
            $orderPricing = $db->loadObject();
            //if values are found, update object
            if (!is_null($orderPricing)) {
                $prices->cfg_vat = (float) $orderPricing->cfg_vat;
                $prices->cfg_currency = $orderPricing->cfg_currency;
                $prices->cfg_rounding = (float) $orderPricing->cfg_rounding;
                $prices->cfg_overall_default_fee_te = (float) $orderPricing->cfg_overall_default_fee_te;
                $prices->cfg_fee_apply_vat = (bool) $orderPricing->cfg_fee_apply_vat;
                $prices->cfg_free_data_fee = (bool) $orderPricing->cfg_free_data_fee;
                $prices->cal_total_amount_ti = $orderPricing->cal_total_amount_ti;
                $prices->cal_fee_ti = $orderPricing->cal_fee_ti;
                $prices->pricing_order_id = (int) $orderPricing->id;
            }

            // get the surface ordered - default to 0
            $prices->surface = isset($basket->extent) && isset($basket->extent->surface) ? $basket->extent->surface / 1000000 : 0;

            // get prices by supplier
            if ($prices->pricing_order_id) {
                $prices->suppliers = array();
                $prices->hasFeeWithoutPricingProfileProduct = false;
                foreach ($basket->extractions as $supplier_id => $supplier) {
                    $prices->suppliers[$supplier_id] = self::basketReloadSavedPricingBySupplier($supplier, $prices);
                    if ($prices->suppliers[$supplier_id]->hasFeeWithoutPricingProfileProduct) {
                        $prices->hasFeeWithoutPricingProfileProduct = true;
                    }
                }
            }
        }

        $basket->pricing = $prices;
    }

    /**
     * basketReloadSavedPricingBySupplier - reload pricing by supplier
     * Do not use it in basket view, is designed for database saved orders (views: order, request, validation)
     * @param int $pricing_order_id
     * @param type $supplier
     * @param type $prices
     * @since 4.3.2
     */
    private static function basketReloadSavedPricingBySupplier($supplier, $prices) {

        // init provider sub-object
        $provider = new stdClass();
        $provider->id = (int) $supplier->id;

        // get organism pricing
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('pos.id, pos.supplier_name, '
                        . 'pos.cfg_internal_free, '
                        . 'pos.cfg_fixed_fee_te, '
                        . 'pos.cfg_fixed_fee_apply_vat, '
                        . 'pos.cfg_data_free_fixed_fee, '
                        . 'pos.cal_total_rebate_ti, '
                        . 'pos.cal_fee_ti, '
                        . 'pos.cal_total_amount_ti')
                ->from('#__sdi_pricing_order_supplier pos')
                ->where('pos.pricing_order_id=' . $prices->pricing_order_id)
                ->where('pos.supplier_id=' . $provider->id);
        $db->setQuery($query);
        $priceOrdSupplier = $db->loadObject();
        // config
        $provider->name = $priceOrdSupplier->supplier_name;
        $provider->cfg_internal_free = (bool) $priceOrdSupplier->cfg_internal_free;
        $provider->cfg_fixed_fee_te = $priceOrdSupplier->cfg_fixed_fee_te;
        $provider->cfg_fixed_fee_apply_vat = (bool) $priceOrdSupplier->cfg_fixed_fee_apply_vat;
        $provider->cfg_data_free_fixed_fee = (bool) $priceOrdSupplier->cfg_data_free_fixed_fee;

        //Calculate fixed fee taxes included
        $provider->cfg_fixed_fee_ti = ($provider->cfg_fixed_fee_apply_vat) ? $provider->cfg_fixed_fee_te * (1 + $prices->cfg_vat / 100) : $provider->cfg_fixed_fee_te;

        //
        $provider->pricing_order_supplier_id = (int) $priceOrdSupplier->id;

        // gwt the provider tax
        $provider->cal_fee_ti = $priceOrdSupplier->cal_fee_ti;

        // get total amount for this provider
        $provider->cal_total_amount_ti = $priceOrdSupplier->cal_total_amount_ti;
        $provider->cal_total_rebate_ti = $priceOrdSupplier->cal_total_rebate_ti;

        //TODO check this
        $prices->supplierRebate = new stdClass();
        $prices->supplierRebate->pct = 0;
        $prices->supplierRebate->name = '';


        // calculate price for each product and increment the provider sub-total
        $provider->products = array();
        $provider->hasFeeWithoutPricingProfileProduct = false;
        foreach ($supplier->items as $product) {
            $provider->products[$product->id] = self::basketReloadSavedPricingByProduct($provider->pricing_order_supplier_id, $product, $prices);
            if ($product->pricing == self::PRICING_FEE_WITHOUT_PROFILE) {
                $provider->hasFeeWithoutPricingProfileProduct = true;
            }
        }

        return $provider;
    }

    /**
     * basketReloadSavedPricingByProduct - reload pricing by supplier
     * Do not use it in basket view, is designed for database saved orders (views: order, request, validation)
     * @param type $product
     * @param type $prices
     */
    private static function basketReloadSavedPricingByProduct($pricing_order_supplier_id, $product, $prices) {
        // init product sub-object
        $price = new stdClass();

        // get product pricing
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('posp.*')
                ->from('#__sdi_pricing_order_supplier_product posp')
                ->where('posp.pricing_order_supplier_id=' . $pricing_order_supplier_id)
                ->where('posp.product_id=' . $product->id);
        $db->setQuery($query);
        $priceOrdSupProduct = $db->loadObject();

        $price->cfg_pricing_type = $priceOrdSupProduct->pricing_id;

        // total data te
        $price->cal_amount_data_te = $priceOrdSupProduct->cal_amount_data_te;
        // final price TE
        $price->cal_total_amount_te = $priceOrdSupProduct->cal_total_amount_te;
        // final price TI
        $price->cal_total_amount_ti = $priceOrdSupProduct->cal_total_amount_ti;
        $price->cal_total_rebate_ti = $priceOrdSupProduct->cal_total_rebate_ti;

        // rebate based on category and supplier
        $price->cfg_pct_category_supplier_discount = $priceOrdSupProduct->cfg_pct_category_supplier_discount;
        $price->ind_lbl_category_supplier_discount = $priceOrdSupProduct->ind_lbl_category_supplier_discount;

        // in case of pricing profile
        if ($price->cfg_pricing_type == self::PRICING_FEE_WITH_PROFILE) {

            // get product pricing
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('pospp.*')
                    ->from('#__sdi_pricing_order_supplier_product_profile pospp')
                    ->where('pospp.pricing_order_supplier_product_id=' . $priceOrdSupProduct->id);
            $db->setQuery($query);
            $pricingProfile = $db->loadObject();

            // get base parameters
            $price->cfg_profile_id = $pricingProfile->pricing_profile_id;
            $price->cfg_profile_guid = null;
            $price->cfg_profile_name = $pricingProfile->pricing_profile_name;
            $price->cfg_fixed_fee_te = $pricingProfile->cfg_fixed_fee_te;
            $price->cfg_apply_vat = $pricingProfile->cfg_apply_vat;
            $price->cfg_surface_rate = $pricingProfile->cfg_surface_rate;
            $price->cfg_min_fee = $pricingProfile->cfg_min_fee;
            $price->cfg_max_fee = $pricingProfile->cfg_max_fee;


            // rebate based on category and profile
            $price->cfg_pct_category_profile_discount = $pricingProfile->cfg_pct_category_profile_discount;
            $price->ind_lbl_category_profile_discount = $pricingProfile->ind_lbl_category_profile_discount;
        }

        return $price;
    }

    /**
     * basketPriceCalculation - calculate pricing for a basket
     * 
     * @param type $basket
     * @return void
     * @since 4.3.0
     */
    public static function basketPriceCalculation(&$basket) {

        // init prices object
        $prices = new stdClass();
        $prices->isActivated = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');

        // test if pricing is activated
        if ($prices->isActivated) {
            // set the invoiced user (based on current user and third-party)
            $prices->debtor = new stdClass();
            if (isset($basket->thirdparty) && $basket->thirdparty > 0) {
                $prices->debtor->id = $basket->thirdparty;
            } else {
                $prices->debtor->id = $basket->sdiUser->role[self::ROLE_MEMBER][0]->id;
                $prices->debtor->name = $basket->sdiUser->role[self::ROLE_MEMBER][0]->name;
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('c.id')
                    ->from('#__sdi_organism_category oc')
                    ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                    ->where('oc.organism_id=' . (int) $prices->debtor->id);
            $db->setQuery($query);
            $prices->debtor->categories = $db->loadColumn();

            // get easysdishop config
            $prices->cfg_vat = (float) JComponentHelper::getParams('com_easysdi_shop')->get('vat', 0);
            $prices->cfg_currency = JComponentHelper::getParams('com_easysdi_shop')->get('currency', 'CHF');
            $prices->cfg_rounding = (float) JComponentHelper::getParams('com_easysdi_shop')->get('rounding', 0.05);
            $prices->cfg_overall_default_fee_te = (float) JComponentHelper::getParams('com_easysdi_shop')->get('overall_default_fee', 0);
            $prices->cfg_fee_apply_vat = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('overall_fee_apply_vat', true);
            $prices->cfg_free_data_fee = (bool) JComponentHelper::getParams('com_easysdi_shop')->get('free_data_fee', false);

            // get the surface ordered - default to 0
            $prices->surface = isset($basket->extent) && isset($basket->extent->surface) ? $basket->extent->surface / 1000000 : 0;

            // calculate prices by supplier
            $prices->suppliers = array();
            $prices->cal_total_amount_ti = 0;
            $prices->hasFeeWithoutPricingProfileProduct = false;
            foreach ($basket->extractions as $supplier_id => $supplier) {
                $prices->suppliers[$supplier_id] = self::basketPriceCalculationBySupplier($supplier, $prices);
                if ($prices->suppliers[$supplier_id]->hasFeeWithoutPricingProfileProduct) {
                    $prices->hasFeeWithoutPricingProfileProduct = true;
                } else {
                    $prices->cal_total_amount_ti += $prices->suppliers[$supplier_id]->cal_total_amount_ti;
                }
            }

            // set the platform tax
            if (!$prices->hasFeeWithoutPricingProfileProduct && $prices->cal_total_amount_ti == 0 && !$prices->cfg_free_data_fee) {
                $prices->cal_fee_te = 0;
                $prices->cal_fee_ti = 0;
            } else {
                $prices->cal_fee_te = $prices->cfg_overall_default_fee_te;
                $prices->cal_fee_ti = ($prices->cfg_fee_apply_vat) ? $prices->cfg_overall_default_fee_te * (1 + $prices->cfg_vat / 100) : $prices->cfg_overall_default_fee_te ;

                //Current user categories are used to defined platform fee.
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('c.id')
                        ->from('#__sdi_organism_category oc')
                        ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                        ->where('oc.organism_id=' . (int) $basket->sdiUser->role[self::ROLE_MEMBER][0]->id);
                $db->setQuery($query);
                $currentcategories = $db->loadColumn();

                if (count($currentcategories)) {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true)
                            ->select('c.overall_fee, c.name')
                            ->from('#__sdi_category c')
                            ->where('c.overall_fee IS NOT NULL')
                            ->where('c.id IN (' . implode(',', $currentcategories) . ')')
                            ->order('overall_fee');
                    $db->setQuery($query, 0, 1);
                    $category = $db->loadObject();

                    if ($category !== null) {
                        $prices->cal_fee_te = $category->overall_fee;
                        $prices->cal_fee_ti = ($prices->cfg_fee_apply_vat) ? $category->overall_fee * (1 + $prices->cfg_vat / 100) : $category->overall_fee;
                        $prices->ind_lbl_category_order_fee = $category->name;
                    }
                }
            }

            $prices->cal_fee_ti = self::rounding($prices->cal_fee_ti, $prices->cfg_rounding);

            // total amount for the platform
            if ($prices->hasFeeWithoutPricingProfileProduct) {
                $prices->cal_total_amount_ti = null;
            } else {
                $prices->cal_total_amount_ti += $prices->cal_fee_ti;
            }
        }

        $basket->pricing = $prices;
    }

    /**
     * basketPriceCalculationBySupplier - calculate pricing by supplier
     * 
     * @param type $supplier
     * @param type $prices
     * @return \stdClass
     * @since 4.3.0
     */
    private static function basketPriceCalculationBySupplier($supplier, $prices) {

        // init provider sub-object
        $provider = new stdClass();
        $provider->id = (int) $supplier->id;

        // get organism pricing params
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('o.name, o.internal_free, o.fixed_fee_te, o.data_free_fixed_fee, o.fixed_fee_apply_vat')
                ->from('#__sdi_organism o')
                ->where('o.id=' . $provider->id);
        $db->setQuery($query);
        $organism = $db->loadObject();

        $provider->name = $organism->name;
        $provider->cfg_internal_free = (bool) $organism->internal_free;
        $provider->cfg_fixed_fee_te = $organism->fixed_fee_te;
        $provider->cfg_data_free_fixed_fee = (bool) $organism->data_free_fixed_fee;
        $provider->cfg_fixed_fee_apply_vat = (bool) $organism->fixed_fee_apply_vat;

        // calculate supplier rebate
        $internalFreeOrder = false;
        $prices->supplierRebate = new stdClass();
        if ($provider->cfg_internal_free == true && $prices->debtor->id == $provider->id) {
            $internalFreeOrder = true;
            $prices->supplierRebate->pct = 100;
            $prices->supplierRebate->name = $organism->name;
        } elseif (count($prices->debtor->categories)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('ocpr.rebate, c.name')
                    ->from('#__sdi_organism_category_pricing_rebate ocpr')
                    ->join('LEFT', '#__sdi_category c ON c.id=ocpr.category_id')
                    ->where('ocpr.organism_id=' . $provider->id . ' AND ocpr.category_id IN (' . implode(',', $prices->debtor->categories) . ')')
                    ->order('rebate DESC')
            ;
            $db->setQuery($query, 0, 1);
            $supplierRebate = $db->loadAssoc();
            $prices->supplierRebate->pct = $supplierRebate['rebate'];
            $prices->supplierRebate->name = $supplierRebate['name'];
        } else {
            $prices->supplierRebate->pct = 0;
            $prices->supplierRebate->name = '';
        }

        // calculate price for each product and increment the provider sub-total
        $provider->products = array();
        $provider->cal_total_amount_ti = 0;
        $provider->cal_total_rebate_ti = 0;
        $provider->hasFeeWithoutPricingProfileProduct = false;
        foreach ($supplier->items as $product) {
            $provider->products[$product->id] = self::basketPriceCalculationByProduct($product, $prices, $internalFreeOrder);
            if ($product->pricing == self::PRICING_FEE_WITHOUT_PROFILE && !$internalFreeOrder)
                $provider->hasFeeWithoutPricingProfileProduct = true;
            elseif (isset($provider->products[$product->id]->cal_total_amount_ti)) {
                $provider->cal_total_amount_ti += $provider->products[$product->id]->cal_total_amount_ti;
                $provider->cal_total_rebate_ti += $provider->products[$product->id]->cal_total_rebate_ti;
            }
        }

        // set the provider tax        
        $provider->cfg_fixed_fee_ti = ($provider->cfg_fixed_fee_apply_vat) ? $provider->cfg_fixed_fee_te * (1 + $prices->cfg_vat / 100) : $provider->cfg_fixed_fee_te;
        $provider->cal_fee_ti = ($provider->cal_total_amount_ti > 0 || $provider->cfg_data_free_fixed_fee) ? self::rounding($provider->cfg_fixed_fee_ti, $prices->cfg_rounding) : 0;

        // total amount for this provider
        if ($provider->hasFeeWithoutPricingProfileProduct) {
            unset($provider->cal_total_amount_ti);
            unset($provider->cal_total_rebate_ti);
        } else {
            $provider->cal_total_amount_ti += $provider->cal_fee_ti;
        }

        return $provider;
    }

    /**
     * basketPriceCalculationByProduct - calculate pricing by product
     * 
     * @param type $product
     * @param type $prices
     * @return \stdClass
     * @since 4.3.0
     */
    private static function basketPriceCalculationByProduct($product, $prices, $internalFreeOrder) {

        // init product sub-object
        $price = new stdClass();

        $price->cfg_pricing_type = $product->pricing;

        if ($price->cfg_pricing_type == self::PRICING_FEE_WITHOUT_PROFILE && $internalFreeOrder) {
            $price->cal_total_amount_ti = 0;
            $price->cal_total_rebate_ti = 0;
        }

        if ($price->cfg_pricing_type != self::PRICING_FEE_WITH_PROFILE) {
            return $price;
        }

        // get base parameters to calculate product price
        $pricingProfile = new sdiPricingProfile($product->pricing_profile);
        $price->cfg_profile_id = $pricingProfile->id;
        $price->cfg_profile_guid = $pricingProfile->guid;
        $price->cfg_profile_name = $pricingProfile->name;
        $price->cfg_fixed_fee_te = $pricingProfile->fixed_fee;
        $price->cfg_surface_rate = $pricingProfile->surface_rate;
        $price->cfg_min_fee = $pricingProfile->min_fee;
        $price->cfg_max_fee = $pricingProfile->max_fee;
        $price->cfg_apply_vat = $pricingProfile->apply_vat;

        // calculate product price
        $price->cal_amount_data_te = self::rounding($price->cfg_fixed_fee_te + ($price->cfg_surface_rate * $prices->surface));

        // limit price according to min and max price
        if ($price->cfg_max_fee > 0 && $price->cal_amount_data_te > $price->cfg_max_fee) {
            $price->cal_amount_data_te = $price->cfg_max_fee;
        } elseif ($price->cfg_min_fee > 0 && $price->cal_amount_data_te < $price->cfg_min_fee) {
            $price->cal_amount_data_te = $price->cfg_min_fee;
        }

        // rebate based on category and profile
        $price->cfg_pct_category_profile_discount = 0;
        $price->ind_lbl_category_profile_discount = '';

        if (count($prices->debtor->categories)) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('ppcpr.category_id, c.name')
                    ->from('#__sdi_pricing_profile_category_pricing_rebate ppcpr')
                    ->join('LEFT', '#__sdi_category c ON c.id=ppcpr.category_id')
                    ->where('ppcpr.pricing_profile_id=' . (int) $pricingProfile->id)
                    ->where('ppcpr.category_id IN (' . implode(',', $prices->debtor->categories) . ')');
            $db->setQuery($query);
            $category_free = $db->loadAssoc();

            if ($category_free !== null) {
                $price->cfg_pct_category_profile_discount = 100;
                $price->ind_lbl_category_profile_discount = $category_free['name'];
            }
        }

        // rebate based on category and supplier
        $price->cfg_pct_category_supplier_discount = $prices->supplierRebate->pct;
        $price->ind_lbl_category_supplier_discount = $prices->supplierRebate->name;

        // final price TE
        $price->cal_total_amount_te = self::rounding($price->cal_amount_data_te * (1 - $price->cfg_pct_category_profile_discount / 100) * (1 - $price->cfg_pct_category_supplier_discount / 100));
        $cal_total_rebate_te = self::rounding($price->cal_amount_data_te - $price->cal_total_amount_te);

        // final price TI
        $price->cal_total_amount_ti = ($price->cfg_apply_vat) ? self::rounding($price->cal_total_amount_te * (1 + $prices->cfg_vat / 100), $prices->cfg_rounding) : self::rounding($price->cal_total_amount_te , $prices->cfg_rounding) ;
        $price->cal_total_rebate_ti = ($price->cfg_apply_vat) ? self::rounding($cal_total_rebate_te * (1 + $prices->cfg_vat / 100), $prices->cfg_rounding) : self::rounding($cal_total_rebate_te , $prices->cfg_rounding);

        return $price;
    }

    /**
     * priceFormatter - format price according to the shop configuration
     * a js implementation is located in basket.js
     * 
     * @param mixed $price - float price or string
     * @param boolean $displayCurrency
     * @return string
     * @since 4.3.0
     */
    public static function priceFormatter($price, $displayCurrency = true) {
        $c = $displayCurrency ? ' ' . JComponentHelper::getParams('com_easysdi_shop')->get('currency', 'CHF') : '';

        if (isset($price) && $price != null && $price != 0) {
            $price = number_format(
                    $price, JComponentHelper::getParams('com_easysdi_shop')->get('digit_after_decimal', 2), JComponentHelper::getParams('com_easysdi_shop')->get('decimal_symbol', '.'), JComponentHelper::getParams('com_easysdi_shop')->get('digit_grouping_symbol', "'")
            );
            return $price . $c;
        } elseif (isset($price) && $price == 0) {
            return '0' . $c;
        } else {
            return '-';
        }
    }

    /**
     * updatePricing - update the pricing schema branch
     * 
     * @param stdClass $posp
     * @param stdClass $pos
     * @param stdClass $po
     * @param JDatabase $db an optionnal reference to database (for transactinal use)
     * 
     * @return void
     * @since 4.3.0
     * 
     * @return boolean|string Returns Boolean 'true' if everything is correctly saved, otherwise returns the error string.
     * Ensure to compare the return to === true for correct completion of the process
     */
    public static function updatePricing($posp, $pos, $po, &$db = null) {
        if (!isset($db)) {
            $db = JFactory::getDbo();
        }

        if (!$posp->save(array())) {
            return 'Cannot update pricing order supplier product';
        }

        $rpos = self::updatePricingSupplierSummary($pos, $po, $db);
        if ($rpos !== true) {
            return $rpos;
        }


        //everything went well
        return true;
    }

    /**
     * updatePricingSupplierSummary - update the pricing supplier and order branch if needed
     * @param stdClass $pos
     * @param stdClass $po
     * @param stdClass $db
     * @return boolean|string Returns Boolean 'true' if everything is correctly saved, otherwise returns the error string.
     * Ensure to compare the return to === true for correct completion of the process
     */
    private static function updatePricingSupplierSummary($pos, $po, &$db) {
        
        // count the products rejected by the current provider
        $db->setQuery($db->getQuery(true)
                        ->select('od.productstate_id')
                        ->from('#__sdi_pricing_order_supplier_product posp')
                        ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id = posp.product_id')
                        ->where('od.order_id='. (int) $po->order_id)
                        ->where('posp.pricing_order_supplier_id=' . (int) $pos->id)
                        ->group('od.productstate_id'));
        $productsRejected = $db->loadColumn();
        if(count($productsRejected) == 1 && $productsRejected[0] == Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER)
        {
            //All supplier products were rejected : cancel supplier fee
            $pos->cal_fee_ti = 0;
            $pos->cal_total_amount_ti = 0;
            $pos->cal_total_rebate_ti = 0;
            if (!$pos->save(array())) {
                return 'Cannot update pricing order supplier';
            }

            $rpo = self::updatePricingOrderSummary($po, $db);
            if ($rpo !== true) {
                return $rpo;
            }
            
            return true;
        }
                
        // count the products without price define for the current provider
        $db->setQuery($db->getQuery(true)
                        ->select('COUNT(1)')
                        ->from('#__sdi_pricing_order_supplier_product posp')
                        ->where('posp.pricing_order_supplier_id=' . (int) $pos->id)
                        ->where('pricing_id <> 1')
                        ->where('posp.cal_total_amount_ti IS NULL'));
        $productsWithoutPrice = $db->loadResult();
        //if all products of this supplier have a price, we can update the supplier pricing branch
        if ($productsWithoutPrice == 0) {
            $db->setQuery($db->getQuery(true)
                            ->select('SUM(posp.cal_total_amount_ti) orderSupplierProductsTotal, SUM(posp.cal_total_rebate_ti) orderSupplierRebatesTotal')
                            ->from('#__sdi_pricing_order_supplier_product posp')
                            ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                            ->where('posp.pricing_order_supplier_id=' . (int) $pos->id));
            $posData = $db->loadObject();

            //if total is 0 and fixed fee are not applied for 'all free' order
            //overwrite the cal_fee_ti with 0
            if ($pos->cfg_data_free_fixed_fee == 0 && $posData->orderSupplierProductsTotal == 0) {
                $pos->cal_fee_ti = 0;
            }

            //update rebates ti total
            $pos->cal_total_rebate_ti = $posData->orderSupplierRebatesTotal;

            //update total ti
            $pos->cal_total_amount_ti = ($posData->orderSupplierProductsTotal + $pos->cal_fee_ti);

            if (!$pos->save(array())) {
                return 'Cannot update pricing order supplier';
            }

            $rpo = self::updatePricingOrderSummary($po, $db);
            if ($rpo !== true) {
                return $rpo;
            }
        }
        return true;
    }

    /**
     * updatePricingOrderSummary - update the pricing order branch if needed
     * @param type $po
     * @param type $db
     * @return boolean|string Returns Boolean 'true' if everything is correctly saved, otherwise returns the error string.
     * Ensure to compare the return to === true for correct completion of the process
     */
    private static function updatePricingOrderSummary($po, &$db) {
        // count the products rejected by the current provider
        $db->setQuery($db->getQuery(true)
                        ->select('od.productstate_id')
                        ->from('#__sdi_pricing_order_supplier pos')
                        ->innerJoin('#__sdi_pricing_order_supplier_product posp ON posp.pricing_order_supplier_id = pos.id')
                        ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id = posp.product_id')
                        ->where('od.order_id='. (int) $po->order_id)
                        ->group('od.productstate_id'));
        $productsRejected = $db->loadColumn();
        if(count($productsRejected) == 1 && $productsRejected[0] == Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER)
        {
            //All products were rejected : cancel plateform fee
            $po->cal_fee_ti = 0;
            $po->cal_total_amount_ti = 0;
            
            if (!$po->save(array())) {
                return 'Cannot update pricing order';
            }
            
            return true;
        }
        
        //count all the suppliers pricing of the order without price
        $db->setQuery($db->getQuery(true)
                        ->select('COUNT(1)')
                        ->from('#__sdi_pricing_order_supplier pos')
                        ->where('pos.pricing_order_id=' . (int) $po->id)
                        ->where('pos.cal_total_amount_ti IS NULL'));
        $suppliersWithoutPrice = $db->loadResult();

        //if all suppliers of this order have a price, we can update the order pricing branch
        if ($suppliersWithoutPrice == 0) {
            $db->setQuery($db->getQuery(true)
                            ->select('SUM(pos.cal_total_amount_ti) orderSuppliersTotal')
                            ->from('#__sdi_pricing_order po')
                            ->innerJoin('#__sdi_pricing_order_supplier pos ON po.id=pos.pricing_order_id')
                            ->where('po.id=' . (int) $po->id));
            $orderSuppliersTotal = $db->loadResult();

            //if total is 0 and platform fee are not applied for 'all free' order
            //overwrite the cal_fee_ti with 0
            if ($po->cfg_free_data_fee == 0 && $orderSuppliersTotal == 0) {
                $po->cal_fee_ti = 0;
            }

            $po->cal_total_amount_ti = ($orderSuppliersTotal + $po->cal_fee_ti);

            if (!$po->save(array())) {
                return 'Cannot update pricing order';
            }
        }
        return true;
    }

    /**
     * changeOrderState - Dynamically changes the statue of the order.
     * 
     * @param integer $orderId Id of the order.
     * 
     * @return void
     * @since 4.3.0
     */
    public static function changeOrderState($orderId) {
        $orderstate = self::getNewOrderState($orderId);
        $db = JFactory::getDbo();

        if (isset($orderstate)) {
            $query = $db->getQuery(true)
                            ->update('#__sdi_order')->set('orderstate_id=' . $orderstate);
            if ($orderstate == Easysdi_shopHelper::ORDERSTATE_FINISH) {
                $query->set('completed=' . $query->quote(date("Y-m-d H:i:s")));
            }
            $query->where('id=' . (int) $orderId);

            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * getNewOrderState - Calculates the new order state based on products/diffusion states
     * 
     * @param integer $orderId Id of the order.
     * 
     * @return void
     * @since 4.3.0
     */
    public static function getNewOrderState($orderId) {

        $db = JFactory::getDbo();
        $db->setQuery($db->getQuery(true)
                        ->select('id')->from('#__sdi_order_diffusion')->where('order_id=' . (int) $orderId));
        $total = $db->getNumRows($db->execute());

        $db->setQuery($db->getQuery(true)
                        ->select('id')->from('#__sdi_order_diffusion')->where('order_id=' . (int) $orderId)->where('productstate_id=' . Easysdi_shopHelper::PRODUCTSTATE_AWAIT));
        $await = $db->getNumRows($db->execute());

        $db->setQuery($db->getQuery(true)
                        ->select('id')->from('#__sdi_order_diffusion')->where('order_id=' . (int) $orderId)->where('productstate_id=' . Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE));
        $available = $db->getNumRows($db->execute());

        $db->setQuery($db->getQuery(true)
                        ->select('id')->from('#__sdi_order_diffusion')->where('order_id=' . (int) $orderId)->where('productstate_id=' . Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER));
        $rejected = $db->getNumRows($db->execute());

        return self::chooseOrderState($total, $await, $available, $rejected);
    }

    /**
     * chooseOrderState - return the correct orderState according to the given params
     * 
     * @param integer $total
     * @param integer $await
     * @param integer $available
     * @param integer $rejected
     * 
     * @return integer|null
     * @since 4.3.0
     */
    private static function chooseOrderState($total, $await, $available, $rejected) {
        if ($total == $rejected) {
            return Easysdi_shopHelper::ORDERSTATE_REJECTED_SUPPLIER;
        }

        if ($available == $total || $available + $rejected == $total) {
            return Easysdi_shopHelper::ORDERSTATE_FINISH;
        }

        if ($available > 0 || $rejected > 0) {
            return Easysdi_shopHelper::ORDERSTATE_PROGRESS;
        }

        if ($await > 0) {
            return Easysdi_shopHelper::ORDERSTATE_AWAIT;
        }

        return null;
    }

    /* -------------------- * 
     * NOTIFICATION METHODS * 
     * -------------------- */

    /**
     * notifyCustomer - notify the customer of its order
     * 
     * @param string $orderId
     * @return void
     * @since 4.3.0
     */
    public static function notifyCustomer($orderId) {
        $user = sdiFactory::getSdiUser();

        //do not send notifications if disabled by user:
        if (!$user->user->notificationrequesttreatment) {
            return;
        }

        $basket = new sdiBasket();
        $basket->loadOrder((int) $orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket);

        $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_NEW_ORDER_SUBJECT');
        $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_NEW_ORDER_BODY');


        if (!$user->sendMail($stringParser->getReplacedStringForClient($subject), $stringParser->getReplacedStringForClient($bodytext))) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }

    /**
     * notifyCustomerOnOrderUpdate - notify the customer (only if needed) about the progress of the order
     * 
     * @param integer $orderId the id of the order
     * @param boolean $silentFail optionnal, if an any error, die silently
     * @return void
     * @since 4.3.0
     */
    public static function notifyCustomerOnOrderUpdate($orderId, $silentFail = false) {

        $basket = new sdiBasket();
        $basket->loadOrder($orderId);

        //do not send notifications if disabled by user:
        if (!$basket->sdiUser->user->notificationrequesttreatment) {
            return;
        }

        //do not sent multiple notifications on the same order
        if ($basket->orderstate_id == Easysdi_shopHelper::ORDERSTATE_PROGRESS && $basket->usernotified) {
            return;
        }

        $stringParser = new Easysdi_shopBasketStringParser($basket);

        $errors = false;

        if (isset($basket->id)) {

            switch ($basket->orderstate_id) {
                case Easysdi_shopHelper::ORDERSTATE_FINISH:
                    $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_FINISH_SUBJECT');
                    $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_FINISH_BODY');
                    break;
                case Easysdi_shopHelper::ORDERSTATE_PROGRESS:
                    $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_FIRST_PRODUCT_SUBJECT');
                    $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_FIRST_PRODUCT_BODY');
                    break;
                case Easysdi_shopHelper::ORDERSTATE_REJECTED_SUPPLIER:
                    $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_REJECTED_SUPPLIER_SUBJECT');
                    $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_REJECTED_SUPPLIER_BODY');
                    break;
                case Easysdi_shopHelper::ORDERSTATE_REJECTED:
                    $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_REJECTED_THIRD_PARTY_SUBJECT');
                    $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_CLIENT_ORDER_REJECTED_THIRD_PARTY_BODY');
                    break;
            }

            $subject = $stringParser->getReplacedStringForClient($subject);
            $bodytext = $stringParser->getReplacedStringForClient($bodytext);

            if (!$basket->sdiUser->sendMail($subject, $bodytext)) {
                $errors = true;
            }

            self::setUserNotified($basket->id);
        } else {
            $errors = true;
        }

        if ($errors && !$silentFail) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }

    /**
     * set the usernotified flog to true on an order to avoid the user beein notified on every product
     * @param integer $order_id
     */
    private static function setUserNotified($order_id) {
        $db = JFactory::getDbo();
        if (is_numeric($order_id)) {
            $query = $db->getQuery(true)
                    ->update('#__sdi_order')
                    ->set('usernotified = 1')
                    ->where('id=' . (int) $order_id);
            $db->setQuery($query);
            $db->execute();
        }
    }

    /**
     * notifyExtractionResponsibleAndNotifiedUsers - notify the extraction responsible 
     * and defined notified users for each products of the order
     * 
     * @param integer $orderId
     * @return void
     * @since 4.4.0
     */
    public static function notifyExtractionResponsibleAndNotifiedUsers($orderId) {
        $basket = new sdiBasket();
        $basket->loadOrder((int) $orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket, $stringParser);
        self::notifyNotifiedUsers($basket, $stringParser);
        self::notifyExtractionResponsible($basket, $stringParser);
    }

    /**
     * notifyNotifiedUsers - notify the defined notified users for a diffusion
     * 
     * @param sdiBasket $basket
     * @param Easysdi_shopBasketStringParser $stringParser
     * @since 4.4.0
     */
    private static function notifyNotifiedUsers($basket, $stringParser) {
        //notified users
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->qn('dnu.user_id', 'user_id'))
                ->select($db->qn('d.id', 'diffusion_id'))
                ->from($db->qn('#__sdi_order', 'o'))
                ->innerJoin($db->qn('#__sdi_order_diffusion', 'od') . ' ON od.order_id = o.id')
                ->innerJoin($db->qn('#__sdi_diffusion', 'd') . ' ON od.diffusion_id = d.id')
                ->innerJoin($db->qn('#__sdi_diffusion_notifieduser', 'dnu') . ' ON dnu.diffusion_id = d.id')
                ->where('od.productstate_id = ' . self::PRODUCTSTATE_SENT)
                ->where('o.id = ' . (int) $basket->id);
        $db->setQuery($query);
        $notifiedUsers = $db->loadRowList();

        //group diffusions by users
        $grouppedNotifiedUsers = array();
        foreach ($notifiedUsers as $nf) {
            $grouppedNotifiedUsers[$nf[0]][] = $nf[1];
        }

        foreach ($grouppedNotifiedUsers as $nUserId => $nUserDiffusions) {
            $user = new sdiUser($nUserId);
            $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_NOTIFIEDUSER_NEW_ORDER_SUBJECT');
            $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_NOTIFIEDUSER_NEW_ORDER_BODY');

            if (!$user->sendMail($stringParser->getReplacedStringForSupplier($subject, $nUserDiffusions), $stringParser->getReplacedStringForSupplier($bodytext, $nUserDiffusions))) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            }
        }
    }

    /**
     * notifyExtractionResponsible - notify the extraction responsible
     * 
     * @param sdiBasket $basket
     * @param Easysdi_shopBasketStringParser $stringParser
     * @since 4.4.0
     */
    private static function notifyExtractionResponsible($basket, $stringParser) {
        //extraction responsible users
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->qn('uro.user_id', 'user_id'))
                ->select($db->qn('d.id', 'diffusion_id'))
                ->from($db->qn('#__sdi_order', 'o'))
                ->innerJoin($db->qn('#__sdi_order_diffusion', 'od') . ' ON od.order_id = o.id')
                ->innerJoin($db->qn('#__sdi_diffusion', 'd') . ' ON od.diffusion_id = d.id')
                ->innerJoin($db->qn('#__sdi_version', 'v') . ' ON d.version_id = v.id')
                ->innerJoin($db->qn('#__sdi_user_role_resource', 'uro') . ' ON v.resource_id = uro.resource_id AND uro.role_id = ' . self::ROLE_EXTRACTIONRESPONSIBLE)
                ->where('o.id = ' . (int) $basket->id)
                ->where('od.productstate_id = ' . self::PRODUCTSTATE_SENT)
                ->where('d.productmining_id = ' . self::PRODUCTMININGMANUAL);
        $db->setQuery($query);
        $extractionResponsibles = $db->loadRowList();

        //group diffusions by users
        $grouppedExtractionResponsibles = array();
        foreach ($extractionResponsibles as $nf) {
            $grouppedExtractionResponsibles[$nf[0]][] = $nf[1];
        }

        foreach ($grouppedExtractionResponsibles as $erUserId => $erUserDiffusions) {
            $user = new sdiUser($erUserId);
            $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_EXTRACTION_RESPONSIBLE_NEW_ORDER_SUBJECT');
            $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_EXTRACTION_RESPONSIBLE_NEW_ORDER_BODY');

            if (!$user->sendMail($stringParser->getReplacedStringForSupplier($subject, $erUserDiffusions), $stringParser->getReplacedStringForSupplier($bodytext, $erUserDiffusions))) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            }
        }
    }

    /**
     * notifyTPValidationManager - notify the thirdparty validation manager after an order
     * 
     * @param integer $orderId
     * @param integer $thirdpartyId
     * @since 4.4.0
     */
    public static function notifyTPValidationManager($orderId, $thirdpartyId) {
        $db = JFactory::getDbo();

        $basket = new sdiBasket();
        $basket->loadOrder($orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket);

        //Select orderdiffusion_id
        $query = $db->getQuery(true);
        $query->select('user_id')
                ->from('#__sdi_user_role_organism')
                ->where('role_id=' . Easysdi_shopHelper::ROLE_VALIDATIONMANAGER . ' AND organism_id=' . (int) $thirdpartyId);
        $db->setQuery($query);
        $users_ids = $db->loadColumn();

        foreach ($users_ids as $user_id) {
            $user = sdiFactory::getSdiUser($user_id);
            $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_NEW_ORDER_SUBJECT');
            $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_NEW_ORDER_BODY');

            if (!$user->sendMail($stringParser->getReplacedStringForValidator($subject, $user_id), $stringParser->getReplacedStringForValidator($bodytext, $user_id))) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            }
        }
    }

    /**
     * notifyValidationManager - notify the validation managers that order has been validated
     * 
     * @param integer $order_id
     * @return void
     * @since 4.3.2
     */
    public static function notifyAfterValidationManager($orderId) {
        $db = JFactory::getDbo();

        $basket = new sdiBasket();
        $basket->loadOrder($orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket);

        //Select orderdiffusion_id
        $query = $db->getQuery(true);
        $query->select('user_id')
                ->from('#__sdi_user_role_organism')
                ->where('role_id=' . Easysdi_shopHelper::ROLE_VALIDATIONMANAGER . ' AND organism_id=' . (int) $basket->thirdparty);
        $db->setQuery($query);
        $users_ids = $db->loadColumn();

        //Select message regarding order state_id
        switch ($basket->orderstate_id):
            case Easysdi_shopHelper::ORDERSTATE_SENT :
                $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_VALIDATED_SUBJECT');
                $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_VALIDATED_BODY');
                break;
            case Easysdi_shopHelper::ORDERSTATE_REJECTED :
                $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_REJECTED_SUBJECT');
                $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_VALIDATIONMANAGER_REJECTED_BODY');
                break;
        endswitch;

        //Send email
        foreach ($users_ids as $user_id) {
            $user = sdiFactory::getSdiUser($user_id);

            $subject = $stringParser->getReplacedStringForValidator($subject, $user_id);
            $bodytext = $stringParser->getReplacedStringForValidator($bodytext, $user_id);

            if (!$user->sendMail($subject, $bodytext)) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            }
        }
    }
    
    /**
     * notifyCustomerOTP - notify the customer of the OTP generated by EasySDI
     * 
     * @param integer $order_id
     * @param string $OTP
     * @return void
     * @since 4.4.2
     */
    public static function notifyCustomerOTP($orderId,$otp) {
        $db = JFactory::getDbo();

        $basket = new sdiBasket();
        $basket->loadOrder($orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket);
        
        $subject = $stringParser->getReplacedStringForOTP(JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTP_SUBJECT'));
        $bodytext = $stringParser->getReplacedStringForOTP(JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTP_BODY'),$otp);
        
        if (!$basket->sdiUser->sendMail($subject, $bodytext)) {
                $errors = true;
        }

        if ($errors && !$silentFail) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }
    
    /**
     * notifyCustomerOTP - notify the customer of the OTP generated by EasySDI
     * 
     * @param integer $order_id
     * @param string $OTP
     * @return void
     * @since 4.4.2
     */
    public static function notifyCustomerOTPUnblocked($orderId) {
        $db = JFactory::getDbo();

        $basket = new sdiBasket();
        $basket->loadOrder($orderId);
        $stringParser = new Easysdi_shopBasketStringParser($basket);
        
        $subject = $stringParser->getReplacedStringForOTP(JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTPUNBLOCKED_SUBJECT'));
        $bodytext = $stringParser->getReplacedStringForClient(JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTPUNBLOCKED_BODY'));
        
        if (!$basket->sdiUser->sendMail($subject, $bodytext)) {
                $errors = true;
        }

        if ($errors && !$silentFail) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }
    
    /**
     * notifyExtractionResponsible - notify the extraction responsible
     * 
     * @param sdiBasket $basket
     * @param Easysdi_shopBasketStringParser $stringParser
     * @since 4.4.0
     */
    public static function notifyExtractionResponsibleOTPChanceReached($orderdiffusion) {
        //extraction responsible users
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->qn('uro.user_id', 'user_id'))
                ->select($db->qn('d.id', 'diffusion_id'))
                ->from($db->qn('#__sdi_order', 'o'))
                ->innerJoin($db->qn('#__sdi_order_diffusion', 'od') . ' ON od.order_id = o.id')
                ->innerJoin($db->qn('#__sdi_diffusion', 'd') . ' ON od.diffusion_id = d.id')
                ->innerJoin($db->qn('#__sdi_version', 'v') . ' ON d.version_id = v.id')
                ->innerJoin($db->qn('#__sdi_user_role_resource', 'uro') . ' ON v.resource_id = uro.resource_id AND uro.role_id = ' . self::ROLE_EXTRACTIONRESPONSIBLE)
                ->where('o.id = ' . (int) $orderdiffusion->order_id)
                ->where('od.diffusion_id = ' . (int) $orderdiffusion->diffusion_id);
        $db->setQuery($query);
        $extractionResponsibles = $db->loadRowList();

        //group diffusions by users
        $grouppedExtractionResponsibles = array();
        foreach ($extractionResponsibles as $nf) {
            $grouppedExtractionResponsibles[$nf[0]][] = $nf[1];
        }
        
        $basket = new sdiBasket();
        $basket->loadOrder($orderdiffusion->order_id);
        $stringParser = new Easysdi_shopBasketStringParser($basket);

        foreach ($grouppedExtractionResponsibles as $erUserId => $erUserDiffusions) {
            $user = new sdiUser($erUserId);
            $subject = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTPMAXATTEMPT_SUBJECT');
            $bodytext = JText::_('COM_EASYSDI_SHOP_NOTIFICATION_OTPMAXATTEMPT_BODY');

            if (!$user->sendMail($subject, $stringParser->getReplacedStringForSupplier($bodytext, $erUserDiffusions))) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
            }
        }
    }

    /**
     * Add javascript config variables in DOM for SHOP MAP
     */
    public static function addMapShopConfigToDoc() {
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('var mapFillColor = "' . JComponentHelper::getParams('com_easysdi_shop')->get('map_fill_color', '#EE9900') . '",
                mapFillOpacity = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_fill_opacity', 0.4) . ',
                mapStrokeColor = "' . JComponentHelper::getParams('com_easysdi_shop')->get('map_stroke_color', '#EE9900') . '",
                mapStrokeOpacity = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_stroke_opacity', 1.0) . ',
                mapStrokeWidth = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_stroke_width', 2) . ',
                mapPointStrokeWidth = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_point_stroke_width', 2) . ',
                mapPointRadius = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_point_radius', 5) . ',
                mapRotateIconURL = "' . JComponentHelper::getParams('com_easysdi_shop')->get('map_rotate_icon_url', Juri::base(true) . '/components/com_easysdi_shop/views/basket/tmpl/rotate_20.png') . '",
                mapMinSurfaceRectangle = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_min_surface_rectangle', 0) . ',
                mapMinSurfaceRectangleBorder = ' . JComponentHelper::getParams('com_easysdi_shop')->get('map_min_surface_rectangle_border', 100) . ';');
    }

    /**
     * Return the modal's HTML to confirm the addition of a product in basket
     * @return String HTML element of basket modal
     */
    public static function getAddToBasketModal() {
        return '<div id="modal-dialog-atb" class="modal hide fade" style="z-index: 1000000" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 id="modalLabel">' . JText::_("COM_EASYSDI_SHOP_ORDER_DIALOG_HEADER") . '</h3>
                    </div>
                    <div class="modal-body">
                        <p><div id="modal-dialog-body-text">' . JText::_("COM_EASYSDI_SHOP_ORDER_CONFIRM_LOAD_IN_BASKET") . '</div></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_EASYSDI_SHOP_ORDER_MODAL_BTN_ABORT") . '</button>
                        <button onClick="confirmAdd();" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_EASYSDI_SHOP_ORDER_MODAL_BTN_CONFIRM") . '</button>
                    </div>
                </div>';
    }
    
     /**
     * Return the modal's HTML to ask for authentication in order to download OTP file
     * @return String HTML element of OTP modal
     */
    public static function downloadOTPProductModal() {
        return '<div id="modal-dialog-otp" class="modal hide fade" style="z-index: 1000000" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                     <form id="form_otp" action="#" class="form-validate form-horizontal">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 id="modalLabel">' . JText::_("COM_EASYSDI_SHOP_ORDER_DIALOG_OTP_HEADER") . '</h3>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger" id="otpmessage" >
                            </div>
                            <p><div id="modal-dialog-body-text">' . JText::_("COM_EASYSDI_SHOP_ORDER_DIALOG_OTP_ASK_AUTH") . '</div></p>
                            <div class="control-group">
                            <div class="control-label"><label id="otp-lbl" for="otp_password" class="" aria-invalid="false">' . JText::_("COM_EASYSDI_SHOP_ORDER_DIALOG_OTP_LABEL") . '</label></div>
                            <div class="controls"><div class="input-append">
                                    <input type="password" name="otp" id="otp" value="" class="required" aria-required="true" required="required" aria-invalid="false">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_EASYSDI_SHOP_ORDER_MODAL_BTN_ABORT") . '</button>
                            <input type="submit"  class="btn btn-danger"  value="' . JText::_("COM_EASYSDI_SHOP_ORDER_MODAL_BTN_DOWNLOAD") . '"></input>
                        </div>
                    <input type="hidden" id="order_id" name="order_id">
                    <input type="hidden" id="diffusion_id" name="diffusion_id">
                    </form>
                </div>';
    }
    

    /**
     * Get a phrase like "2 hours ago" from a date. Units are: Year, Month, day, hour, minute, seconds
     * 
     * @param DateTime $date
     * @return String "xx timeUnit ago"
     */
    public static function getRelativeTimeString(DateTime $date) {
        $current = new DateTime;
        $diff = $current->diff($date);
        $units = array("YEAR" => $diff->format("%y"),
            "MONTH" => $diff->format("%m"),
            "DAY" => $diff->format("%d"),
            "HOUR" => $diff->format("%h"),
            "MINUTE" => $diff->format("%i"),
            "SECOND" => $diff->format("%s"),
        );
        $out = JText::_('COM_EASYSDI_SHOP_TIME_NOW');
        foreach ($units as $unit => $amount) {
            if (empty($amount)) {
                continue;
            }
            $out = JText::plural('COM_EASYSDI_SHOP_TIME_' . $unit . '_AGO', $amount);
            break;
        }
        return $out;
    }

    /**
     * Return a bootstrap html label depending on a basket status
     * @param type $order an order element
     * @param type $basket an easysdi basket (matching the order)
     * @return string A bootstrap styled label element
     */
    public static function getOrderStatusLabel($order, $basket, $isGroupedBySupplier = false, $witharchivedstate = false) {

        //for drafts, return the order type
        if ($order->ordertype_id == 3) {
            return'<span class="label">' . JText::_($order->ordertype) . '</span>';
        }

        //order and estimates (no draft)
        $progressCount = 0;
        $statusCompl = '';
        $labelClass = '';

        //if extractions have been grouped by supplier, push them in an array
        $tmpExtractions = array();
        if ($isGroupedBySupplier) {
            foreach ($basket->extractions as $supplier) {
                $tmpExtractions = array_merge($tmpExtractions, $supplier->items);
            }
        } else {
            $tmpExtractions = $basket->extractions;
        }

        //count finished products
        foreach ($tmpExtractions as $extraction) {
            if (
                    $extraction->productstate_id == self::PRODUCTSTATE_AVAILABLE ||
                    $extraction->productstate_id == self::PRODUCTSTATE_DELETED ||
                    $extraction->productstate_id == self::PRODUCTSTATE_REJECTED_SUPPLIER ||
                    $extraction->productstate_id == self::PRODUCTSTATE_REJECTED_TP ||
                    $extraction->productstate_id == self::PRODUCTSTATE_BLOCKED
            ) {
                $progressCount++;
            }
        }

        switch ($order->orderstate_id) {
            case self::ORDERSTATE_HISTORIZED:
            case self::ORDERSTATE_FINISH:
                $labelClass = 'label-success';
                if (count($tmpExtractions) > 1) {
                    $statusCompl = ' (' . $progressCount . '/' . count($tmpExtractions) . ')';
                }
                break;
            case self::ORDERSTATE_AWAIT:
                $labelClass = 'label-warning';
                break;
            case self::ORDERSTATE_PROGRESS:
                $labelClass = 'label-info';
                if (count($tmpExtractions) > 1) {
                    $statusCompl = ' (' . $progressCount . '/' . count($tmpExtractions) . ')';
                }
                break;
            case self::ORDERSTATE_SENT:
                $labelClass = 'label-inverse';
                break;
            case self::ORDERSTATE_SAVED:
                $labelClass = 'label-success';
                break;
            case self::ORDERSTATE_VALIDATION:
                $labelClass = 'label-warning';
                break;
            case self::ORDERSTATE_REJECTED:
            case self::ORDERSTATE_REJECTED_SUPPLIER:
                $labelClass = 'label-important';
                break;
        }

        $result = '<span class="label ' . $labelClass . '">' . JText::_($order->orderstate) . $statusCompl . '</span>';
        if ($witharchivedstate && $order->archived == 1) {
            $result .= ' <span class="order-archived-label label label-important" >' . JText::_('ARCHIVED') . '</span>';
        };


        return $result;
    }

    /**
     * Return a string shortent at the end of penultimate word and add a endin string (...)
     * @param string $string The string to shorten
     * @param int $len The length to shorten the string to
     * @param string $endchars The end character(s) , default '...'
     * @return string the shorenend string
     */
    public static function getShortenedString($string, $len, $endchars = '...') {
        if (strlen($string) > $len) {
            // truncate string
            $$stringtCut = substr($string, 0, $len);
            // make sure it ends in a word so assassinate doesn't become ass...
            return substr($$stringtCut, 0, strrpos($$stringtCut, ' ')) . $endchars;
        } else {
            return $string;
        }
    }

    /**
     * getCleanFilename - Remove path information and dots around the filename, to prevent uploading
     * into different directories or replacing hidden system files.
     * Also remove control characters and spaces (\x00..\x20) around the filename.
     * If filename is empty before or after cleaning, a guid is returned.
     * 
     * @param string $name
     * @return string new clean filename
     */
    public static function getCleanFilename($name) {
        $name = trim(basename(stripslashes($name)), ".\x00..\x20");
        if (!$name) {
            $name = str_replace('.', '-', microtime(true));
        }
        return $name;
    }

    /**
     * Return a human readable filesize
     * @param int $bytes
     * @param int $decimals default: 2
     * @return string Human readable file size example : 12.31 MB
     */
    public static function getHumanReadableFilesize($bytes, $decimals = 2) {
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    /**
     * getPricingOrder - retrieve a pricingorder object from the order's id
     * 
     * @param integer $oId
     * 
     * @return stdClass pricingorder object
     * @since 4.3.0
     */
    public static function getPricingOrder($oId) {
        $poModel = JModelLegacy::getInstance('PricingOrder', 'Easysdi_shopModel');
        $po = $poModel->getTable();
        $po->load(array('order_id' => $oId));
        return $po;
    }

    /**
     * getPricingOrderSupplier - retrieve a pricingordersupplier from its id
     * 
     * @param integer $posId
     * 
     * @return stdClass pricingordersupplier object
     * @since 4.3.0
     * 
     * call getException if the pricingordersupplier cannot be loaded
     */
    public static function getPricingOrderSupplier($posId) {
        $posModel = JModelLegacy::getInstance('PricingOrderSupplier', 'Easysdi_shopModel');
        $pos = $posModel->getTable();
        if (!($pos->load(array('id' => $posId)))) {
            return null;
        }
        return $pos;
    }

    /**
     * getPricingOrderSupplierProduct - retrieve a pricingordersupplierproduct object
     * 
     * @param integer $dId
     * @param integer $poId
     * 
     * @return stdClass pricingordersupplierproduct object
     * @since 4.3.0
     * 
     * call getException if the pricingordersupplierproduct cannot be loaded
     */
    public static function getPricingOrderSupplierProduct($dId, $poId) {
        $db = JFactory::getDbo();
        $pospModel = JModelLegacy::getInstance('PricingOrderSupplierProduct', 'Easysdi_shopModel');
        $posp = $pospModel->getTable();

        $db->setQuery($db->getQuery(true)
                        ->select('posp.id')
                        ->from('#__sdi_pricing_order_supplier_product posp')
                        ->innerJoin('#__sdi_pricing_order_supplier pos ON pos.id=posp.pricing_order_supplier_id')
                        ->where('posp.product_id=' . (int) $dId)
                        ->where('pos.pricing_order_id=' . (int) $poId));

        $pospId = $db->loadResult();
        if ($pospId == null || !($posp->load(array('id' => $pospId)))) {
            return null;
        }
        return $posp;
    }

    /**
     * downloadOrderFile get the file from local or remote storage,
     * rights must have been checked before
     * @param type $orderdiffusion easySDI order diffusion
     * @return type
     */
    public static function downloadOrderFile($orderdiffusion) {
        //remote stroage, use curl
        if ($orderdiffusion->storage_id == Easysdi_shopHelper::EXTRACTSTORAGE_REMOTE) {

            $curlHelper = new CurlHelper(true);

            $curldata['url'] = $orderdiffusion->file;
            $curldata['filename'] = $orderdiffusion->displayName;
            $curlHelper->get($curldata);
        }
        //local storage
        else {
            $folder = JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder');
            $file = JPATH_ROOT . '/' . $folder . '/' . $orderdiffusion->order_id . '/' . $orderdiffusion->diffusion_id . '/' . $orderdiffusion->file;

            error_reporting(0);

            $chunk = 8 * 1024 * 1024; // bytes per chunk (10 MB)

            $file_size = filesize($file);
	    $size = $file_size >= 0 ? $file_size : 4*1024*1024*1024 + $file_size;
            
            if ($size > $chunk) {
                set_time_limit(0);
                ignore_user_abort(false);
                ini_set('output_buffering', 0);
                ini_set('zlib.output_compression', 0);

                $fh = fopen($file, "rb");

                if ($fh === false) {
                    $this->setMessage(JText::_('RESOURCE_LOCATION_UNAVAILABLE'), 'error');
                    die();
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Accept-Ranges: bytes");
                header('Content-Disposition: attachment; filename="' . $orderdiffusion->file . '"');
                header('Expires: -1');
                header('Cache-Control: no-cache');
                header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
                header('Pragma: public');
                //do not check filesize on 32bits integer systems : http://php.net/manual/en/function.filesize.php#refsect1-function.filesize-returnvalues
                if (PHP_INT_SIZE >= 8) {
                    header('Content-Length: ' . filesize($file));
                }

                // Repeat reading until EOF
                while (!feof($fh)) {
                    $buffer = fread($fh, $chunk);
                    echo $buffer;
                    @ob_flush();  // flush output
                    @flush();
                }
            } else {
                ini_set('zlib.output_compression', 0);
                header('Pragma: public');
                header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
                header('Content-Transfer-Encoding: none');
                header("Content-Length: " . filesize($file));
                header('Content-Type: application/octetstream; name="' . $orderdiffusion->file . '"');
                header('Content-Disposition: attachement; filename="' . $orderdiffusion->file . '"');

                readfile($file);
            }
            
            $order = JTable::getInstance('order', 'Easysdi_shopTable');
            $order->load($orderdiffusion->order_id);
            
            $currentUser = sdiFactory::getSdiUser();
            $clientUser = new sdiUser((int) $order->user_id);
            
            $userExtrationsResponsible = $currentUser->getResponsibleExtraction();
            if (!is_array($userExtrationsResponsible)) {
                $userExtrationsResponsible = array();
            }
            
            $diffusion = JTable::getInstance('diffusion', 'Easysdi_shopTable');
            $diffusion->load($orderdiffusion->diffusion_id);
            
            //if diffusion securized by OTP and download not done by the responsible of extraction
            if (($diffusion->otp == 1) && (!in_array($orderdiffusion->diffusion_id, $userExtrationsResponsible)))
            {
                //Delete folder
                $orderResponseFolderBase = JPATH_ROOT . JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder');
                $orderResponseFolder = $orderResponseFolderBase . '/' . $orderdiffusion->order_id .'/'.$orderdiffusion->diffusion_id;
                if (JFolder::exists($orderResponseFolder)) {
                    JFolder::delete($orderResponseFolder);
                }
                $orderdiffusion->productstate_id = Easysdi_shopHelper::PRODUCTSTATE_DELETED;
                $orderdiffusion->store();
            }
            
            die();
        }
    }

}
