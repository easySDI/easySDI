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
 * Virtualservice controller class.
 */
class Easysdi_serviceControllerVirtualservice extends JControllerForm {
	function __construct() {
		$this->view_list = 'virtualservices';
		parent::__construct();
	}
}