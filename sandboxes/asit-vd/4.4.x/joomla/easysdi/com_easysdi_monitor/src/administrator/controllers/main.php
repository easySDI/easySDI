<?php
/**
 * @version     4.4.2
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Main controller class.
 */
class Easysdi_monitorControllerMain extends JControllerForm
{

    function __construct() {
        $this->view_list = 'mains';
        parent::__construct();
    }

}