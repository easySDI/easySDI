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

defined('_JEXEC') or die('Restricted access');


class attribute extends JTable
{
	var $id=null;
	var $guid=null;
	var $code=null;
	var $name=null;
	var $description=null;
	var $created=null;
	var $updated=null;
	var $createdby=null;
	var $updatedby=null;
	var $label=null;
	var $ordering=0;
	var $information=null;
	var $attributetype_id=0;
	var $default=null;
	var $length=null;
	var $pattern=null;
	var $issystem=null;
	var $isextensible=null;
	var $type_isocode=null;
	var $codeList=null;
	var $namespace_id=null;
	var $listnamespace_id=null;
	var $checked_out=null;
	var $checked_out_time=null;
 	var $isocode=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_attribute', 'id', $db ) ;
	}
}

class account_attribute extends JTable
{
	var $id=null;
	var $account_id=null;
	var $attribute_id=null;
 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_account_attribute', 'id', $db ) ;
	}
}
?>