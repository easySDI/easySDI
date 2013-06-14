<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

defined('JPATH_PLATFORM') or die;

/**
 * Gives a form field for each supported EasySDI language
 */
class JFormFieldMultilingual extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Multilingual';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$required = $this->required ? ' required="required" aria-required="true"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

                $languages = JComponentHelper::getParams('com_easysdi_catalog')->get('languages');
                $html = "";
                $db = JFactory::getDbo();
                if(!is_array($languages))
                    return $html;
                
                foreach($languages as $language){
                    
                    $db->setQuery('
                            SELECT value
                            FROM #__sdi_language
                            WHERE id='.$language);
                    $db->execute();
                    $lang = $db->loadResult();
                    
                    $value = (isset($this->value[$language]))? $this->value[$language] : "";
                    
                    $html .= '<div class="control-group">';
                    $html .= '<div class="control-label">';
                    $html .= '<label id="jform_'.$this->element['name'].'-lbl" for="'. $language.'" class="hasTip" title="">'.$lang.'</label>';
                    $html .= '</div>';
                   
                    $html .= '<div class="controls">';
                    $html .= '<input type="text" name="jform['.$this->element['name'].']['.$language.']" id="'.$language.'" 
                              value="'. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . $required . '/>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                return $html;
                    
//		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
//			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . $required . '/>';
	}
}
