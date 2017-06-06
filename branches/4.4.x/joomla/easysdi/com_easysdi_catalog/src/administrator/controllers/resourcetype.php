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

jimport('joomla.application.component.controllerform');

/**
 * Resourcetype controller class.
 */
class Easysdi_catalogControllerResourcetype extends JControllerForm
{

    function __construct() {
        $this->view_list = 'resourcestype';
        parent::__construct();
    }

}