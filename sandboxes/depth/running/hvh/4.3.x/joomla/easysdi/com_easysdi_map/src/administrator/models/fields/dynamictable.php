<?php
/**
 \* @version     4.0.0
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
class JFormFieldDynamicTable extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'DynamicTable';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
                $doc = JFactory::getDocument();
                $doc->addScript(Juri::base(true) . '/components/com_easysdi_map/models/fields/dynamictable.js');
		// Initialize variables.
		$html = array();
                $html[] = '<table class="table table-bordered table-hover" id="tab-dyn">';
                $html[] = '<thead>';
                $html[] = '<tr>';
                $html[] = '<th class="text-center">';
                $html[] = '#';
                $html[] = '</th>';
                $html[] = '<th class="text-center">';
                $html[] = JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_LEVELLABEL');
                $html[] = '</th>';
                $html[] = '<th class="text-center">';
                $html[] = JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_LEVELCODE');
                $html[] = '</th>';
                $html[] = '</tr>';
                $html[] = '</thead>';
                $html[] = '<tbody>';
                $html[] = '<tr id="level1"></tr>';
                $html[] = '</tbody>';
                $html[] = '</table>';

                $html[] = '<a id="add_row" class="btn btn-default pull-left">Add Row</a><a id="delete_row" class="pull-right btn btn-default">Delete Row</a>';
		return implode($html);
	}
}