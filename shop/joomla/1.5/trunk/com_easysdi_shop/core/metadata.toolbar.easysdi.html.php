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

	function _EDITMETADATATAB() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDTABS');
		JToolBarHelper::cancel('cancelMDTabs');						
	}
	

function _LISTMDTABS(){
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataTab');
		JToolBarHelper::editList('editMetadataTab');
		JToolBarHelper::deleteList('','deleteMetadataTab');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );	
}
		
function _LISTSTANDARDCLASSES() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataStandardClasses');
		JToolBarHelper::editList('editMetadataStandardClasses');
		JToolBarHelper::deleteList('','deleteMetadataStandardClasses');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
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
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
		function _EDITSTANDARD() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDStandard');
		JToolBarHelper::cancel('cancelMDStandard');						
	}

	function _LISTEXT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataExt');
		JToolBarHelper::editList('editMetadataExt');
		JToolBarHelper::deleteList('','deleteMetadataExt');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
	
function _LISTNUMERICS() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataNumerics');
		JToolBarHelper::editList('editMetadataNumerics');
		JToolBarHelper::deleteList('','deleteMetadataNumerics');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
	
	function _LISTLOCFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataLocfreetext');
		JToolBarHelper::editList('editMetadataLocfreetext');
		JToolBarHelper::deleteList('','deleteMetadataLocfreetext');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
		function _EDITLOCFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDLocfreetext');
		JToolBarHelper::cancel('cancelMDLocfreetext');						
	}
	function _EDITEXT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDExt');
		JToolBarHelper::cancel('cancelMDExt');						
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
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );			
	}
	
	
	function _EDITNUMERICS() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDNumerics');
		JToolBarHelper::cancel('cancelMDNumerics');						
	}
	
	
	function _EDITFREETEXT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDFreetext');
		JToolBarHelper::cancel('cancelMDFreetext');						
	}
function _LISTEDITCONTENT() {
		global $mainframe;

		
		JToolBarHelper::save('saveMDListContent');
		JToolBarHelper::cancel('cancelMDListContent');							
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
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );		
	}
	
function _LISTLISTCONTENT() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataListContent');
		JToolBarHelper::editList('editMetadataListContent');
		JToolBarHelper::deleteList('','deleteMetadataListContent');
		JToolBarHelper::cancel('cancelMDList');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	
function _LISTLIST() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataList');
		JToolBarHelper::editList('editMetadataList');
		JToolBarHelper::deleteList('','deleteMetadataList');
		//JToolBarHelper::editList("listMetadataListContent");
		JToolBarHelper::custom("listMetadataListContent",'preview.png','preview.png',JTEXT::_("EASYSDI_MENU_LIST_CONTENT"), false);
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	function _LISTDATE() {
		global $mainframe;

		
		JToolBarHelper::addNew('newMetadataDate');
		JToolBarHelper::editList('editMetadataDate');
		JToolBarHelper::deleteList('','deleteMetadataDate');
		JToolBarHelper::spacer();		
		JToolBarHelper::custom( 'ctrlPanelMetadata', 'tool_f2.png', 'tool_f2.png', JTEXT::_("EASYSDI_MENU_CPANEL"), false );
	}
	
}
?>