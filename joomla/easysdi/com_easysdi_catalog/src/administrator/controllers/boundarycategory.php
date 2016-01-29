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
 * Boundarycategory controller class.
 */
class Easysdi_catalogControllerBoundarycategory extends JControllerForm
{

    function __construct() {
        $this->view_list = 'boundariescategory';
        parent::__construct();
    }

}