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

class property extends sdiTable
{	
	var $mandatory=null;
	var $published=null;
	var $account_id=null;
	var $type_id=null;
	var $type=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_property', 'id', $db ) ;    		
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
	
	function loadPropertyValues ()
	{
		$this->_db->setQuery( "SELECT * FROM #__sdi_propertyvalue where property_id=".$this->id );
		$property = $this->_db->loadObject();
		if ($this->_db->getErrorNum()) {						
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");						 		
		}
		return $property;
	}
	function delete ()
	{
		$this->_db->setQuery( 'SELECT id FROM #__sdi_propertyvalue WHERE property_id ='.$this->id );
		$results = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			return false;
		}
		foreach ($results as $result)
		{
			$property_value = new property_value($this->_db);
			$property_value->load($result->id);
			if (!$property_value->delete()) {
				return false;
			}
		}
		return parent::delete();
	}
}

class property_value extends sdiTable
{	
	var $property_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_propertyvalue', 'id', $db ) ;    		
	}
	
	function store ()
	{
		return parent::store("property_id",$this->property_id);
	}
	
	function delete ()
	{
		return parent::delete("property_id",$this->property_id);
	}
	
	function getObjectCount($property_id)
	{
		$this->_db->setQuery( "select count(*) from  $this->_tbl WHERE property_id=".$property_id );
	 	$result = $this->_db->loadResult();
		if ($this->_db->getErrorNum())
		{	
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}
		return $result;
	}

}
?>