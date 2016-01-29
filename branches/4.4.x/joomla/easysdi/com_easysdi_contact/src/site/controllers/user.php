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
 * User controller class.
 */
class Easysdi_coreControllerUser extends JController
{

    function __construct() {
        $this->view_list = 'users';
        parent::__construct();
    }

}