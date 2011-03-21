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
		JToolBarHelper::addNew('newOverlayContent');
		JToolBarHelper::editList('editOverlayContent');
		JToolBarHelper::deleteList('','deleteOverlayContent');
		JToolBarHelper::custom( 'overlayCtrlPanel', 'back.png', 'back.png', JTEXT::_("MAP_MENU_BACK"), false );
	}
	
	function _LISTOVERLAYCONTENT() 
	{
		JToolBarHelper::addNew('newOverlayContent');
		JToolBarHelper::editList('editOverlayContent');
		JToolBarHelper::deleteList('','deleteOverlayContent');
		JToolBarHelper::custom( 'overlayCtrlPanel', 'back.png', 'back.png', JTEXT::_("MAP_MENU_BACK"), false );
	}

	function _EDITOVERLAYCONTENT() 
	{
		JToolBarHelper::save('saveOverlayContent');
		JToolBarHelper::cancel('overlayContent');
	}

	function _LISTOVERLAYGROUP() 
	{
		JToolBarHelper::addNew('newOverlayGroup');
		JToolBarHelper::editList('editOverlayGroup');
		JToolBarHelper::deleteList('','deleteOverlayGroup');
		JToolBarHelper::custom( 'overlayCtrlPanel', 'back.png', 'back.png', JTEXT::_("MAP_MENU_BACK"), false );
	}

	function _EDITOVERLAYGROUP() 
	{
		JToolBarHelper::save('saveOverlayGroup');
		JToolBarHelper::cancel('overlayGroup');
	}
}
?>