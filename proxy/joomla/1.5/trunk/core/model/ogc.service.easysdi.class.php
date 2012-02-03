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

class ogcservice extends sdiTable
{	
	var $servletclass=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_ogcservice', 'code', $db ) ;    		
	}

	function getVersions (){
		$this->_db->setQuery( "SELECT v.code  FROM #__sdi_ogcservice_version sv INNER JOIN #__sdi_ogcversion v ON v.id = sv.ogcversion_id WHERE sv.ogcservice_id = ".$this->id );
		return $this->_db->loadResultArray();
	}
	
	public static function getOgcService(){
		$db =& JFactory::getDBO();
		$db->setQuery("SELECT code , servletclass FROM #__sdi_ogcservice");
		return $db->loadObjectList();
	}
}
?>