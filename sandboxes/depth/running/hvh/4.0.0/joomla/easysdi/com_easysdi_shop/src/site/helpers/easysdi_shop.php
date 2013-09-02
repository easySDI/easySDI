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
        if (empty($item))
            return true;

        $extraction = json_decode($item);

        //Get the session basket content
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if (empty($basketcontent)) {
            //Create the array
            $basketcontent = new stdClass();
            $basketcontent->extractions = array();
            $basketcontent->perimeters = array();
        }

        if (!is_array($basketcontent)) {
            //Problem the session variable must be an array
            JError::raiseError(500, 'Session variable corrupted');
            return false;
        }

        //Complete extraction object with allowed perimeters
        $diffusionperimeter = JTable::getInstance('diffusionperimeter', 'Easysdi_shopTable');
        $perimeters = $diffusionperimeter->loadBydiffusionID($extraction->id);
        if ($perimeters === false) {
            //Can't load linked perimeters
            return false;
        }
        $extraction->perimeters = $perimeters;

        //Add the new extraction to the basket
        if (count($basketcontent->extractions) == 0):
            //First add
            $basketcontent->extractions[] = $extraction;
            $basketcontent->perimeters[] = $extraction->perimeters;
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
            if(count($common) == 0):
                //There is no more common perimeter between the extraction in the basket
                //Extraction can not be added, send a message to the user
                return false;
            endif;
            $basketcontent->extractions[] = $extraction;
            $basketcontent->perimeters = $common;
        endif;


        
        //If there is already a perimeter defined for the basket, check if this perimeter is allowed for the new extraction


        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);

//        $result = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
//        foreach ($result as $r){
//            
//            $v= $r;
//        }

        return true;
    }

    public static function removeFromBasket($id) {
        if (empty($id))
            return true;

        //Get the session basket content
        $json = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if (empty($json)) {
            return true;
        }

        $basketcontent = json_decode($json);

        if (!is_array($basketcontent)) {
            //When there 
            JError::raiseError(500, 'Session variable corrupted');
            return false;
        }

        foreach ($basketcontent as $key => $encodedcontent):
            $content = json_decode($encodedcontent);
            if ($content->id == $id) {
                unset($basketcontent[$key]);
                break;
            }
        endforeach;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', json_encode($basketcontent));

        return true;
    }

}

