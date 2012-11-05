<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Layergroup controller class.
 */
class Easysdi_mapControllerLayergroup extends JControllerForm
{

    function __construct() {
        $this->view_list = 'layergroups';
        parent::__construct();
    }

}