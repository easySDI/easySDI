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

class product extends sdiTable
{
	
	var $objectversion_id=0;
	var $surfacemin=null;
	var $surfacemax=null;
	var $published=0;
	var $visibility_id=0;
	var $manager_id=null;
	var $diffusion_id=null;
	var $treatmenttype_id=null;
	var $notification=null;
	var $viewbasemap_id=null;
	var $viewurlwms=null;
	var $viewlayers=null;
	var $viewminresolution=null;
	var $viewmaxresolution=null;
	var $viewprojection=null;
	var $viewunit=null;
	var $viewimgformat=null;
	var $viewuser=null;
	var $viewpassword=null;
	var $viewaccount_id=null;
		
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_product', 'id', $db ) ;    		
	}
	
	function deleteProduct ()
	{
		$this->_db->setQuery( "DELETE FROM  $this->_tbl WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}

		$this->_db->setQuery( "DELETE FROM  $this->_tbl WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
		
		return parent::delete();
		
	}
	function delete()
	{
		$this->_db->setQuery( "DELETE FROM  #__sdi_product_perimeter WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
	
		$this->_db->setQuery( "DELETE FROM  #__sdi_product_property WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
		
		return parent::delete();
	}
	
	function publish ()
	{
		$this->published = 1;
		return $this->store();
	}
	
	function unpublish()
	{
		$this->published = 0;
		return $this->store();
	}
}

?>