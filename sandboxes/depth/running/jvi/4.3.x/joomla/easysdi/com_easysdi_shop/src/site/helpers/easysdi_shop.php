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
        if (empty($item)):
            $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
            $basket->extent = null;
            JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));
            $return['MESSAGE'] = 'OK';
            echo json_encode($return);
            die();
        endif;

        $perimeter = json_decode($item);
        $basket = unserialize(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        $basket->extent = $perimeter;
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', serialize($basket));

        $return['MESSAGE'] = 'OK';
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
    
    private static function roundish($value, $roundish){
        return round($value/$roundish, 0)*$roundish;
    }
    
    public static function extractionsBySupplierGrouping(&$basket){
        $myItems = $basket->extractions;
        $basket->extractions = array();
        if(count($myItems)){
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
    
    public static function basketPriceCalculation(&$basket){
        
        // init prices object
        $prices = new stdClass();
        $prices->priceET = 0;
        $prices->rebate = 0;
        $prices->priceIT = 0;
        
        // timestamp when price was calculated
        $prices->tmstp = mktime();
        
        // set the invoiced user (based on current user and third-party
        $prices->debtor = new stdClass();
        if(isset($basket->thirdparty)){
            $prices->debtor->id = $basket->thirdparty;
        }
        else{
            $prices->debtor->id = $basket->sdiUser->role[2][0]->id;
            $prices->debtor->name = $basket->sdiUser->role[2][0]->name;
        }
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('c.id')
                ->from('#__sdi_organism_category oc')
                ->join('LEFT', '#__sdi_category c ON c.id=oc.category_id')
                ->where('oc.organism_id='.(int)$prices->debtor->id);
        $db->setQuery($query);
        $prices->debtor->categories = $db->loadColumn();
        
        // test if pricing is activated
        $prices->isActivated = (bool)JComponentHelper::getParams('com_easysdi_shop')->get('is_activated');
        if($prices->isActivated){
            // get easysdishop config
            $prices->tva = (float)JComponentHelper::getParams('com_easysdi_shop')->get('tva');
            $prices->tva_factor = 1 + $prices->tva/100;
            $prices->currency = JComponentHelper::getParams('com_easysdi_shop')->get('currency');
            $prices->roundish = (float)JComponentHelper::getParams('com_easysdi_shop')->get('roundish');
            $prices->overall_default_tax = (float)JComponentHelper::getParams('com_easysdi_shop')->get('overall_default_tax');
            $prices->tax_if_free_data = (bool)JComponentHelper::getParams('com_easysdi_shop')->get('tax_if_free_data');
            
            // get the surface ordered - default to 0
            $prices->surface = isset($basket->extent) && isset($basket->extent->surface) ? $basket->extent->surface : 0;

            // calculate prices by supplier
            $prices->suppliers = array();
            $prices->suppliersTaxes = 0;
            foreach($basket->extractions as $supplier_id => $supplier){
                $prices->suppliers[$supplier_id] = self::basketPriceCalculationBySupplier($supplier, $prices);
                $prices->suppliersTaxes += $prices->suppliers[$supplier_id]->tax;
                $prices->priceET += $prices->suppliers[$supplier_id]->priceET;
                $prices->rebate += $prices->suppliers[$supplier_id]->rebate['total'];
            }
        }
        
        // set the platform tax
        $prices->tax = $prices->overall_default_tax;
        
        if( ($prices->tax_if_free_data || $prices->priceET>0) && count($prices->debtor->categories)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('MAX(c.tax) as tax')
                    ->from('#__sdi_category c')
                    ->where('c.id IN ('.implode(',', $prices->debtor->categories).')');
            $db->setQuery($query);
            $tax = $db->loadResult();
            
            if($tax !== null) $prices->tax = $tax;
        }
        
        // calculate total for the basket
        $prices->priceIT = ($prices->priceET - $prices->rebate) * $prices->tva_factor + $prices->suppliersTaxes + $prices->tax;
        
        // roundish the total price
        $prices->priceIT = self::roundish($prices->priceIT, $prices->roundish);
        
        $basket->prices = $prices;
    }
    
    private static function basketPriceCalculationBySupplier($supplier, $prices){
        
        // init provider sub-object
        $provider = new stdClass();
        $provider->id = (int)$supplier->id;
        
        // get organism pricing params
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('o.name, o.internal_free, o.order_fixed_costs, o.fixed_costs_if_data_free')
                ->from('#__sdi_organism o')
                ->where('o.id='.$provider->id);
        $db->setQuery($query);
        $organism = $db->loadObject();
        
        $provider->name = $organism->name;
        $provider->internal_free = (bool)$organism->internal_free;
        $provider->order_fixed_costs = $organism->order_fixed_costs;
        $provider->fixed_costs_if_data_free = (bool)$organism->fixed_costs_if_data_free;
        $provider->priceET = 0;
        $provider->rebate = array('products' => 0, 'supplier' => 0, 'total' => 0);
        $provider->priceIT = 0;
        $provider->products = array();
        
        // calculate price for each product and increment the provider sub-total
        foreach($supplier->items as $product){
            $provider->products[$product->id] = self::basketPriceCalculationByProduct($product, $prices);
            $provider->priceET += $provider->products[$product->id]->priceET;
            $provider->rebate['products'] += $provider->products[$product->id]->rebate;
        }
        
        // calculate supplier rebate
        if($provider->internal_free && $prices->debtor->id==$provider->id){
            $provider->rebate['supplier'] = 100;
        }
        elseif(count($prices->debtor->categories)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('MAX(ocpr.rebate) as rebate')
                    ->from('#__sdi_organism_category_pricing_rebate ocpr')
                    ->where('ocpr.organism_id='.$provider->id.' AND ocpr.category_id IN ('.implode(',', $prices->debtor->categories).')');
            $db->setQuery($query);
            $provider->rebate['supplier'] = $db->loadResult();
        }
        
        $provider->rebate['total'] = $provider->priceET * $provider->rebate['supplier']/100 + $provider->rebate['products'];
        $provider->rebate['total'] = self::roundish($provider->rebate['total'], $prices->roundish);
        
        // set the provider tax
        $provider->tax = ($provider->priceET>0 || $provider->fixed_costs_if_data_free===true) ? $provider->order_fixed_costs : 0;
        
        // calculate total for the current provider
        $provider->priceIT = ($provider->priceET - $provider->rebate['total']) * $prices->tva_factor + $provider->tax;
        
        // roundish the total price
        $provider->priceIT = self::roundish($provider->priceIT, $prices->roundish);
        
        return $provider;
    }
    
    private static function basketPriceCalculationByProduct($product, $prices){
        $price = new stdClass();
        $pricingProfile = new sdiPricingProfile($product->pricing_profile);
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('ppcf.category_id')
                ->from('#__sdi_pricing_profile_category_free ppcf')
                ->where('ppcf.pricing_profile_id='.(int)$pricingProfile->id);
        $db->setQuery($query);
        $price->category_free = $db->loadColumn();
        
        // get base parameters to calculate product price
        $price->fixedPrice = $pricingProfile->fixed_price;
        $price->surfacePrice = $pricingProfile->surface_price;
        
        // calculate product price
        $price->priceET = $price->fixedPrice + ($price->surfacePrice * $prices->surface);
        
        // get min and max pricing profile price
        $price->minPrice = $pricingProfile->min_price;
        $price->minPriceReached = false;
        $price->maxPrice = $pricingProfile->max_price;
        $price->maxPriceReached = false;
        
        // limit price according to min and max price
        if($price->maxPrice>0 && $price->priceET > $price->maxPrice){
            $price->priceET = $price->maxPrice;
            $price->maxPriceReached = true;
        }
        elseif($price->minPrice>0 && $price->priceET < $price->minPrice){
            $price->priceET = $price->minPrice;
            $price->minPriceReached = true;
        }
        
        // calculate rebate
        $rebate = 0;
        if(count(array_intersect($price->category_free, $prices->debtor->categories)))
            $rebate = 100;
        $price->rebate = $price->priceET * $rebate/100;
        
        // calculate final price applying VAT and rebate
        $price->priceIT = ($price->priceET - $price->rebate) * $prices->tva_factor;
        
        // roundish the total price
        $price->priceIT = self::roundish($price->priceIT, $prices->roundish);
        
        return $price;
    }

}








