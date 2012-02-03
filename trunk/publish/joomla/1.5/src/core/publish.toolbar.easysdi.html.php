<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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
class TOOLBAR_publish{

	function _EDITGLOBALSETTINGS() {
		global $mainframe;
		JToolBarHelper::title( JText::_( 'CORE_CONFIGURATION_PUBLISH_TAB_TITLE' ), 'config.png' );
		JToolBarHelper::save('saveMDTABS');
		JToolBarHelper::help(null);
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelConfig', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	function _EDIT() {
		global $mainframe;
		JToolBarHelper::title( JText::_( 'CORE_CONFIGURATION_PUBLISH_TAB_TITLE' ), 'config.png' );
		JToolBarHelper::save('saveConfig');
		JToolBarHelper::help(null);
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelConfig', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
	
	
}