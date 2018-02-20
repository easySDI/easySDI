<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldAddbutton extends JFormField {
 
	protected $type = 'addbutton';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		$html[] = '	<span class="btn btn-success btn-small" name="'.$this->name.'" id="'.$this->id.'" 
                    onclick="addXPath();"><i class="icon-white icon-plus"></i>'.$this->label.'</span>';
		
		return implode($html);
	}
}