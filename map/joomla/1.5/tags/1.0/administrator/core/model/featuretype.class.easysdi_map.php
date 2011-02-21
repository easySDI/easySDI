<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class feature_type extends JTable
{
	var $id=null;
	var $name=null;
	var $geometry=null;
		 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_feature_type', 'id', $db ) ;    		
	}
}
class attribute extends JTable
{
	var $id=null;
	var $id_ft=null;
	var $name=null;
	var $data_type=null;
	var $width=null;
	var $initial_visibility=null;
	var $visible = null;
		 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_attribute', 'id', $db ) ;    		
	}
}
?>