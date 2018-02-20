<?php
/**
 \* @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
		
                if($this->servicetype=='wfs'){
                    $connectors= '4';
                }
                elseif ($this->servicetype=='wms'){
                    $connectors= '2';
                }
                else{
                    $connectors= '2,3,11,12,13,14';
                }
                        
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('id, name,serviceconnector_id');
                $query->from('#__sdi_physicalservice');
                $query->where('state=1');
                $query->where('serviceconnector_id IN ('.$connectors.')');
                
		$db->setQuery($query);
		$physicals = $db->loadObjectList();
                
                $query = $db->getQuery(true);
                $query->select('id, name,serviceconnector_id');
                $query->from('#__sdi_virtualservice');
                $query->where('state=1');
                $query->where('serviceconnector_id IN ('.$connectors.')');
		$db->setQuery($query);
		$virtuals = $db->loadObjectList();
		
		//Javascript Chosen library :
		// "on single selects, the first element is assumed to be selected by the browser. 
		// To take advantage of the default text support, you will need to include a blank option as the first element of your select list."
		//In case of multiple selection, adding this blank option leads to a form validation error. So, we don't add it.
		if(!$this->multiple)
		{
			$groups[''] = array();
			$tmp = JHtml::_('select.option', null,	'', 'value', 'text');
			$groups[''][] = $tmp;
		}
		$groups['Physical'] = array();
		foreach ($physicals as $physical)
		{
			$tmp = JHtml::_('select.option', "physical_".$physical->id,	$physical->name, 'value', 'text');
			$this->serviceconnector [] = json_encode(array("physical_".$physical->id, $physical->serviceconnector_id));
			// Add the option.
			$groups['Physical'][] = $tmp;
		}
		
		$groups['Virtual'] = array();
		foreach ($virtuals as $virtual)
		{
			$tmp = JHtml::_('select.option', "virtual_".$virtual->id,	$virtual->name, 'value', 'text');
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
                $query = $db->getQuery(true);
                $query->select('id, name,serviceconnector_id');
                $query->from('#__sdi_physicalservice');
                $query->where('state=1');
                $query->where('serviceconnector_id IN (2,3,11,12,13,14)');
                        
		$db->setQuery($query);
		$physicals = $db->loadObjectList();
		foreach ($physicals as $physical)
		{
			if($physical->serviceconnector_id == 12 || $physical->serviceconnector_id == 13 || $physical->serviceconnector_id == 14)
			{
				$query = $db->getQuery(true);
                                $query->select('name');
                                $query->from('#__sdi_layer');
                                $query->where(' physicalservice_id=' . (int)$physical->id);
                                
				$db->setQuery($query);
				$layers = $db->loadColumn();				
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
		$attr .= "data-placeholder='".JText::_('JGLOBAL_SELECT_SOME_OPTIONS')."'";

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
