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
class TOOLBAR_product{
	
	function editComponentConfig(){
		JToolBarHelper::custom('saveComponentConfig','save.png','save.png',JText::_( 'SHOP_SAVE' ),false);
                JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelComponentConfig', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );		
	}
	
	function _EDITPRODUCTMETADATA(){
		JToolBarHelper::save('saveProductMetadata');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('cancelProductMetadata');
	}
	
	function _EDITPRODUCT(){
		JToolBarHelper::save('saveProduct');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelProduct', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _LISTPRODUCT() {
		JToolBarHelper::addNew('newProduct');
		JToolBarHelper::editList('editProduct');
		JToolBarHelper::deleteList('','deleteProduct');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
}
?>