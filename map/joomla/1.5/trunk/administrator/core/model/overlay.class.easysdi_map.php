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

class overlay extends sdiTable
{
	var $group_id=null;
	var $url=null;
	var $version=null;
	var $type=null;
	var $layers=null;
	var $maxextent=null;
	var $minscale=null;
	var $maxscale=null;
	var $resolutions=null;
	var $resolutionoverscale=false;
	var $projection=null;
	var $imgformat=null;
	var $customstyle=false;
	var $cache=false;
	var $unit=null;
	var $singletile=false;
	var $user=null;
	var $password=null;
	var $account_id=null;
	var $defaultvisibility=false;
	var $defaultopacity=null;
	var $metadataurl=null;
	var $minresolution=null;
	var $maxresolution=null;
	var $published=null;
	var $matrixset = null;
	var $matrixids = null;
	var $style = null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_overlay', 'id', $db ) ;
	}
}

class overlayGroup extends sdiTable
{
	var $open=false;
	var $published=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_overlaygroup', 'id', $db ) ;
	}
}

?>