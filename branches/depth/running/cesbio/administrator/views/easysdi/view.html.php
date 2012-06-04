<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * View 
 */
class Easysdi_coreViewEasysdi extends JView
{
	protected $form;
	
	/**
	 * Display the view
	 */
	function display($tpl = null) 
	{
		// Assign data to the view
		$this->form		= $this->get('Form');
 
		// Display the view
		parent::display($tpl);
	}
}