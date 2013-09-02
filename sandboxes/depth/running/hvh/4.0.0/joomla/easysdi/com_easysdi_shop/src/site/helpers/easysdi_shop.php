<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/tables/diffusionperimeter.php';

abstract class Easysdi_shopHelper {

    /**
     * 
     * @param string $item : json {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
     */
    public static function addToBasket($item) {
        if (empty($item)):
            $return['MESSAGE'] = 0;
            echo json_encode($return);
            die();
        endif;

        $extraction = json_decode($item);

        //Get the session basket content
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if (empty($basketcontent)) {
            //Create the array
            $basketcontent = new stdClass();
            $basketcontent->extractions = array();
            $basketcontent->perimeters = array();
        }

        //Complete extraction object with allowed perimeters
        $diffusionperimeter = JTable::getInstance('diffusionperimeter', 'Easysdi_shopTable');
        $perimeters = $diffusionperimeter->loadBydiffusionID($extraction->id);
        if ($perimeters === false) {
            //Can't load linked perimeters
            $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ADD_BASKET_ERROR_NO_PERIMETER');
            echo json_encode($return);
            die();
        }
        $extraction->perimeters = $perimeters;

        //Add the new extraction to the basket
        if (count($basketcontent->extractions) == 0):
            //First add
            $basketcontent->extractions[] = $extraction;
            $basketcontent->perimeters = $extraction->perimeters;
        else:
            //There is already extractions in the basket
            //Check if there is at least one common perimeter within all the extractions in the basket
            $common = array();
            foreach ($basketcontent->perimeters as $perimeter):
                foreach ($extraction->perimeters as $newperimeter):
                    if ($newperimeter->perimeter_id == $perimeter->perimeter_id):
                        //Common perimeter
                        $common[] = $perimeter;
                        break;
                    endif;
                endforeach;
            endforeach;
            if (count($common) == 0):
                //There is no more common perimeter between the extraction in the basket
                //Extraction can not be added, send a message to the user
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ADD_BASKET_ERROR_NO_COMMON_PERIMETER');
                echo json_encode($return);
                die();
            endif;
            //If there is already a perimeter defined for the basket, check if this perimeter is allowed for the new extraction
            if (!empty($basketcontent->extent)):
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ADD_BASKET_ERROR_EXISTING_EXTENT');
                echo json_encode($return);
                die();
            endif;
            $basketcontent->extractions[] = $extraction;
            $basketcontent->perimeters = $common;
        endif;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);

        $return['MESSAGE'] = count($basketcontent->extractions);
        echo json_encode($return);
        die();
    }

    /**
     * 
     * @param int $id
     */
    public static function removeFromBasket($id) {
        if (empty($id)):
            $return['MESSAGE'] =0;
            echo json_encode($return);
            die();
        endif;

        //Get the session basket content
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if (empty($basketcontent)) :
            $return['MESSAGE'] = 0;
            echo json_encode($return);
            die();
        endif;

        foreach ($basketcontent->extractions as $key => $extraction):
            if ($extraction->id == $id) {
                unset($basketcontent->extractions[$key]);
                break;
            }
        endforeach;
        
        $basketcontent->perimeters = Easysdi_shopHelper::getCommonPerimeter($basketcontent);

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);

        $return['MESSAGE'] = count($basketcontent->extractions);
            echo json_encode($return);
            die();
    }
    
    /**
     * 
     * @param stdClass $basketcontent
     * @return array common perimeter object
     */
    private static function getCommonPerimeter ($basketcontent){
        $common = array();
        foreach ($basketcontent->extractions as $extraction):
            foreach ($extraction->perimeters as $perimeter):
                    if (!in_array($perimeter, $common)):
                        $common[] = $perimeter;
                    endif;
            endforeach;
        endforeach;
        
        return $common;
    }

}

