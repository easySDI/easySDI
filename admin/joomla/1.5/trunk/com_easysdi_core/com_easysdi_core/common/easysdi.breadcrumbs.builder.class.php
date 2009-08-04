<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class breadcrumbsBuilder
{

	function addFirstCrumb ($page_title)
	{
		// Get the menu item object
		global  $mainframe;
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$params = &$mainframe->getParams();
		//Handle the breadcrumbs
		if(!$menu)
		{
			$params->set('page_title',	JText::_($page_title));
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_($page_title), '' );
			$document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
		}
		
		
	}
	
	function addSecondCrumb ($page_title, $previousCrumb_title, $previousCrumb_link)
	{
	// Get the menu item object
		global  $mainframe;
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$params = &$mainframe->getParams();
		//Handle the breadcrumbs
		if(!$menu)
		{
			$params->set('page_title',	JText::_($page_title));
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_($previousCrumb_title), $previousCrumb_link );
			$breadcrumbs->addItem( JText::_($page_title), '' );
			$document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
		}
	}
}
?>