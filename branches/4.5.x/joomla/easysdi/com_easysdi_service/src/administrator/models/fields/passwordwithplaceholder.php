<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_PLATFORM') or die;

/**
 * EasySDI Extension of :
 * Form Field class for the Joomla Platform.
 * Text field for passwords
 */
class JFormFieldPasswordWithPlaceHolder extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'passwordwithplaceholder';

	/**
	 * Method to get the field input markup for password.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class		= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$auto		= ((string) $this->element['autocomplete'] == 'off') ? ' autocomplete="off"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$meter		= ((string) $this->element['strengthmeter'] == 'true');
		$threshold	= $this->element['threshold'] ? (int) $this->element['threshold'] : 66;
		
		//Placeholder extension
		$placeholder = ((string) $this->element['placeholder']) ? ' placeholder="'.JText::_($this->element['placeholder']).'"' : '';

		$script = '';
		if ($meter)
		{
			JHtml::_('script', 'system/passwordstrength.js', true, true);
			$script = '<script type="text/javascript">new Form.PasswordStrength("' . $this->id . '",
				{
					threshold: ' . $threshold . ',
					onUpdate: function(element, strength, threshold) {
						element.set("data-passwordstrength", strength);
					}
				}
			);</script>';
		}

		return '<div class="input-prepend"><span class="add-on">#</span><input type="password" name="' . $this->name . '" id="' . $this->id . '"' .
			' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
			$auto . $class . $readonly . $disabled . $size . $maxLength . $placeholder . '/></div>' . $script;
	}
}
