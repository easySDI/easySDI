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

class MDClasses extends JTable
{	
	var $id=null;	
	var $name=null;
	var $iso_key=null;
	var $type=null;	
	var $partner_id=null;	
	var $is_global=null;
	var $description=null;
	var $is_final=null;
	var $ordering=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_classes', 'id', $db ) ;    		
	}

}
class MDExt extends JTable
{	
	var $id=null;	
	var $name=null;
	var $value=null;
	var $partner_id=null;	
	var $translation=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_ext', 'id', $db ) ;    		
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
	var $is_constant=0;
	var $is_date=0;
	var $is_number=0;
	var $is_id=0;
	var $translation=null;
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_freetext', 'id', $db ) ;    		
	}

}
class MDList extends JTable
{	
	var $id=null;	
	var $name=null;
	var $partner_id=null;
	var $multiple=null;
	var $translation=null;
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_list', 'id', $db ) ;    		
	}

}


class MDListContent extends JTable
{	
	var $id=null;	
	var $list_id=null;
	var $code_key=null;
	var $key=null;
	var $value=null;
	var $partner_id=null;	
	//var $is_default=null;
	var $is_global=0;
	var $default=null;
	var $translation=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_list_content', 'id', $db ) ;    		
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
	var $translation=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_loc_freetext', 'id', $db ) ;    		
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
	var $is_global=null;
	
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
	var $partner_id=null;
	var $tab_id=null;
	var $text_prompt=null;
	var $ordering=null;
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_standard_classes', 'id', $db ) ;    		
	}
	
}


class MDTabs extends JTable{
	
	var $id=null;	
	var $text=null;
	var $name=null;
	var $partner_id=null;
	var $ordering=null;
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_metadata_tabs', 'id', $db ) ;    		
	}
	
}


?>
