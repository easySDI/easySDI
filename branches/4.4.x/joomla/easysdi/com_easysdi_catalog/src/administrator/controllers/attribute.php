<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Attribute controller class.
 */
class Easysdi_catalogControllerAttribute extends JControllerForm
{

    function __construct() {
        $this->view_list = 'attributes';
        parent::__construct();
    }

}