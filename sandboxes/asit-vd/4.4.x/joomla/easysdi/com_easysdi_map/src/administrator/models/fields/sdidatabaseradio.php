<?php
/**
 * Form Field class for the Joomla Platform.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 * 
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldSdidatabaseradio extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Sdidatabaseradio';
	
	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected $forceMultiple = true;
	
	
	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		
		// Initialize some field attributes.
		$classfs = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio"';
	
		$values = $this->getValues();
		
		// Get the field options.
		$options = $this->getOptions();
	
		foreach ($values as $v => $value)
		{
			// Start the radio field output.
			$html[] = '<div class="control-group">';
			$html[] = '<div class="control-label">';
			$html[] = '<label id="'.$value->value.'" class="hasTip" title="" for="jform_tools">'.$value->text.'</label>';
			$html[] = '</div>';
			$html[] = '<div class="controls">';
			$html[] = '<fieldset id="' . $this->id . $value->value . '"' . $classfs . '>';
			
			// Build the radio field output.
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
				$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
	
				// Initialize some JavaScript option attributes.
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
				
				$html[] = '<input type="radio" id="' . $this->id . $value->value . $i . '" name="' . $this->name . '"' . ' value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';
	
				$html[] = '<label for="' . $this->id . $value->value . $i . '"' . $class . '>'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $value->value)) . '</label>';
				
			}
			
			// End the radio field output.
			$html[] = '</div>';
			$html[] = '</fieldset>';
			$html[] = '</div>';
		}
	
		return implode($html);
	}
	

	/**
	 */
	protected function getValues()
	{
		// Initialize variables.
		$tools = array();

		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select($query->quoteName($this->element['valuefield']).' as value');
                $query->select($query->quoteName($this->element['textfield']).' as text');
                $query->from($query->quoteName($this->element['sourcetable']));
                $query->order('ordering');
                
		$db->setQuery($query);
		$tools = $db->loadObjectList();
		
		return $tools;
	}
	
	/**
	 * Method to get the field options for radio buttons.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
	
		foreach ($this->element->children() as $option)
		{
	
			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}
	
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
					'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
					((string) $option['disabled'] == 'true')
			);
	
			// Set some option attributes.
			$tmp->class = (string) $option['class'];
	
			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
	
			// Add the option object to the result set.
			$options[] = $tmp;
		}
	
		reset($options);
	
		return $options;
	}
}
