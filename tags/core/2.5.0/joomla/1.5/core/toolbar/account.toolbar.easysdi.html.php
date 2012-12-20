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

class TOOLBAR_account {

	function _DEFAULT() {
		JToolBarHelper::title(JText::_("CORE_ACCOUNT_TITLE"));
		
		global $mainframe;

		$option = JRequest::getVar('option');
		
		switch ($mainframe->getUserStateFromRequest( "type{$option}", 'type', '' )) {
			case '':
				JToolBarHelper::addNew('newRootAccount');
				JToolBarHelper::editList('editRootAccount');
				break;
			default:
				JToolBarHelper::addNew('newAffiliateAccount');
				JToolBarHelper::editList('editAffiliateAccount');
				break;
		}
		JToolBarHelper::deleteList('','removeAccount');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cpanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
		
	}

	function _EDIT() {
		global $mainframe;
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$option = JRequest::getVar('option');

		if (intval($cid[0]) == 0) // New
			$text = JText::_("CORE_NEW");
		else // Edit
			$text = JText::_("CORE_EDIT");
		
		switch ($mainframe->getUserStateFromRequest( "type{$option}", 'type', '' )) {
				case '':
					JToolBarHelper::title( JText::_("CORE_ROOTACCOUNT_TITLE_EDIT").': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
					break;
				default:
					JToolBarHelper::title(JText::_("CORE_AFFILIATEACCOUNT_TITLE_EDIT").': <small><small>[ '. $text.' ]</small></small>', 'addedit.png');
					break;
			}
		
		JToolBarHelper::save('saveAccount');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelAccount', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}

}

?>
