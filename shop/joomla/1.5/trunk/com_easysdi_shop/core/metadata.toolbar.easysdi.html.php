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
class TOOLBAR_metadata{
	

	
	
function _LISTSTANDARDCLASSES() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataStandardClasses');
		JToolBarHelper::editList('editMetadataStandardClasses');
		JToolBarHelper::deleteList('','deleteMetadataStandardClasses');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
		function _EDITSTANDARDCLASSES() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDStandardClasses');
		JToolBarHelper::cancel('cancelMDStandardClasses');						
	}
	
	
	
	

	
function _LISTSTANDARD() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataStandard');
		JToolBarHelper::editList('editMetadataStandard');
		JToolBarHelper::deleteList('','deleteMetadataStandard');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
		function _EDITSTANDARD() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDStandard');
		JToolBarHelper::cancel('cancelMDStandard');						
	}
	
	function _LISTLOCFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataLocfreetext');
		JToolBarHelper::editList('editMetadataLocfreetext');
		JToolBarHelper::deleteList('','deleteMetadataLocfreetext');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
		function _EDITLOCFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDLocfreetext');
		JToolBarHelper::cancel('cancelMDLocfreetext');						
	}
	
	
function _EDITCLASS() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDClass');
		JToolBarHelper::cancel('cancelMDClass');						
	}
	
function _LISTCLASS() {
		global $mainframe;

		
			JToolBarHelper::addNew('newMetadataClass');
		JToolBarHelper::editList('editMetadataClass');
		JToolBarHelper::deleteList('','deleteMetadataClass');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );			
	}
	
	function _EDITFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDFreetext');
		JToolBarHelper::cancel('cancelMDFreetext');						
	}
	
function _LISTEDIT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDList');
		JToolBarHelper::cancel('cancelMDList');							
	}

	
	function _LISTFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataFreetext');
		JToolBarHelper::editList('editMetadataFreetext');
		JToolBarHelper::deleteList('','deleteMetadataFreetext');
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
	
	
function _LISTLIST() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataList');
		JToolBarHelper::editList('editMetadataList');
		JToolBarHelper::deleteList('','deleteMetadataList');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	function _LISTDATE() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataDate');
		JToolBarHelper::editList('editMetadataDate');
		JToolBarHelper::deleteList('','deleteMetadataDate');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	
}
?>