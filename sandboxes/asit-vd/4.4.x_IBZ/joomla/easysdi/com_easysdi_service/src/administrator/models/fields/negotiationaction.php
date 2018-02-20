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
 
class JFormFieldNegotiationAction extends JFormField {
 
	protected $type = 'negotiationaction';
 
	public function getInput() {
		// Initialize variables.
		$html = array();
		$html[] = '	<span class="span2 btn" name="'.$this->name.'" id="'.$this->id.'" onclick="javascript:negoVersionService();"><i class="icon-white icon-refresh"></i> Negotiation</span>';
		
		return implode($html);
	}
}