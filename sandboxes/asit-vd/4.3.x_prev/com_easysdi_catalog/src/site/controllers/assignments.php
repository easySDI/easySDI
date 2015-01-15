<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Assignments controller class.
 */
class Easysdi_catalogControllerAssignments extends Easysdi_catalogController{
    
    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'Resources', $prefix = 'Easysdi_coreModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
}