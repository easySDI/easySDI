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

class JFormFieldSdidatabasecheckboxes extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Sdidatabasecheckboxes';
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
		$class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
	
		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
	
		// Get the field options.
		$options = $this->getOptions();
	
		
		// Build the checkbox field output.
		foreach ($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
			$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
 			$disabled = empty($option->disable) || $option->disable == "false" ? '': ' disabled="disabled"' ;
	
			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
	
			$html[] = '<div class="control-group">';
			$html[] = '<div class="control-label">'.'<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
			. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>'.'</div>';
			$html[] = '<div class="controls">'.'<label for="' . $this->id . $i . '"' . $class . '>' . JText::_($option->text) . '</label>'.'</div>';
			$html[] = '</div>';
		}
		
		// End the checkbox field output.
		$html[] = '</fieldset>';
	
		return implode($html);
	}
	

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select($query->quoteName($this->element['valuefield']).' as value');
                $query->select($query->quoteName($this->element['textfield']).' as text');
                $query->from($query->quoteName($this->element['sourcetable']));
                $query->order('ordering');
                
		$db->setQuery($query);
		$tools = $db->loadObjectList();
		
		foreach ($tools as $tool)
		{

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option',$tool->value, $tool->text, 'value', 'text','false');

			// Set some option attributes.
			//$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			//$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
