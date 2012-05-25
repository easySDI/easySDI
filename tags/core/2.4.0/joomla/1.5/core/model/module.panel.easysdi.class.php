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

class modulePanel extends sdiTable
{	
	var $view_path=null;
	var $module_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_module_panel', 'id', $db ) ;    		
	}
	
	static function loadModulePanels(&$db)
	{
		$db->setQuery( "SELECT p.view_path , m.code FROM #__sdi_list_module m, #__sdi_module_panel p 
					WHERE m.id = p.module_id
					AND m.code <> 'CORE'
					ORDER BY p.ordering" );
		return $db->loadObjectList();
	}

}
?>