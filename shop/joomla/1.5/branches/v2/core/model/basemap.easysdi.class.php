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
	var $projection=null;
	var $unit=null;
	var $minResolution=null;
	var $maxResolution=null;
	var $default=0;	
	var $maxExtent=null;
	var $restrictedExtent=null;
	var $restrictedScales=null;
	var $decimalPrecision=null;
	var $dfltfillcolor=null;
	var $dfltstrkcolor=null;
	var $dfltstrkwidth=null;
	var $selectfillcolor=null;
	var $selectstrkcolor=null;
	var $tempfillcolor=null;
	var $tempstrkcolor=null;
	
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
		. ' WHERE default = 1';
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

}

class basemap_content extends sdiTable
{	
	var $basemap_id=null;	
	var $url=null;
	var $urltype=null;
	var $singletile=null;
	var $maxExtent=null;	
	var $minResolution=null;
	var $maxResolution=null;
	var $projection=null;
	var $unit=null;
	var $layers=null;
	var $imgformat=null;
	var $attribution=null;
	var $user = null;
	var $password = null;
	var $account_id = null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_basemap_content', 'id', $db ) ;    		
	}

}
?>