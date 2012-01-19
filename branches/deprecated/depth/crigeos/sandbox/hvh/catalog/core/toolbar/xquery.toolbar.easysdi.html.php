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


class TOOLBAR_XQuery {
	function _DEFAULT() {
		JToolBarHelper::title(JText::_("CATALOG_LIST_XQUERY"));

		JToolBarHelper :: custom( 'newXQueryReport', 'new.png', 'new.png', JTEXT::_("CATALOG_XQUERY_NEW"),  false );
		JToolBarHelper :: custom( 'editXQueryReport', 'edit.png', 'edit.png', JTEXT::_("CATALOG_XQUERY_EDIT"),  false );
		JToolBarHelper :: custom( 'assignXQueryReport', 'adduser.png', 'adduser.png', JTEXT::_("CATALOG_XQUERY_MANAGE_USERS"),  false );
		JToolBarHelper :: custom( 'deleteXQueryReport', 'delete.png', 'delete.png', JTEXT::_("CATALOG_XQUERY_DELETE"),  false );	
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'adminPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
	}
	function _EDIT() {
		global $mainframe;
		$cid = JRequest::getVar( 'cid' );
	
		if ($cid == 0) // New
			$text = JText::_("CATALOG_XQUERY_NEW");
		else // Edit
			$text = JText::_("CATALOG_XQUERY_EDIT");
		JToolBarHelper::title(JText::_( 'CATALOG_XQUERY_TITLE' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		JToolBarHelper ::custom( 'saveXQueryReport', 'new.png', 'new.png', JTEXT::_("CATALOG_XQUERY_SAVE"),  false );
		
		JToolBarHelper::custom( 'cancelXQueryReport', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _MANAGEUSERS() {
		global $mainframe;
		$cid = JRequest::getVar('cid');	
		$task = JRequest::getVar( 'task' );
		
		if($task =="assignXQueryReport"){
			
			$text = JText::_("CATALOG_XQUERY_MANAGE_USERS");
				
			JToolBarHelper::title(JText::_( 'CATALOG_XQUERY_TITLE' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');

			JToolBarHelper ::custom( 'saveXQueryUserReportAccess', 'new.png', 'new.png', JTEXT::_("CATALOG_XQUERY_SAVEUSERS"),  false );

			JToolBarHelper::custom( 'cancelXQueryReport', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
		}

	}
}
?>