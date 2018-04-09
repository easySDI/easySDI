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
                $doc->addScript(Juri::base(true) . '/components/com_easysdi_map/models/fields/dynamictable.js?v=' . sdiFactory::getSdiFullVersion());
		// Initialize variables.
		$html = array();
                $html[] = '<table class="table  table-striped table-hover" id="tab-dyn">';
                $html[] = '<thead>';
                $html[] = '<tr>';
                $html[] = '<th class="text-center hasTip" title="'.JText::_('COM_EASYSDI_MAP_FORM_DESC_MAP_DEFAULTLEVEL').'">';
                $html[] = JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_DEFAULTLEVEL');
                $html[] = '</th>';
                $html[] = '<th class="text-center hasTip" title="'.JText::_('COM_EASYSDI_MAP_FORM_DESC_MAP_LEVELLABEL').'" >';
                $html[] = JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_LEVELLABEL');
                $html[] = '</th>';
                $html[] = '<th class="text-center hasTip" title="'.JText::_('COM_EASYSDI_MAP_FORM_DESC_MAP_LEVELCODE').'">';
                $html[] = JText::_('COM_EASYSDI_MAP_FORM_LBL_MAP_LEVELCODE');
                $html[] = '</th>';                
                $html[] = '<th class="text-center">';
                $html[] = '</tr>';
                $html[] = '</thead>';
                $html[] = '<tbody>';
                $html[] = '<tr id="level1"></tr>';
                $html[] = '</tbody>';
                $html[] = '</table>';

                $html[] = '<a id="add_row" class="btn btn-success pull-right">Add Row</a>';
		return implode($html);
	}
}