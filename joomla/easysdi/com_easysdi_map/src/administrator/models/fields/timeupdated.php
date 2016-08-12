<?php
/**
 \* @version     4.4.2
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldTimeupdated extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'timeupdated';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();


		$old_time_updated = $this->value;
		if (!$old_time_updated) {
			$html[] = '-';
		} else {
			$jdate = new JDate($old_time_updated);
			$pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
			$html[] = "<div>".$pretty_date."</div>";
		}
		$time_updated = date("Y-m-d H:i:s");
		$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$time_updated.'" />';

		return implode($html);
	}
}