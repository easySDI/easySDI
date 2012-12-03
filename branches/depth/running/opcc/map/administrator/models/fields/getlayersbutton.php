<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldGetlayersbutton extends JFormField {
 
	protected $type = 'getlayersbutton';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		
		// Start the action field output.
		$html[] = '<a href="#" onclick="javascript:getLayers(document.getElementById(\'jform_service_id\'));" >
						<img class="helpTemplate" src="templates/bluestork/images/menu/icon-16-maintenance.png" alt=""/>
					</a>';
		
		return implode($html);
	}
}