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
 * Namespace controller class.
 */
class Easysdi_catalogControllerNamespace extends JControllerForm
{

    function __construct() {
        $this->view_list = 'namespaces';
        parent::__construct();
    }

}