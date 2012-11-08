<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldServiceselector extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'text';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT id, alias,serviceconnector_id FROM #__sdi_physicalservice WHERE state=1');
		$physicals = $db->loadObjectList();
		$db->setQuery('SELECT id, alias FROM #__sdi_virtualservice WHERE state=1');
		$virtuals = $db->loadObjectList();
		
		$text = '<select id="jform_'.$this->name.'" name="jform['.$this->name.']" onchange="javascript:getLayers(this)" >
						<optgroup label="Physicals">';
		foreach ($physicals as $physical)
		{
			$text .= '<option value="physical_'.$physical->id.'">'.$physical->alias.'</option>';
		}
		$text .= '</optgroup>
						<optgroup label="Virtuals">';
		foreach ($virtuals as $virtual)
		{
			$text .= '<option value="virtual_'.$virtual->id.'">'.$virtual->alias.'</option>';
		}
		$text .= '</optgroup>
					</select>';
		
		foreach ($physicals as $physical)
		{
			if($physical->serviceconnector_id == 12 || $physical->serviceconnector_id == 13 || $physical->serviceconnector_id == 14)
			{
				$db->setQuery('SELECT name FROM #__sdi_layer WHERE physicalservice_id='.$physical->id);
				$layers = $db->loadResultArray();
				$text .= '<input type="hidden" name="physical_'.$physical->id.'" id="physical_'.$physical->id.'" value="'.htmlentities(json_encode($layers)).'" />';
			}
		}
		
		$html = array();
		$html[] = $text;

		return implode($html);
	}
}