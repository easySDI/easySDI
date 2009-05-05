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


class basemap extends JTable
{	
	var $id=null;	
	var $projection=null;
	var $unit=null;
	var $minResolution=null;
	var $maxResolution=null;	
	var $maxExtent=null;
	var $restrictedExtend=null;
	var $restrictedScales=null;
	var $def=null;
	var $alias=null;
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_basemap_definition', 'id', $db ) ;    		
	}

}
class basemap_content extends JTable
{	
	var $id=null;
	var $basemap_def_id=null;	
	var $url=null;
	var $url_type=null;
	var $singletile=null;	
	var $projection=null;
	var $unit=null;
	var $minResolution=null;
	var $maxResolution=null;	
	var $maxExtent=null;
	var $layers=null;
	var $name=null;
	var $img_format=null;
	var $ordering = 0;
	var $user = null;
	var $password = null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_basemap_content', 'id', $db ) ;    		
	}

}
?>