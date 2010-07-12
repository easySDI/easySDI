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


class TOOLBAR_objectversion {
	function _EDIT(){
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		
		if (intval($cid[0]) == 0) // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'CATALOG_EDIT_OBJECTVERSION' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		JToolBarHelper::save('saveObjectVersion');
		JToolBarHelper::apply('applyObjectVersion');
		JToolBarHelper::cancel('cancelObjectVersion');
		if (intval($cid[0]) <> 0) // Edit
		{
			JToolBarHelper::spacer();
			JToolBarHelper::custom('editMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		}
	}
	
	function _DEFAULT() {
		global $mainframe;
		
		$database=& JFactory::getDBO();
		if (array_key_exists('object_id', $_GET))
			$object_id = $_GET['object_id'];
		else
			$object_id = JRequest::getVar ('object_id', array(0) );
		 
		$object = new object($database);
		$object->load($object_id);
		$object_name = "\"".$object->name."\"";
		
		JToolBarHelper::title(JText::_("CATALOG_OBJECTVERSION_TITLE")." ".$object_name); 
		
		JToolBarHelper::addNew('newObjectVersion');
		JToolBarHelper::editList('editObjectVersion');
		JToolBarHelper::custom('historyAssignMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CATALOG_HISTORYASSIGN_METADATA"), false );
		JToolBarHelper::custom('archiveObjectVersion', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CATALOG_ARCHIVE_METADATA"), false );
		JToolBarHelper::deleteList('','deleteObjectVersion');
		JToolBarHelper::spacer();
		//JToolBarHelper::custom('askForEditMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		JToolBarHelper::custom('editMetadata', 'preview.png', 'preview.png', JTEXT::_("CORE_OBJECT_MENU_EDITMETADATA"), false );
		JToolBarHelper::custom('viewObjectVersionLink', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CORE_OBJECT_MENU_VIEWOBJECTVERSIONLINK"), false );
		JToolBarHelper::custom('manageObjectVersionLink', 'edit.png', 'edit.png', JTEXT::_("CORE_OBJECT_MENU_MANAGEOBJECTVERSIONLINK"), false );
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'backObjectVersion', 'back.png', 'back.png', JTEXT::_("CATALOG_MENU_BACK"), false );
	}
	
	function _NEW() {
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		
		$database=& JFactory::getDBO();
		$object_id = $cid[0]; 
		$object = new object($database);
		$object->load($object_id);
		$object_name = "\"".$object->name."\"";
		
		JToolBarHelper::title(JText::_( 'CATALOG_NEW_OBJECTVERSION' )." ".$object_name.': <small><small>[ '.JText::_("CORE_NEW").' ]</small></small>', 'addedit.png');
		
		JToolBarHelper::save('saveObjectVersion');
		JToolBarHelper::cancel('cancelObjectVersion');
	}
	
	function _VIEW() {
		global $mainframe;
		$database=& JFactory::getDBO();
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$objectversion_id = $cid[0];
		$objectversion = new objectversion($database);
		$objectversion->load($objectversion_id);
		
		$objectversion_name = "\"".$objectversion->name."\"";
		JToolBarHelper::title(JText::_("CATALOG_VIEW_OBJECTVERSIONLINK")." ".$objectversion_name);
		
		JToolBarHelper::custom( 'backObjectVersionLink', 'back.png', 'back.png', JTEXT::_("CATALOG_MENU_BACK"), false );
	}
	function _MANAGE() {
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		
		$database=& JFactory::getDBO();
		$objectversion_id = $cid[0];
		$objectversion = new objectversion($database);
		$objectversion->load($objectversion_id);
		$objectversion_name = "\"".$objectversion->name."\"";
		JToolBarHelper::title(JText::_("CATALOG_MANAGE_OBJECTVERSIONLINK")." ".$object_name);
		
		//JToolBarHelper::save('saveObjectLink');
		JToolBarHelper::custom( 'backObjectVersionLink', 'back.png', 'back.png', JTEXT::_("CATALOG_MENU_BACK"), false );
	}
}
?>