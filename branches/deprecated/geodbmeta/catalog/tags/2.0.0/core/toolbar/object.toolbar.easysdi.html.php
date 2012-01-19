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
class TOOLBAR_object{
	
	function editComponentConfig(){
		JToolBarHelper::custom('saveComponentConfig','save.png','save.png',JText::_( 'CORE_SAVE' ),false);
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelComponentConfig', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	function _EDIT(){
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		
		if (intval($cid[0]) == 0) // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'CORE_OBJECT_TITLE_EDIT' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		JToolBarHelper::save('saveObject');
		JToolBarHelper::apply('applyObject');
		JToolBarHelper::spacer();
                JToolBarHelper::custom( 'cancelObject', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
		/*if (intval($cid[0]) <> 0) // Edit
		{
			JToolBarHelper::spacer();
			JToolBarHelper::custom('editMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		}*/
	}
	
	function _DEFAULT() {
		global $mainframe;
		
		JToolBarHelper::title(JText::_("CORE_OBJECT_TITLE")); 
		
		JToolBarHelper::addNew('newObject');
		JToolBarHelper::editList('editObject');
		//JToolBarHelper::custom('historyAssignMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CATALOG_HISTORYASSIGN_METADATA"), false );
		//JToolBarHelper::custom('archiveObject', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CATALOG_ARCHIVE_METADATA"), false );
		//JToolBarHelper::custom('versionaliseObject', 'copy.png', 'copy.png', JTEXT::_("CATALOG_VERSIONALISE_METADATA"), false );
		JToolBarHelper::deleteList( JText::_( 'CATALOG_OBJECT_DELETE_CONFIRM_MSG'), 'deleteObject', JText::_( 'DELETE'));		
		JToolBarHelper::spacer();
		JToolBarHelper::custom('askForEditMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		//JToolBarHelper::custom('editMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		//JToolBarHelper::custom('viewObjectLink', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_OBJECT_MENU_VIEWOBJECTTYPELINK"), false );
		//JToolBarHelper::custom('manageObjectLink', 'edit.png', 'edit.png', JTEXT::_("CORE_OBJECT_MENU_MANAGEOBJECTTYPELINK"), false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
}
?>