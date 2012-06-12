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
 
class JFormFieldNegotiatedVersions extends JFormField {
 
	protected $type = 'negotiatedversions';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		
		// Start the action field output.
		$html[] = '<div id="div-supportedversions" class="' . (string) $this->element['class'] . '"></>';
		
		return implode($html);

		//if(isset($this->element['extension']))
	}
}