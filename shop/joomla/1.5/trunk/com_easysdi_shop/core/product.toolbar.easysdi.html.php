<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
		JToolBarHelper::custom('saveComponentConfig','save.png','save.png',JText::_( 'EASYSDI_SAVE' ),false);		
		JToolBarHelper::cancel('cancelComponentConfig');					
	}
	
	function _EDITPRODUCTMETADATA(){
		JToolBarHelper::save('saveProductMetadata');
		JToolBarHelper::cancel('cancelProductMetadata');
	}
	
	function _EDITPRODUCT(){
		
		JToolBarHelper::save('saveProduct');
		JToolBarHelper::cancel('cancelProduct');
	}
	
	function _LISTPRODUCT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newProduct');
		JToolBarHelper::editList('editProduct');
		JToolBarHelper::deleteList('','deleteProduct');
		//JToolBarHelper::editList('editProductMetadata');
		JToolBarHelper::custom( 'editProductMetadata', 'preview.png' ,'preview.png',JTEXT::_("EASYSDI_MENU_PRODUCT_METADATA"), false  );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'ctrlPanelShop', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	
}
?>