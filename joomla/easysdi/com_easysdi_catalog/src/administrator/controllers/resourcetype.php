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
 * Resourcetype controller class.
 */
class Easysdi_catalogControllerResourcetype extends JControllerForm
{

    function __construct() {
        $this->view_list = 'resourcestype';
        parent::__construct();
    }

}