<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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