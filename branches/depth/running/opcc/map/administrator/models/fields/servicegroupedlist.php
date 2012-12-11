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
 * Form Field class for the EasySDI solution.
 * Provides a grouped list select field for services.
 *
 * @package     EasySDI
 * @subpackage  EasySDI Map
 * @since       3.0.0
 */
class JFormFieldServicegroupedList extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'ServicegroupedList';
	
	protected $serviceconnector = array();

	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   11.1
	 */
	protected function getGroups()
	{
		// Initialize variables.
		$groups = array();
		$label = 0;
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT id, alias,serviceconnector_id FROM #__sdi_physicalservice WHERE state=1 AND serviceconnector_id IN (2,3,11,12,13,14)');
		$physicals = $db->loadObjectList();
		$db->setQuery('SELECT id, alias,serviceconnector_id FROM #__sdi_virtualservice WHERE state=1 AND serviceconnector_id IN (2,3,11,12,13,14)');
		$virtuals = $db->loadObjectList();
		
		$groups['Physical'] = array();
		foreach ($physicals as $physical)
		{
			$tmp = JHtml::_('select.option', "physical_".$physical->id,	$physical->alias, 'value', 'text');
			$this->serviceconnector [] = json_encode(array("physical_".$physical->id, $physical->serviceconnector_id));
			// Add the option.
			$groups['Physical'][] = $tmp;
		}
		
		$groups['Virtual'] = array();
		foreach ($virtuals as $virtual)
		{
			$tmp = JHtml::_('select.option', "virtual_".$virtual->id,	$virtual->alias, 'value', 'text');
			$this->serviceconnector [] = json_encode(array("virtual_".$virtual->id, $virtual->serviceconnector_id));
			// Add the option.
			$groups['Virtual'][] = $tmp;
		}
		reset($groups);

		return $groups;
	}
	
	protected function getHiddenLayersNames ()
	{
		$text = '';
		$db = JFactory::getDbo();
		$db->setQuery('SELECT id, alias,serviceconnector_id FROM #__sdi_physicalservice WHERE state=1 AND serviceconnector_id IN (2,3,11,12,13,14)');
		$physicals = $db->loadObjectList();
		foreach ($physicals as $physical)
		{
			if($physical->serviceconnector_id == 12 || $physical->serviceconnector_id == 13 || $physical->serviceconnector_id == 14)
			{
				$db->setQuery('SELECT name FROM #__sdi_layer WHERE physicalservice_id='.$physical->id);
				$layers = $db->loadResultArray();
				$text .= '<input type="hidden" name="physical_'.$physical->id.'" id="physical_'.$physical->id.'" value="'.htmlentities(json_encode($layers)).'" />';
			}
		}
		
		return $text;
	}

	/**
	 * Method to get the field input markup fora grouped list.
	 * Multiselect is enabled by using the multiple attribute.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		if($this->element['onchange'])
		{
			$attr .=  ' onchange="'.$this->element['onchange'].'"' ;
		}
		// Get the field groups.
		$groups = (array) $this->getGroups();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_(
				'select.groupedlist', $groups, null,
				array(
					'list.attr' => $attr, 'id' => $this->id, 'list.select' => $this->value, 'group.items' => null, 'option.key.toHtml' => false,
					'option.text.toHtml' => false
				)
			);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_(
				'select.groupedlist', $groups, $this->name,
				array(
					'list.attr' => $attr, 'id' => $this->id, 'list.select' => $this->value, 'group.items' => null, 'option.key.toHtml' => false,
					'option.text.toHtml' => false
				)
			);
			$html[] = '<input type="hidden" name="serviceconnectorlist"  id="serviceconnectorlist" value="' .htmlentities(json_encode($this->serviceconnector)). '"/>';
		}

		$html[] = $this->getHiddenLayersNames();
		return implode($html);
	}
}
