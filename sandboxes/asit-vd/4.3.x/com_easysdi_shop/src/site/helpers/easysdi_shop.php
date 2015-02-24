<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;


require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/diffusion.php';
require_once JPATH_COMPONENT_SITE . '/libraries/easysdi/sdiBasket.php';
require_once JPATH_COMPONENT_SITE . '/libraries/easysdi/sdiExtraction.php';
require_once JPATH_COMPONENT_SITE . '/libraries/easysdi/sdiPerimeter.php';
require_once JPATH_COMPONENT_SITE . '/libraries/easysdi/sdiPricingProfile.php';
require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';

jimport('joomla.application.component.helper');

abstract class Easysdi_shopHelper {
    
    // PRICING
    const PRICING_FREE                  = 1;
    const PRICING_FEE_WITHOUT_PROFILE   = 2;
    const PRICING_FEE_WITH_PROFILE      = 3;
    
    const ROLE_MEMBER                   = 1;
    const ROLE_RESOURCEMANAGER          = 2;
    const ROLE_METADATARESPONSIBLE      = 3;
    const ROLE_METADATAEDITOR           = 4;
    const ROLE_DIFFUSIONMANAGER         = 5;
    const ROLE_PREVIEWMANAGER           = 6;
    const ROLE_EXTRACTIONRESPONSIBLE    = 7;
    const ROLE_PRICINGMANAGER           = 9;
    const ROLE_VALIDATIONMANAGER        = 10;

