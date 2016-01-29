<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Policy controller class.
 */
class Easysdi_serviceControllerPolicy extends JControllerForm {

	function __construct() {
		$this->view_list = 'policies';
		parent::__construct();
	}
}