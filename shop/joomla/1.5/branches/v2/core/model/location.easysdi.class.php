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

class location extends JTable
{	
	var $id=null;
	var $wfs_url=null;
	var $feature_type_name=null;
	var $location_name=null;
	var $location_desc=null;	
	var $name_field_name=null;
	var $id_field_name=null;
  	var $filter_field_name=null;
  	var $id_location_filter=0;
	var $is_localisation=0;
	var $maxfeatures=-1;
	var $searchbox=0;
	var $allowMultipleSelection=1;
	var $sort=0;
	var $user=null;
	var $password=null;
	var $easysdi_account_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_location_definition', 'id', $db ) ;    		
	}

}
?>