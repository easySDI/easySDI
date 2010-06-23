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

class perimeter extends JTable
{	
	var $id=null;
	var $wfs_url=null;
	var $layer_name=null;
	var $wms_url=null;
	var $feature_type_name=null;
	var $perimeter_name=null;
	var $perimeter_desc=null;	
	var $area_field_name=null;
	var $name_field_name=null;
	var $name_field_search_name=null;
	var $id_field_name=null;
	var $wms_scale_min=0;
  	var $wms_scale_max=-1;
  	var $filter_field_name=null;
  	var $id_perimeter_filter=0;
	var $is_localisation=0;
	var $maxfeatures=-1;
	var $searchbox=0;
	var $allowMultipleSelection=1;
	var $sort=0;
	var $img_format=null;
	var $user=null;
	var $password=null;
	var $min_resolution=0;
	var $max_resolution=0;
	var $perimeter_code=null;
	var $ordering =0;
	var $easysdi_account_id=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_perimeter_definition', 'id', $db ) ;    		
	}

}


?>