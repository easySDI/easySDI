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
class TOOLBAR_basemap{
		
	function _EDITBASEMAP(){		
		JToolBarHelper::save('saveBasemap');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelBasemap', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _LISTBASEMAP() {
		global $mainframe;
		JToolBarHelper::addNew('newBasemap');
		JToolBarHelper::editList('editBasemap');
		JToolBarHelper::deleteList('','deleteBasemap');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'listBasemapContent', 'preview.png', 'preview.png', JTEXT::_("SHOP_MENU_LISTBASEMAP_CONTENT"), false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
	function _EDITBASEMAPCONTENT(){
		JToolBarHelper::save('saveBasemapContent');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelBasemapContent', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _LISTBASEMAPCONTENT($basemap_id) {
		JToolBarHelper::addNew('newBasemapContent');
		JToolBarHelper::editList('editBasemapContent');
		JToolBarHelper::deleteList('','deleteBasemapContent');		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelBasemap', 'back.png', 'back.png', JTEXT::_("SHOP_MENU_BACK"), false );
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
}
?>