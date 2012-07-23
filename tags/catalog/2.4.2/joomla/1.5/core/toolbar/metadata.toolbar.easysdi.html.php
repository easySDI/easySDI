<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�"Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

	function _EDIT() {
		global $mainframe;
		$database =& JFactory::getDBO();

		$object_id = JRequest::getVar( 'object_id', 0);
		$version_hidden = JRequest::getVar('version_hidden', 0);
		if ($version_hidden <> 0)
		{
			$rowObjectVersion = new objectversion( $database );
			$rowObjectVersion->load( $version_hidden );
			
			$rowObject = new object( $database );
			$rowObject->load( $rowObjectVersion->object_id );
		}
		else
		{
			if ($object_id == 0) // Appel de l'�dition depuis l'�cran de gestion des objets
			{
				$cid = JRequest::getVar( 'cid');
				$object_id = $cid[0];
			}
			$version_id = JRequest::getVar('version_id', 0);
			
			$rowObject = new object( $database );
			$rowObject->load( $object_id );
			
			if (!$version_id)
			{
				$database->setQuery( "SELECT id FROM #__sdi_objectversion WHERE object_id=".$object_id." ORDER BY name" );
				$version_id= $database->loadResult();
			}
			
			$rowObjectVersion = new objectversion( $database );
			$rowObjectVersion->load( $version_id );
		}
		
		//echo $cid[0]."<br>".$object_id."<br>".$version_id;
		
		JToolBarHelper::title(JText::sprintf("CATALOG_EDIT_METADATA", $rowObject->name, $rowObjectVersion->title));
		
		//JToolBarHelper::save('saveMetadata');
		//JToolBarHelper::custom( 'saveMetadata', 'tool_f2.png', 'tool_f2.png', 'SAVE', false );
		//JToolBarHelper::apply('applyMetadata');
		JToolBarHelper::spacer();
		JToolBarHelper::custom( 'cancelMetadata', 'back.png', 'back.png', JTEXT::_("CORE_MENU_BACK"), false );
	}
}
?>