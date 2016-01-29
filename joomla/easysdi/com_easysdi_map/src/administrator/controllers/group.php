<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_map
 * @copyright	
 * @license		
 * @author		
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Group controller class.
 */
class Easysdi_mapControllerGroup extends JControllerForm
{

	function __construct() {
		$this->view_list = 'groups';
		parent::__construct();
	}

}