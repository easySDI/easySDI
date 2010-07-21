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
class TOOLBAR_location{
		
	function _EDITLOCATION(){
		JToolBarHelper::save('saveLocation');
		JToolBarHelper::cancel('cancelLocation');
	}
	
	function _LISTLOCATION() {
		JToolBarHelper::addNew('newLocation');
		JToolBarHelper::editList('editLocation');
		JToolBarHelper::deleteList('','deleteLocation');
		JToolBarHelper::custom( 'copyLocation', 'copy.png', 'copy.png', JTEXT::_("SHOP_MENU_COPY_LOCATION"), false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'config.png', 'config.png', JTEXT::_("SHOP_MENU_CPANEL"), false );
	}
}
?>