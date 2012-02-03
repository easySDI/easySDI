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

class simpleSearch extends JTable
{
	var $id=null;
	var $title=null;
	var $dropdown_feature_type=null;
	var $dropdown_display_attr=null;
	var $dropdown_id_attr=null;
	var $search_attribute=null;
	var $operator=null;
	 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_simple_search_type', 'id', $db ) ;    		
	}
}

class additionalFilter extends JTable
{
	var $id=null;
	var $attribute=null;
	var $value=null;
	var $operator='==';
	var $title=null;
	 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_simple_search_additional_filter', 'id', $db ) ;    		
	}
}
?>