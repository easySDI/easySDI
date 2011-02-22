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

class properties extends JTable
{	
	var $id=null;	
	var $order=null;
	var $mandatory=null;
	var $text=null;	
	var $published=null;
	var $update_date = null;
	var $partner_id=null;
	var $type_code=null;
	var $code=null;
	var $translation=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_product_properties_definition', 'id', $db ) ;    		
	}

}

class properties_values extends JTable
{	
	var $id=null;
	var $properties_id=null;
	var $order=null;
	var $value=null;
	var $text=null;	
	var $translation=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_product_properties_values_definition', 'id', $db ) ;    		
	}

}
?>