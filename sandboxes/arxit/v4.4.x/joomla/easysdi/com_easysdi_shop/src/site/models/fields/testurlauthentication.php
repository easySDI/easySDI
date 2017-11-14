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
 
class JFormFieldTestUrlAuthentication extends JFormField {
 
	protected $type = 'testurlauthentication';
 
	public function getInput() {
		// Initialize variables.
		return "<button class='span2 btn' name='{$this->name}' id='{$this->id}'>"
                . JText::_('COM_EASYSDI_SHOP_FORM_BTN_TXT_DIFFUSION_TESTURLAUTHENTICATION')
                . "</button>";
	}
}