    /**
     * 
     * @param string $item : json {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
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
                                    if ($bperimeter->allowedbuffer == 0 || $perimeter->allowedbuffer == 0):
                                        $cperimeter->allowedbuffer = 0;
                                        continue 2;
                                    endif;
                                endif;
                            endforeach;
                            if ($bperimeter->allowedbuffer == 0):
                                $common[] = $bperimeter;
                            else:
                                $common[] = $perimeter;
                            endif;
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
     * 
     * @param int $id
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
                    foreach ($common as $cperimeter):
                        if ($perimeter->id == $cperimeter->id):
                            if ($perimeter->allowedbuffer == 0 || $cperimeter->allowedbuffer == 0):
                                $cperimeter->allowedbuffer = 0;
                                continue 2;
                            endif;
                        endif;
                    endforeach;
                    $common[] = $perimeter;
                endforeach;
            endforeach;

            $basket->setPerimeters($common);
        endif;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));

//        $return['COUNT'] = count($basket->extractions);
//        echo json_encode($return);
//        die();
    }

    public static function abortAdd() {
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', null);
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $return['ABORT'] = count($basket->extractions);
        echo json_encode($return);
        die();
    }

    /**
     * 
     * @param string $item : json {"id":perimeter_id,"name":perimeter_name,"features":[{"id": feature_id, "name":feature_name}]}
     */
    public static function addExtentToBasket($item) {
        //add extent if defined
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $basket->extent = empty($item) ? null : json_decode($item);
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
        $return = array(
            'MESSAGE'   => 'OK',
            'extent'    => $basket->extent
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

    public static function getHTMLOrderPerimeter($item) {
        $params = JFactory::getApplication()->getParams('com_easysdi_shop');
        $paramsarray = $params->toArray();

        $mapscript = Easysdi_mapHelper::getMapScript($paramsarray['ordermap'], true);
        
        ?> <div class="row-fluid" >
            <h3><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_PERIMETER'); ?></h3>
            
            <div class="row-fluid" >
                <div class="map-recap span8" >
                   <div >
                        <?php
                        echo $mapscript;
                        ?>
                    </div>                
                </div>
                <div  class="value-recap span4" >
                    <div id="perimeter-buffer" class="row-fluid" >
                        <div><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_BUFFER'); ?></h4>
                            <span><?php if (!empty($item->basket->buffer)) echo (float)$item->basket->buffer; ?></span>                            
                        </div>                                
                    </div>
                    <div  class="row-fluid" >
                        <?php if (!empty($item->basket->extent)): ?>
                            <div><h4><?php echo JText::_('COM_EASYSDI_SHOP_BASKET_SURFACE'); ?></h4>
                                <div><?php
                                    if (!empty($item->basket->extent->surface)) :
                                        if (floatval($item->basket->extent->surface) > intval($paramsarray['maxmetervalue'])):
                                            echo round(floatval($item->basket->extent->surface) / 1000000, intval($paramsarray['surfacedigit']));
                                            echo JText::_('COM_EASYSDI_SHOP_BASKET_KILOMETER');
                                        else:
                                            echo round(floatval($item->basket->extent->surface), intval($paramsarray['surfacedigit']));
                                            echo JText::_('COM_EASYSDI_SHOP_BASKET_METER');
                                        endif;
                                    endif;
                                    ?></div>
                            </div>                                
                            
                        <?php endif; ?>
                    </div>                           
                </div>
                
            </div>
            <div class="row-fluid" >
                <div id="perimeter-recap" class="span12" >
                   <div><h4><?php echo JText::_($item->basket->extent->name); ?></h4></div>                        
                        <?php
                        if (is_array($item->basket->extent->features)):  
                            ?> <div id="perimeter-recap-details" style="overflow-y:scroll; height:100px;"> <?php
                            foreach ($item->basket->extent->features as $feature):
                                ?>
                                <div><?php echo $feature->name; ?></div>
                                <?php
                            endforeach; 
                            ?> </div>   <?php
                        endif;
                        ?>
                                      
                </div>
            </div>
        </div>
        <?php
    }
    
    function rrmdir($dir) { 
        if (is_dir($dir)) { 
          $objects = scandir($dir); 
          foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
              if (filetype($dir."/".$object) == "dir") Easysdi_shopHelper::rrmdir($dir."/".$object); else unlink($dir."/".$object); 
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
        if(function_exists('http_build_url'))
            return http_build_url($url, $parts);
        
        $parsed_url = array_merge($url, $parts);
        
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
        $pass     = ($user || $pass) ? "$pass@" : ''; 
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
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
    private static function rounding($value, $rounding = 0.01){
        return round($value/$rounding, 0)*$rounding;
    }
    
    /**
     * extractionsBySupplierGrouping - reword extractions array grouping products by suppliers
     * 
     * @param type $basket
     * @return voide
     * @since 4.3.0
     */
    public static function extractionsBySupplierGrouping(&$basket){
        $myItems = $basket->extractions;
        $basket->extractionsNb = count($myItems);
        $basket->extractions = array();
        if($basket->extractionsNb){
            foreach($myItems as $myItem){
                if(!isset($basket->extractions[$myItem->organism_id])){
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
     * basketPriceCalculation - calculate pricing for a basket
     * 
     * @param type $basket
     * @return void
     * @since 4.3.0
     */
    public static function basketPriceCalculation(&$basket){
        
        // init prices object
        $prices = new stdClass();
        $prices->isActivated = (bool)JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');
        
        // test if pricing is activated
        if($prices->isActivated){
            // set the invoiced user (based on current user and third-party)
            $prices->debtor = new stdClass();
            if(isset($basket->thirdparty)){
                $prices->debtor->id = $basket->thirdparty;
            }
            else{
                $prices->debtor->id = $basket->sdiUser->role[self::ROLE_MEMBER][0]->id;
                $prices->debtor->name = $basket->sdiUser->role[self::ROLE_MEMBER][0]->name;
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('c.id')
                    ->from('#__sdi_organism_category oc')
                    ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                    ->where('oc.organism_id='.(int)$prices->debtor->id);
            $db->setQuery($query);
            $prices->debtor->categories = $db->loadColumn();
            
            // get easysdishop config
            $prices->cfg_vat = (float)JComponentHelper::getParams('com_easysdi_shop')->get('vat', 0);
            $prices->cfg_currency = JComponentHelper::getParams('com_easysdi_shop')->get('currency', 'CHF');
            $prices->cfg_rounding = (float)JComponentHelper::getParams('com_easysdi_shop')->get('rounding', 0.05);
            $prices->cfg_overall_default_fee = (float)JComponentHelper::getParams('com_easysdi_shop')->get('overall_default_fee', 0);
            $prices->cfg_free_data_fee = (bool)JComponentHelper::getParams('com_easysdi_shop')->get('free_data_fee', false);
            
            // get the surface ordered - default to 0
            $prices->surface = isset($basket->extent) && isset($basket->extent->surface) ? $basket->extent->surface/1000000 : 0;

            // calculate prices by supplier
            $prices->suppliers = array();
            $prices->cal_total_amount_ti = 0;
            $prices->hasFeeWithoutPricingProfileProduct = false;
            foreach($basket->extractions as $supplier_id => $supplier){
                $prices->suppliers[$supplier_id] = self::basketPriceCalculationBySupplier($supplier, $prices);
                if($prices->suppliers[$supplier_id]->hasFeeWithoutPricingProfileProduct)
                    $prices->hasFeeWithoutPricingProfileProduct = true;
                else
                    $prices->cal_total_amount_ti += $prices->suppliers[$supplier_id]->cal_total_amount_ti;
            }
            
            // set the platform tax
            if($prices->cal_total_amount_ti==0 && !$prices->cfg_free_data_fee)
                $prices->cal_fee_ti = 0;
            else{
                $prices->cal_fee_ti = $prices->cfg_overall_default_fee;
                
                if(count($prices->debtor->categories)){
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true)
                            ->select('c.overall_fee, c.name')
                            ->from('#__sdi_category c')
                            ->where('c.overall_fee IS NOT NULL')
                            ->where('c.id IN ('.implode(',', $prices->debtor->categories).')')
                            ->order('overall_fee');
                    $db->setQuery($query, 0, 1);
                    $category = $db->loadObject();

                    if($category !== null){
                        $prices->cal_fee_ti = $category->overall_fee;
                        $prices->ind_lbl_category_order_fee = $category->name;
                    }
                }
            }
            
            $prices->cal_fee_ti = self::rounding($prices->cal_fee_ti, $prices->cfg_rounding);
            
            // total amount for the platform
            if($prices->hasFeeWithoutPricingProfileProduct)
                $prices->cal_total_amount_ti = '-';
            else
                $prices->cal_total_amount_ti += $prices->cal_fee_ti;
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
    private static function basketPriceCalculationBySupplier($supplier, $prices){
        
        // init provider sub-object
        $provider = new stdClass();
        $provider->id = (int)$supplier->id;
        
        // get organism pricing params
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('o.name, o.internal_free, o.fixed_fee_ti, o.data_free_fixed_fee')
                ->from('#__sdi_organism o')
                ->where('o.id='.$provider->id);
        $db->setQuery($query);
        $organism = $db->loadObject();
        
        $provider->name = $organism->name;
        $provider->cfg_internal_free = (bool)$organism->internal_free;
        $provider->cfg_fixed_fee_ti = $organism->fixed_fee_ti;
        $provider->cfg_data_free_fixed_fee = (bool)$organism->data_free_fixed_fee;
        
        // calculate supplier rebate
	$internalFreeOrder = false;
        $prices->supplierRebate = new stdClass();
        if($provider->cfg_internal_free==true && $prices->debtor->id==$provider->id){
	    $internalFreeOrder = true;
            $prices->supplierRebate->pct = 100;
            $prices->supplierRebate->name = $organism->name;
        }
        elseif(count($prices->debtor->categories)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('ocpr.rebate, c.name')
                    ->from('#__sdi_organism_category_pricing_rebate ocpr')
                    ->join('LEFT', '#__sdi_category c ON c.id=ocpr.category_id')
                    ->where('ocpr.organism_id='.$provider->id.' AND ocpr.category_id IN ('.implode(',', $prices->debtor->categories).')')
                    ->order('rebate DESC')
                    ;
            $db->setQuery($query, 0, 1);
            $supplierRebate = $db->loadAssoc();
            $prices->supplierRebate->pct = $supplierRebate['rebate'];
            $prices->supplierRebate->name = $supplierRebate['name'];
        }
        else{
            $prices->supplierRebate->pct = 0;
            $prices->supplierRebate->name = '';
        }
        
        // calculate price for each product and increment the provider sub-total
        $provider->products = array();
        $provider->cal_total_amount_ti = 0;
        $provider->cal_total_rebate_ti = 0;
        $provider->hasFeeWithoutPricingProfileProduct = false;
        foreach($supplier->items as $product){
            $provider->products[$product->id] = self::basketPriceCalculationByProduct($product, $prices);
            if($product->pricing == self::PRICING_FEE_WITHOUT_PROFILE && !$internalFreeOrder)
                $provider->hasFeeWithoutPricingProfileProduct = true;
            elseif($product->pricing == self::PRICING_FEE_WITH_PROFILE){
                $provider->cal_total_amount_ti += $provider->products[$product->id]->cal_total_amount_ti;
                $provider->cal_total_rebate_ti += $provider->products[$product->id]->cal_total_rebate_ti;
            }
        }
        
        // set the provider tax
        $provider->cal_fee_ti = ($provider->cal_total_amount_ti>0 || $provider->cfg_data_free_fixed_fee) ? self::rounding($provider->cfg_fixed_fee_ti, $prices->cfg_rounding) : 0;
        
        // total amount for this provider
        if($provider->hasFeeWithoutPricingProfileProduct){
            unset($provider->cal_total_amount_ti);
            unset($provider->cal_total_rebate_ti);
        }
        else
            $provider->cal_total_amount_ti += $provider->cal_fee_ti;
        
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
    private static function basketPriceCalculationByProduct($product, $prices){
        
        // init product sub-object
        $price = new stdClass();
        
        $price->cfg_pricing_type = $product->pricing;
        
        if($price->cfg_pricing_type != self::PRICING_FEE_WITH_PROFILE)
            return $price;
        
        // get base parameters to calculate product price
        $pricingProfile = new sdiPricingProfile($product->pricing_profile);
        $price->cfg_profile_id = $pricingProfile->id;
        $price->cfg_profile_guid = $pricingProfile->guid;
        $price->cfg_profile_name = $pricingProfile->name;
        $price->cfg_fixed_fee = $pricingProfile->fixed_fee;
        $price->cfg_surface_rate = $pricingProfile->surface_rate;
        $price->cfg_min_fee = $pricingProfile->min_fee;
        $price->cfg_max_fee = $pricingProfile->max_fee;
        
        // calculate product price
        $price->cal_amount_data_te = self::rounding($price->cfg_fixed_fee + ($price->cfg_surface_rate * $prices->surface));
        
        // limit price according to min and max price
        if($price->cfg_max_fee>0 && $price->cal_amount_data_te > $price->cfg_max_fee){
            $price->cal_amount_data_te = $price->cfg_max_fee;
        }
        elseif($price->cfg_min_fee>0 && $price->cal_amount_data_te < $price->cfg_min_fee){
            $price->cal_amount_data_te = $price->cfg_min_fee;
        }
        
        // rebate based on category and profile
        $price->cfg_pct_category_profile_discount = 0;
        $price->ind_lbl_category_profile_discount = '';
        
        if(count($prices->debtor->categories)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('ppcpr.category_id, c.name')
                    ->from('#__sdi_pricing_profile_category_pricing_rebate ppcpr')
                    ->join('LEFT', '#__sdi_category c ON c.id=ppcpr.category_id')
                    ->where('ppcpr.pricing_profile_id='.(int)$pricingProfile->id)
                    ->where('ppcpr.category_id IN ('.  implode(',', $prices->debtor->categories).')');
            $db->setQuery($query);
            $category_free = $db->loadAssoc();
            
            if($category_free !== null){
                $price->cfg_pct_category_profile_discount = 100;
                $price->ind_lbl_category_profile_discount = $category_free['name'];
            }
        }
        
        // rebate based on category and supplier
        $price->cfg_pct_category_supplier_discount = $prices->supplierRebate->pct;
        $price->ind_lbl_category_supplier_discount = $prices->supplierRebate->name;
        
        // final price TE
        $price->cal_total_amount_te = self::rounding($price->cal_amount_data_te * (1 - $price->cfg_pct_category_profile_discount/100) * (1 - $price->cfg_pct_category_supplier_discount/100));
        $cal_total_rebate_te = self::rounding($price->cal_amount_data_te - $price->cal_total_amount_te);
        
        // final price TI
        $price->cal_total_amount_ti = self::rounding($price->cal_total_amount_te * (1 + $prices->cfg_vat/100), $prices->cfg_rounding);
        $price->cal_total_rebate_ti = self::rounding($cal_total_rebate_te * (1 + $prices->cfg_vat/100), $prices->cfg_rounding);
        
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
    public static function priceFormatter($price, $displayCurrency = true){
        return  $price == '-'
                ?   '-'
                :   (   $price == 0
                    ?   0
                    :   number_format(
                            $price, 
                            JComponentHelper::getParams('com_easysdi_shop')->get('digit_after_decimal', 2), 
                            JComponentHelper::getParams('com_easysdi_shop')->get('decimal_symbol', '.'), 
                            JComponentHelper::getParams('com_easysdi_shop')->get('digit_grouping_symbol', "'")
                        )
                    ).($displayCurrency ? ' '.JComponentHelper::getParams('com_easysdi_shop')->get('currency', 'CHF') : '')
                ;
    }
    
    /**************************/
    /** NOTIFICATION METHODS **/
    /**************************/
    
    /**
     * notifyCustomer - notify the customer of its order
     * 
     * @param string $orderName
     * @return void
     * @since 4.3.0
     */
    public static function notifyCustomer($orderName){
        $user = sdiFactory::getSdiUser();
        if(!$user->sendMail(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_CONFIRM_ORDER_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_CONFIRM_ORDER_BODY', $orderName)))
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
    }
    
    /**
     * notifyNotifiedUsers - notify the defined notified users for a diffusion
     * 
     * @param integer $diffusionId
     * @return void
     * @since 4.3.0
     */
    public static function notifyNotifiedUsers($diffusionId){
        //Get the user to notified when the order is saved
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('user_id')
                ->from('#__sdi_diffusion_notifieduser')
                ->where('diffusion_id = ' . (int)$diffusionId);
        $db->setQuery($query);
        $notifiedusers = $db->loadColumn();

        $diffusiontable = JTable::getInstance('diffusion', 'Easysdi_shopTable');
        $diffusiontable->load($diffusionId);

        //Send mail to notifieduser
        foreach ($notifiedusers as $notifieduser){
            $user = sdiFactory::getSdiUser($notifieduser);
            if (!$user->sendMail(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_NOTIFIEDUSER_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_NOTIFIEDUSER_BODY', $diffusiontable->name)))
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
        }
    }
    
    /**
     * notifyExtractionResponsible - notify the extraction responsible
     * 
     * @param integer $diffusionId
     * @return void
     * @since 4.3.0
     */
    public static function notifyExtractionResponsible($diffusionId){
        $diffusiontable = JTable::getInstance('diffusion', 'Easysdi_shopTable');
        $diffusiontable->load($diffusionId);
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('rr.user_id')
                ->from('#__sdi_user_role_resource rr')
                ->where('rr.role_id = 7')
                ->where('rr.resource_id = (SELECT r.id FROM #__sdi_resource r INNER JOIN #__sdi_version v ON v.resource_id = r.id WHERE v.id = ' . (int)$diffusiontable->version_id . ')');
        $db->setQuery($query);
        $responsible = $db->loadResult();
        
        $user = sdiFactory::getSdiUser($responsible);
        if (!$user->sendMail(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_NOTIFIEDUSER_SUBJECT'), JText::sprintf('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_RESPONSIBLE_BODY', $diffusiontable->name)))
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_SEND_MAIL_ERROR_MESSAGE'));
    }

}








