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
 
class JFormFieldSupportedVersions extends JFormField {
 
	protected $type = 'supportedversions';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		
		$supportedversions = json_decode($this->form->getValue('supportedversions'));
		// Start the action field output.
		$html[]  = '<div id="div-supportedversions" class="' . (string) $this->element['class'] . ' ">';
		if(!$supportedversions){
			$html[] .= '<span class="star">*</span>';
		}
		else 
		{
			$html[] .= '<span class="star">*</span>';
			foreach($supportedversions as $supportedversion)
			{
				$html[] .= '<span class="label label-info">';
				$html[] .= $supportedversion;
				$html[] .= '</span>';
			}
		}
		$html[] .= '</div>';
		
		return implode($html);

		//if(isset($this->element['extension']))
	}
}