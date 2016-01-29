<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Property controller class.
 */
class Easysdi_shopControllerProperty extends JControllerForm
{

    function __construct() {
        $this->view_list = 'properties';
        parent::__construct();
    }

}