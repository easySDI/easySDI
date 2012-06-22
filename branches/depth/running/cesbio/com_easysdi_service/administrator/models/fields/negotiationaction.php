<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldNegotiationAction extends JFormField {
 
	protected $type = 'negotiationaction';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		
		// Start the action field output.
		$html[] = '<a href="#" onclick="javascript:negoVersionService();" >
									<img class="helpTemplate" src="../templates/system/images/j_button2_image.png" alt=""/>
								</a>';
		
		return implode($html);

		//if(isset($this->element['extension']))
	}
}