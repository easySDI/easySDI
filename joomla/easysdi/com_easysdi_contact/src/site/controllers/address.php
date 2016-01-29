<?php
/**
*** @version		4.4.0
* @package     com_easysdi_contact
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Address controller class.
 */
class Easysdi_coreControllerAddress extends JController
{

    function __construct() {
        $this->view_list = 'addresses';
        parent::__construct();
    }

}