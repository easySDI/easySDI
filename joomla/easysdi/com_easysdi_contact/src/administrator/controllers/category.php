<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_contact
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Organism controller class.
 */
class Easysdi_contactControllerCategory extends JControllerForm
{

    function __construct() {
        $this->view_list = 'categories';
        parent::__construct();
    }

}