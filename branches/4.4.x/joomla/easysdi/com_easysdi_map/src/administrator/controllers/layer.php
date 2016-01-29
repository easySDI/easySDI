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
 * Layer controller class.
 */
class Easysdi_mapControllerLayer extends JControllerForm
{

    function __construct() {
        $this->view_list = 'layers';
        parent::__construct();
    }

}