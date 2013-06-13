<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Attribute_value controller class.
 */
class Easysdi_catalogControllerAttribute_value extends JControllerForm
{

    function __construct() {
        $this->view_list = 'attribute_values';
        parent::__construct();
    }

}