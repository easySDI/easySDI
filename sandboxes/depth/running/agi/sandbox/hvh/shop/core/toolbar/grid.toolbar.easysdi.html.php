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
class TOOLBAR_grid{
		
	function _EDITGRID(){

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		if (intval($cid[0]) == 0) // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'SHOP_GRID_TITLE_EDIT' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		JToolBarHelper::save('saveGrid');
		JToolBarHelper::apply('applyGrid');
		JToolBarHelper::cancel('cancelGrid');
	}
	
	function _LISTGRID() {
		JToolBarHelper::addNew('newGrid');
		JToolBarHelper::editList('editGrid');
		JToolBarHelper::deleteList('','deleteGrid');
		JToolBarHelper::custom( 'copyGrid', 'copy.png', 'copy.png', JTEXT::_("SHOP_MENU_COPY_GRID"), false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
}
?>