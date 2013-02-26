<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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

	function add() {
		$serviceconnector = JRequest::getVar('serviceconnector',null);
		
		if(isset($serviceconnector)) {
			if($serviceconnector == "WMSC")
    			$layout = "WMS";
    		else
    			$layout = $serviceconnector;
			$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=add&layout='.$serviceconnector.'&mco_debug=1');
		}
		else {
			$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=add&layout=add'.'&mco_debug=2');
		}
	}

	function edit() {
		$params 		= JComponentHelper::getParams('com_easysdi_service');
		$cid 				= JRequest::getVar('cid',array(0));
		$layout 		= JRequest::getVar('serviceconnector',null);
		if (!isset($layout)) {
			foreach ($cid as $id ){
				$layout = "wms";
			}
		}
		
		$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=edit&id='.$cid[0].'&layout='.$layout.'&mco_debug=3');
	}
}