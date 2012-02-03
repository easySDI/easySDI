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
 
                //JToolBarHelper::spacer();
		//JToolBarHelper::custom( 'cancelProperties', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
		//JToolBarHelper::custom( 'ctrlPanelShop', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_MENU_CPANEL"), false );

defined('_JEXEC') or die('Restricted access');
class TOOLBAR_proxy{
	
	function editComponentConfig(){
		JToolBarHelper::save('saveComponentConfig',JText::_( 'EASYSDI_SAVE' ));	
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelComponentConfig', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function editConfig(){
		
		/*if (JRequest::getVar("configId"))
			$configId = JRequest::getVar("configId");
		else
			$configId = "New Config";
		
		JToolBarHelper::title( JText::_( 'EASYSDI_EDIT_CONFIG' ).' : '.$configId, 'edit.png' );
		*/
		global $mainframe;
		$task = $_POST['task'];
		
		if ($task == "addConfig") // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'EASYSDI_EDIT_CONFIG' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		$serviceType = JRequest::getVar('serviceType');
		if($serviceType != 'CSW')
			JToolBarHelper::addNew('addNewServer',JText::_( 'EASYSDI_ADD NEW SERVER'));
		JToolBarHelper::save('saveConfig',JText::_( 'EASYSDI_SAVE' ));	
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelConfig', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function editPolicy(){	
		//JToolBarHelper::title( JText::_( 'EASYSDI_EDIT_POLICY' ), 'edit.png' );

		global $mainframe;
		$task = $_POST['task'];
		
		if ($task == "addPolicy") // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'EASYSDI_EDIT_POLICY' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		JToolBarHelper::save('savePolicy',JText::_( 'EASYSDI_SAVE POLICY' ));
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelPolicy', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function editPolicyList(){
		$configId = JRequest::getVar("configId");
		JToolBarHelper::title( JText::_( 'EASYSDI_POLICIES_LIST' ).': <small><small>[ '. $configId.' ]</small></small>', 'edit.png' );
		
		JToolBarHelper::editList('editPolicy',JText::_( 'EASYSDI_EDIT POLICY' ));
		JToolBarHelper::addNew('addPolicy',JText::_( 'EASYSDI_CREATE A POLICY' ));
		JToolBarHelper::custom('copyPolicy','copy.png','copy.png',JText::_( 'EASYSDI_COPY A POLICY' ),true);
		//JToolBarHelper::deleteList('deletePolicy',JText::_( 'EASYSDI_DELETE POLICY' ));
		JToolBarHelper::deleteList( JText::_( 'CORE_DELETE_MSG'), 'deletePolicy', JText::_( 'EASYSDI_DELETE POLICY'));		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelConfig', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	
	function _NEW(){
		JToolBarHelper::save();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();		
		JToolBarHelper::deleteList();
	}
	
	function configList(){
		
		JToolBarHelper::editList('editConfig',JText::_( 'EASYSDI_EDIT CONFIG' ));

		JToolBarHelper::addNew('addConfig',JText::_( 'EASYSDI_NEW CONFIG' ));
		JToolBarHelper::deleteList( JText::_( 'CORE_DELETE_MSG'), 'deleteConfig', JText::_( 'EASYSDI_DELETE CONFIG'));		
		//JToolBarHelper::custom('deleteConfig','delete.png','delete.png',JText::_( 'EASYSDI_DELETE CONFIG' ),true, true);
		JToolBarHelper::editList('editPolicyList',JText::_( 'EASYSDI_POLICIES LIST' ));
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	
}
?>