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

class object extends JTable
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
	var $ordering=null;
	var $objecttype_id=null;
	var $metadata_id=null;
	var $published=null;
	var $projection_id=null;
	var $view_minResolution=null;
	var $view_maxResolution=null;
	var $view_maxExtent=null;
	var $view_decimalPrecisionDisplayed=null;
	var $view_restrictedExtend=null;
	var $view_restrictedScales=null;
	var $view_url=null;
	var $view_layers=null;
	var $view_ImageFormat=null;
	var $view_user=null;
	var $view_password=null;
	var $account_id=null;
	var $checked_out=null;
	var $checked_out_time=null;
	var $metadatastate_id=null;
	/*
	
	var $update_date=null;
	var $creation_date=null;
	var $supplier_name=null;	
	var $surface_min=0;	
	var $surface_max=0;
	var $data_title=null;
	var $orderable=null;
	var $internal=0;
	var $external=0;
	var $metadata_standard_id=null;
	var $is_free=0;
	var $metadata_partner_id=0;
	var $previewBaseMapId=0;
	var $previewProjection=null;
	var $previewUnit=null;
	var $diffusion_partner_id=0;
	var $treatment_type=null;
	var $notification_email=null;
	var $metadata_internal=0;
	var $metadata_external=0;
	var $admin_partner_id=0;
	*/
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_object', 'id', $db ) ;    		
	}
}

class objectByMetadataId extends JTable
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
	var $ordering=null;
	var $objecttype_id=null;
	var $metadata_id=null;
	var $published=null;
	var $projection_id=null;
	var $view_minResolution=null;
	var $view_maxResolution=null;
	var $view_maxExtent=null;
	var $view_decimalPrecisionDisplayed=null;
	var $view_restrictedExtend=null;
	var $view_restrictedScales=null;
	var $view_url=null;
	var $view_layers=null;
	var $view_ImageFormat=null;
	var $view_user=null;
	var $view_password=null;
	var $account_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_object', 'metadata_id', $db ) ;    		
	}
}

class manager_object extends JTable
{
	var $id=null;
	var $account_id=null;
	var $object_id=null;
 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_manager_object', 'id', $db ) ;
	}
}

class editor_object extends JTable
{
	var $id=null;
	var $account_id=null;
	var $object_id=null;
 	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_editor_object', 'id', $db ) ;
	}
}
?>