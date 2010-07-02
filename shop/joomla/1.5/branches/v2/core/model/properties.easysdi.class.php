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

class properties extends sdiTable
{	
	var $mandatory=null;
	var $published=null;
	var $account_id=null;
	var $type_id=null;
	var $type=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_property', 'id', $db ) ;    		
	}

}

class properties_values extends sdiTable
{	
	var $property_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_property_value', 'id', $db ) ;    		
	}

}
?>