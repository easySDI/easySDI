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

class TOOLBAR_properties{
	
	function _EDITPROPERTIES(){
		JToolBarHelper::save('saveProperties');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelProperties', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _LISTPROPERTIES() {
		JToolBarHelper::addNew('newProperties');
		JToolBarHelper::editList('editProperties');
		JToolBarHelper::deleteList('','deleteProperties');
		JToolBarHelper::custom( 'listPropertiesValues', 'preview.png', 'preview.png',JTEXT::_("SHOP_MENU_PROPERTIES_VALUES"), false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
	function _EDITPROPERTIESVALUES(){
		JToolBarHelper::save('savePropertiesValues');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelPropertiesValues', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _LISTPROPERTIESVALUES() {
		JToolBarHelper::addNew('newPropertiesValues');
		JToolBarHelper::editList('editPropertiesValues');
		JToolBarHelper::deleteList('','deletePropertiesValues');				
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelProperties', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
}
?>