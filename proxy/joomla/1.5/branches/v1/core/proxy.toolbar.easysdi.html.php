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
class TOOLBAR_proxy{
	
	function editComponentConfig(){
		JToolBarHelper::custom('saveComponentConfig','save.png','save.png',JText::_( 'EASYSDI_SAVE' ),false);		
		JToolBarHelper::cancel('cancelComponentConfig');					
	}
	
	function editConfig(){
		$serviceType = JRequest::getVar('serviceType');
		if($serviceType != 'CSW')
			JToolBarHelper::custom('addNewServer','new.png','new.png',JText::_( 'EASYSDI_ADD NEW SERVER'),false);
		JToolBarHelper::custom('saveConfig','save.png','save.png',JText::_( 'EASYSDI_SAVE' ),false);		
		JToolBarHelper::cancel();					
	}
	
function editPolicy(){	
		
		JToolBarHelper::custom('savePolicy','save.png','save.png',JText::_( 'EASYSDI_SAVE POLICY' ),false);
		JToolBarHelper::cancel('cancelPolicy');
	}
	
	function editPolicyList(){
		JToolBarHelper::custom('editPolicy','edit.png','edit.png',JText::_( 'EASYSDI_EDIT POLICY' ),false);
		JToolBarHelper::custom('addPolicy','new.png','new.png',JText::_( 'EASYSDI_CREATE A POLICY' ),false);
		JToolBarHelper::custom('copyPolicy','copy.png','copy.png',JText::_( 'EASYSDI_COPY A POLICY' ),false);
		JToolBarHelper::custom('deletePolicy','delete.png','delete.png',JText::_( 'EASYSDI_DELETE POLICY' ),false);		
		//JToolBarHelper::cancel();
		JToolBarHelper::custom( 'cancel', 'back.png', 'back.png', JTEXT::_("EASYSDI_MENU_BACK"), false );
	}
	
	
	function _NEW(){
				JToolBarHelper::save();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();		
		JToolBarHelper::deleteList();
	}
	
	function configList(){
		
		JToolBarHelper::custom('editConfig','edit.png','edit.png',JText::_( 'EASYSDI_EDIT CONFIG' ),false);

		JToolBarHelper::custom('addConfig','new.png','new.png',JText::_( 'EASYSDI_NEW CONFIG' ),false);
		JToolBarHelper::custom('deleteConfig','delete.png','delete.png',JText::_( 'EASYSDI_DELETE CONFIG' ),false);
		JToolBarHelper::custom('editPolicyList','edit.png','edit.png',JText::_( 'EASYSDI_POLICIES LIST' ),false);
				
		//JToolBarHelper::cancel('cancelConfigList');		
		JToolBarHelper::custom( 'cpanel', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	
}
?>