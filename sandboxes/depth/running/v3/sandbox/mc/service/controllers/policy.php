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
 * Policy controller class.
 */
class Easysdi_serviceControllerPolicy extends JControllerForm {

	function __construct() {
		$this->view_list = 'policies';
		parent::__construct();
	}

	function add() {
		$session = JFactory::getSession();
		$virtualservice_id = $session->get('id', '', 'sdi_virtualservice');
		
		$db = JFactory::getDbo();
		$db->setQuery('
			SELECT sc.value
			FROM #__sdi_virtualservice vs
			JOIN #__sdi_sys_serviceconnector sc
			ON sc.id = vs.sys_serviceconnector_id
			WHERE vs.id = ' . $virtualservice_id . ';
		');
		$layout = $db->loadResult();
		
		$this->setRedirect('index.php?option=com_easysdi_service&view=policy&layout=' . $layout . '&virtualservice_id=' . $virtualservice_id );
	}

}