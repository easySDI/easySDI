<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_map
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Map controller class.
 */
class Easysdi_mapControllerMap extends JControllerForm
{

    function __construct() {
        $this->view_list = 'maps';
        parent::__construct();
    }

}