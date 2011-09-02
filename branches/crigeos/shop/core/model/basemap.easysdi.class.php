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


class basemap extends sdiTable
{	
	var $projection				=null;
	var $unit					=null;
	var $minresolution			=null;
	var $maxresolution			=null;
	var $default				=0;	
	var $maxextent				=null;
	var $restrictedextent		=null;
	var $restrictedscales		=null;
	var $decimalprecision		=null;
	var $dfltfillcolor			=null;
	var $dfltstrkcolor			=null;
	var $dfltstrkwidth			=null;
	var $selectfillcolor		=null;
	var $selectstrkcolor		=null;
	var $tempfillcolor			=null;
	var $tempstrkcolor			=null;
	var $minresol				=null;
	var $maxresol				=null;
	var $restrictedresol		=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_basemap', 'id', $db ) ;    		
	}
	
	function loadDefault ()
	{
		$this->reset();

		$db =& $this->getDBO();

		$query = 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE `default` = 1';
		$db->setQuery( $query );

		if ($result = $db->loadAssoc( )) {
			return $this->bind($result);
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	function delete ()
	{
		$this->_db->setQuery( 'SELECT id FROM #__sdi_basemapcontent WHERE basemap_id ='.$this->id );
		$results = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			return false;
		}
		foreach ($results as $result)
		{
			$basemapcontent = new basemap_content($this->_db);
			$basemapcontent->load($result->id);
			if (!$basemapcontent->delete()) {
				return false;
			}
		}
		return parent::delete();
	}

}

class basemap_content extends sdiTable
{	
	var $basemap_id				=null;	
	var $url					=null;
	var $urltype				=null;
	var $singletile				=null;
	var $maxextent				=null;	
	var $minresolution			=null;
	var $maxresolution			=null;
	var $projection				=null;
	var $unit					=null;
	var $layers					=null;
	var $imgformat				=null;
	var $attribution			=null;
	var $matrixset 				=null;
	var $matrixids 				=null;
	var $style	 				=null;
	var $user 					=null;
	var $password 				=null;
	var $account_id 			=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_basemapcontent', 'id', $db ) ;    		
	}
	
	function store ()
	{
		return parent::store("basemap_id",$this->basemap_id);
	}
	
	function delete ()
	{
		return parent::delete("basemap_id",$this->basemap_id);
	}
	
	function getObjectCount($basemap_id)
	{
		$this->_db->setQuery( "select count(*) from  $this->_tbl WHERE basemap_id=".$basemap_id );
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