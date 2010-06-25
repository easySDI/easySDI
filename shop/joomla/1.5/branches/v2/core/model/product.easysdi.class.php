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
	var $treatment_type=null;
	var $notification_email=null;
	var $previewUser = null;
	var $previewPassword = null;
	var $metadata_internal=0;
	var $metadata_external=0;
	var $admin_partner_id=0;
	var $easysdi_account_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_product', 'id', $db ) ;    		
	}
	
	function deleteProduct ()
	{
		if(!parent::delete())
		{
			return false;
		}
		
		$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$this->id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			return false;
		}

		$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$this->id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			return false;
		}
		
		return true;
		
	}
	

}

?>