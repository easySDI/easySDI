<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
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