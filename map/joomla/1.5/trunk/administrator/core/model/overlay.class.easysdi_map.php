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

class overlay_content extends JTable
{
	var $id=null;
	var $url=null;
	var $url_type=null;
	var $projection=null;
	var $unit=null;
	var $maxExtent=null;
	var $minScale=null;
	var $maxScale=null;
	var $resolutions=null;
	var $resolutionOverScale=false;
	var $cache=false;
	var $layers=null;
	var $name=null;
	var $img_format=null;
	var $customStyle=false;
	var $overlay_group_id=null;
	var $default_visibility=false;
	var $order=null;
	var $default_opacity=null;
	var $metadata_url=null;
	var $singletile=false;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_overlay_content', 'id', $db ) ;
	}
}

class overlay_group extends JTable
{
	var $id=null;
	var $name=null;
	var $order=null;
	var $open=false;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_overlay_group', 'id', $db ) ;
	}
}

?>