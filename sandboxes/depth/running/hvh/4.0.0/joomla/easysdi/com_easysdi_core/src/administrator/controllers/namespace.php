<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Namespace controller class.
 */
class Easysdi_coreControllerNamespace extends JControllerForm
{

    function __construct() {
        $this->view_list = 'namespaces';
        parent::__construct();
    }

}