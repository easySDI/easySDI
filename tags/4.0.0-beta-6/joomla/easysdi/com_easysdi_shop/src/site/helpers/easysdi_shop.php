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
require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';

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

}

