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
    public static function addToBasket($item, $force = false) {
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

        if (empty($item)):
            $return['COUNT'] = 0;
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
            foreach ($extraction->perimeters as $perimeter):
                if (in_array($perimeter, $basketcontent->perimeters)):
                    if (!in_array($perimeter, $common)):
                        $common[] = $perimeter;
                    endif;
                endif;
            endforeach;


            if (count($common) == 0):
                //There is no more common perimeter between the extraction in the basket
                //Extraction can not be added, send a message to the user
                $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_BASKET_ERROR_NO_COMMON_PERIMETER');
                echo json_encode($return);
                die();
            endif;
            //If there is already a perimeter defined for the basket, check if this perimeter is allowed for the new extraction
            if (!empty($basketcontent->extent) && !$force):
                $check = false;
                foreach ($common as $p):
                    if ($p->perimeter_id == $basketcontent->extent->id):
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
            elseif (!empty($basketcontent->extent) && $force):
                //Clean session
                JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', null);
                $basketcontent->extent = null;
            endif;
            $basketcontent->extractions[] = $extraction;
            $basketcontent->perimeters = $common;
        endif;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);
        $return['COUNT'] = count($basketcontent->extractions);
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
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if (empty($basketcontent)) :
            $return['COUNT'] = 0;
            echo json_encode($return);
            die();
        endif;

        foreach ($basketcontent->extractions as $key => $extraction):
            if ($extraction->id == $id) {
                unset($basketcontent->extractions[$key]);
                break;
            }
        endforeach;


        $common = array();
        foreach ($basketcontent->extractions as $extraction):
            foreach ($extraction->perimeters as $perimeter):
                if (in_array($perimeter, $basketcontent->extractions[0]->perimeters)):
                    if (!in_array($perimeter, $common)):
                        $common[] = $perimeter;
                    endif;
                endif;
            endforeach;
        endforeach;

        $basketcontent->perimeters = $common;

        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);

        $return['COUNT'] = count($basketcontent->extractions);
        echo json_encode($return);
        die();
    }

    public static function abortAdd() {
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.suspend', null);
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        $return['ABORT'] = count($basketcontent->extractions);
        echo json_encode($return);
        die();
    }

    /**
     * 
     * @param string $item : json {"id":perimeter_id,"name":perimeter_name,"features":[{"id": feature_id, "name":feature_name}]}
     */
    public static function addExtentToBasket($item) {
        if (empty($item)):
            $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
            $basketcontent->extent = null;
            JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);
            $return['MESSAGE'] = 'OK';
            echo json_encode($return);
            die();
        endif;

        $perimeter = json_decode($item);
        $basketcontent = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        $basketcontent->extent = $perimeter;
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', $basketcontent);

        $return['MESSAGE'] = 'OK';
        echo json_encode($return);
        die();
    }

}

