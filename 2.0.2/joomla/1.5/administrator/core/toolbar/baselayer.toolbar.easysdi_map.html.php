<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

class TOOLBAR_baselayer {

	function _DEFAULT() 
	{
		JToolBarHelper::addNew('newBaseLayer');
		JToolBarHelper::editList('editBaseLayer');
		JToolBarHelper::deleteList('','deleteBaseLayer');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
	function _LIST() 
	{
		JToolBarHelper::addNew('newBaseLayer');
		JToolBarHelper::editList('editBaseLayer');
		JToolBarHelper::deleteList('','deleteBaseLayer');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}

	function _EDIT() 
	{
		JToolBarHelper::save('saveBaseLayer');
		JToolBarHelper::cancel('cancelBaseLayer');
	}
	
	function _LIST_DEFINITION() 
	{
		JToolBarHelper::addNew('newBaseDefinition');
		JToolBarHelper::editList('editBaseDefinition');
		JToolBarHelper::custom( 'baseLayer', 'preview.png', 'preview.png', JTEXT::_("EASYSDI_MENU_LISTBASEMAP_CONTENT"), false );
		JToolBarHelper::deleteList('','deleteBaseDefinition');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}

	function _EDIT_DEFINITION() 
	{
		JToolBarHelper::save('saveBaseDefinition');
		JToolBarHelper::cancel('baseLayer');
	}
}
class TOOLBAR_basemap {

	function _DEFAULT() 
	{
		JToolBarHelper::save('saveBaseMap');
		JToolBarHelper::cancel('cancelBaseMap');
	}
	
	
	function _EDIT() 
	{
		JToolBarHelper::save('saveBaseMap');
		JToolBarHelper::cancel('cancelBaseMap');
	}
}
?>