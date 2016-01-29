<?php
/**
* @version		4.4.0
* @package     com_easysdi_processing
* @copyright	
* @license		
* @author		
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Order controller class.
 */
class Easysdi_processingControllerOrder extends JControllerForm
{

    function __construct() {
        $this->view_list = 'orders';
        parent::__construct();
    }

}