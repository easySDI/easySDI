<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class base_layer extends JTable
{
	var $id=null;
	var $id_base=null;
	var $name=null;
	var $url=null;
	var $layers=null;
	var $projection=null;
	var $img_format=null;
	var $maxExtent=null;
	var $minScale=null;
	var $maxScale=null;
	var $resolutions='';
	var $resolutionOverScale=false;
	var $cache=false;
	var $unit=null;
	var $default_visibility=false;
	var $order=null;
	var $default_opacity=null;
	var $metadata_url=null;
	var $singletile=false;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_base_layer', 'id', $db ) ;
	}
}
class base_definition extends JTable
{
	var $id=null;
	var $projection=null;
	var $maxExtent=null;
	var $minScale=null;
	var $maxScale=null;
	var $resolutions=null;
	var $resolutionOverScale=false;
	var $unit=null;
	var $def=false;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_map_base_definition', 'id', $db ) ;
	}
}

?>