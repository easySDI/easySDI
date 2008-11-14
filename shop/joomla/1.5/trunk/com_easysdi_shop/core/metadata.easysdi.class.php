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

class MDClasses extends JTable
{	
	var $id=null;	
	var $name=null;
	var $iso_key=null;
	var $type=null;	
	var $partner_id=null;	
	var $is_default=null;
	var $description=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_classes', 'id', $db ) ;    		
	}

}

class MDDate extends JTable
{	
	var $id=null;	
	var $name=null;
	var $default_value=null;
	var $partner_id=null;	
	var $is_global=0;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_date', 'id', $db ) ;    		
	}

}

class MDFreetext extends JTable
{	
	var $id=null;	
	var $name=null;
	var $default_value=null;
	var $description=null;
	var $partner_id=null;	
	var $is_default=null;
	var $is_global=0;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_freetext', 'id', $db ) ;    		
	}

}


class MDList extends JTable
{	
	var $id=null;	
	var $code_key=null;
	var $key=null;
	var $value=null;
	var $default=null;
	var $partner_id=null;	
	var $is_default=null;
	var $is_global=0;
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_list', 'id', $db ) ;    		
	}

}

class MDLocFreetext extends JTable
{	
	var $id=null;	
	var $name=null;
	var $default_value=null;
	var $description=null;
	var $partner_id=null;	
	var $lang=null;
	var $is_global=0;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_loc_freetext', 'id', $db ) ;    		
	}
}

class MDNumeric extends JTable
{	
	var $id=null;	
	var $name=null;
	var $default_value=null;
	var $partner_id=null;
	var $min_value=null;
	var $max_value=null;
	

	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_numeric', 'id', $db ) ;    		
	}
}


class MDConstant extends JTable
{	
	var $id=null;	
	var $name=null;
	var $partner_id=null;
	var $value=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_constant', 'id', $db ) ;    		
	}
}

class MDStandard extends JTable{
	
	var $id=null;	
	var $name=null;
	var $partner_id=null;
	var $inherited=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_standard', 'id', $db ) ;    		
	}
	
}


class MDStandardClasses extends JTable{
	
	var $id=null;	
	var $standard_id=null;
	var $class_id=null;
	var $position=0;	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_standard_classes', 'id', $db ) ;    		
	}
	
}


?>