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

class TOOLBAR_overlay {

	function _DEFAULT() 
	{
		JToolBarHelper::addNew('newOverlay');
		JToolBarHelper::editList('editOverlay');
		JToolBarHelper::deleteList('','deleteOverlay');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
	function _LISTOVERLAY() 
	{
		JToolBarHelper::addNew('newOverlay');
		JToolBarHelper::editList('editOverlay');
		JToolBarHelper::deleteList('','deleteOverlay');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}

	function _EDITOVERLAY() 
	{
		JToolBarHelper::save('saveOverlay');
		JToolBarHelper::cancel('cancelOverlay');
	}

	function _LISTOVERLAYGROUP() 
	{
		JToolBarHelper::addNew('newOverlayGroup');
		JToolBarHelper::editList('editOverlayGroup');
		JToolBarHelper::deleteList('','deleteOverlayGroup');
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}

	function _EDITOVERLAYGROUP() 
	{
		JToolBarHelper::save('saveOverlayGroup');
		JToolBarHelper::cancel('cancelOverlayGroup');
	}
}
?>