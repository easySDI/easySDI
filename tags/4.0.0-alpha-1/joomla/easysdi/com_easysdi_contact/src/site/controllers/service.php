<?php
/**
*** @version     4.0.0
* @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Service controller class.
 */
class Easysdi_coreControllerService extends JController
{

    function __construct() {
        $this->view_list = 'services';
        parent::__construct();
    }

}