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
 * Boundary controller class.
 */
class Easysdi_catalogControllerBoundary extends JControllerForm
{

    function __construct() {
        $this->view_list = 'boundaries';
        parent::__construct();
    }

}