<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/helpers/curl.php';

/**
 * Resource controller class.
 */
class Easysdi_coreControllerProxy extends Easysdi_coreController{
    
    private $curlHelper;
    private $inputs;
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->curlHelper = new CurlHelper();
        $this->inputs = JFactory::getApplication()->input;
    }
    
    public function run(){
        $this->curlHelper->run($this->inputs);
    }
    
    public function getRequest(){
        $this->curlHelper->get($this->inputs);
    }
    
    public function head(){
        $this->curlHelper->head($this->inputs);
    }
    
    public function post(){
        $this->curlHelper->post($this->inputs);
    }
    
    public function put(){
        $this->curlHelper->put($this->inputs);
    }
    
    public function delete(){
        $this->curlHelper->delete($this->inputs);
    }
}