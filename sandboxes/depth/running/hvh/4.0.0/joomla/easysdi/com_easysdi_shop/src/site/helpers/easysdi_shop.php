<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

abstract class Easysdi_shopHelper {

    /**
     * 
     * @param string $item : json {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
     */
    public static function addToBasket($item) {
        if(empty($item ))
            return true;
            
        //Get the session basket content
        $json = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if(empty ($json)){
            //Create the array
            $basketcontent = array();
        }else{
            $basketcontent = json_decode($json, true);
        }
        if(!is_array($basketcontent)){
            //Problem the session variable must be an array
            JError::raiseError(500, 'Session variable corrupted');
            return false;
        }
        
        $basketcontent[] = $item;
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', json_encode($basketcontent));
                
        $result = json_decode(JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content'));
        foreach ($result as $r){
            $t = json_decode($r);
            $v= $t;
        }
        
        return true;
    }
    
    public static function removeFromBasket($id) {
        if(empty($id))
            return true;
        
        //Get the session basket content
        $json = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        if(empty ($json)){
            return true;
        }
        
        $basketcontent = json_decode($json);
        
        if(!is_array($basketcontent)){
            //When there 
            JError::raiseError(500, 'Session variable corrupted');
            return false;
        }
        
        foreach ($basketcontent as $key => $encodedcontent):
            $content = json_decode($encodedcontent);
            if($content->id == $id){
                unset($basketcontent[$key]);
                break;
            }
        endforeach;
        
        JFactory::getApplication()->setUserState('com_easysdi_shop.basket.content', json_encode($basketcontent));
                
        return true;
    }

}

