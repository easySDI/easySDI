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


class TOOLBAR_objecttypelink {
	function _DEFAULT() {
		JToolBarHelper::title(JText::_("CATALOG_LIST_OBJECTTYPELINK"));
		
		JToolBarHelper::addNew('newObjectTypeLink');
		JToolBarHelper::editList('editObjectTypeLink');
		JToolBarHelper::deleteList('','removeObjectTypeLink');
		
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel', 'tool_f2.png', 'tool_f2.png', JTEXT::_("CATALOG_MENU_CPANEL"), false );
	}
	function _EDIT() {
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		
		if (intval($cid[0]) == 0) // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		JToolBarHelper::title(JText::_( 'CATALOG_EDIT_OBJECTTYPELINK' ).': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
		
		JToolBarHelper::save('saveObjectTypeLink');
		JToolBarHelper::cancel('cancelObjectTypeLink');
	}
}
?>