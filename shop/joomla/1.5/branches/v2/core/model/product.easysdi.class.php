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
	var $available=0;
	var $free=0;
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
	
	function store()
	{
		if($this->available == 0 || $this->free == 0)
		{
			$this->_db->setQuery( "DELETE FROM  #__sdi_product_file WHERE product_id = ".$this->id );
			if (!$this->_db->query()) {
				return false;
			}
		}
		else
		{
			if(isset($_FILES['productfile']) && !empty($_FILES['productfile']['name'])) 
		 	{
		 		$fileName = $_FILES['productfile']["name"];
		 		$tmpName =  $_FILES['productfile']["tmp_name"];
		 		$type = strtolower(substr($fileName, strrpos($fileName, '.')+1)) ;
  				$size = ($_FILES['productfile']["size"] / 1024) ;
			 	$fp      = fopen($tmpName, 'r');
			 	$content = fread($fp, filesize($tmpName));
			 	$content = addslashes($content);
			 	fclose($fp);
				
				$this->_db->setQuery( "SELECT COUNT(*) FROM  #__sdi_product_file WHERE product_id = ".$this->id );
				$result = $this->_db->loadResult();
				if ($this->_db->getErrorNum()) {
					return false;
				}
				if($result > 0)
				{
					$this->_db->setQuery( "UPDATE  #__sdi_product_file SET data='".$content."', filename='".$fileName."', size =".$size.", type='".$type."' WHERE product_id = ".$this->id );
					if (!$this->_db->query()) {
						return false;
					}
				}
				else
				{
					$this->_db->setQuery( "INSERT INTO  #__sdi_product_file (filename, data,product_id, type, size) VALUES ('".$fileName."' ,'".$content."', ".$this->id.", '".$type."', ".$size." )" );
					if (!$this->_db->query()) {
						return false;
					}
				}
			}
		}
		return parent::store();
	}
	
	function getFileName()
	{
			$this->_db->setQuery( "SELECT filename FROM  #__sdi_product_file WHERE product_id = ".$this->id );
			$filename = $this->_db->loadResult();
			if ($this->_db->getErrorNum()) {
				$mainframe->enqueueMessage($this->_db->getErrorMsg());
				return false;
			}
			return $filename;
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