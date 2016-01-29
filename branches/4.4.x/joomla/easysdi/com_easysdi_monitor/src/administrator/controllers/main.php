<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_monitor
 * @copyright	
 * @license		
 * @author		
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