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

class product extends JTable
{
	var $metadata_id=null;
	var $id=0;
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
	var $is_free=0;
	var $metadata_partner_id=0;
	var $previewBaseMapId=0;
	var $previewWmsUrl=null;
	var $previewWmsLayers=null;
	var $previewMinResolution=0;
	var $previewMaxResolution=0;
	var $previewProjection=null;
	var $previewUnit=null;
	var $previewImageFormat=null;
	var $diffusion_partner_id=0;
	
	
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
	var $wms_scale_min=0;
  	var $wms_scale_max=-1;
  	var $filter_field_name=null;
  	var $id_perimeter_filter=0;
	var $is_localisation=0;
	var $maxfeatures=-1;
	var $searchbox=0;
	var $sort=0;
	var $img_format=null;
	var $user=null;
	var $password=null;
	var $min_resolution=0;
	var $max_resolution=0;
	var $perimeter_code=null;
	var $ordering =0;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_perimeter_definition', 'id', $db ) ;    		
	}

}

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
	var $sort=0;
	var $user=null;
	var $password=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_location_definition', 'id', $db ) ;    		
	}

}
?>