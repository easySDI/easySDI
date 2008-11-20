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

class product extends JTable
{
	var $metadata_id=null;
	var $id=null;
	var $partner_id=null;
	var $update_date=null;
	var $creation_date=null;
	var $supplier_name=null;	
	var $surface_min=0;	
	var $surface_max=0;
	var $data_title=null;
	var $published=null;
	var $orderable=null;
	var $internal=0;
	var $external=0;
	var $metadata_standard_id=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_product', 'id', $db ) ;    		
	}

}

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
	var $id_field_name=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_perimeter_definition', 'id', $db ) ;    		
	}

}
?>