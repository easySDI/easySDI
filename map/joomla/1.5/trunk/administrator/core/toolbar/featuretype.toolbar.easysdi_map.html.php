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

class TOOLBAR_featuretype {

	function _DEFAULT() 
	{
		JToolBarHelper::addNew('newFeatureType');
		JToolBarHelper::editList('editFeatureType');
		JToolBarHelper::deleteList('','deleteFeatureType');
		JToolBarHelper::custom( 'ctrlPanel', 'back.png', 'back.png', JTEXT::_("MAP_MENU_BACK"), false );
	}
	
	function _LIST() 
	{
		JToolBarHelper::addNew('newFeatureType');
		JToolBarHelper::editList('editFeatureType');
		JToolBarHelper::deleteList('','deleteFeatureType');
		JToolBarHelper::custom( 'ctrlPanel', 'back.png', 'back.png', JTEXT::_("MAP_MENU_BACK"), false );
	}

	function _EDIT() 
	{
		JToolBarHelper::save('saveFeatureType');
		JToolBarHelper::cancel('featureType');
	}

}
?>