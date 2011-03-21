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

class baseLayer extends sdiTable
{
	var $url=null;
	var $layers=null;
	var $projection=null;
	var $imgformat=null;
	var $customStyle=false;
	var $maxextent=null;
	var $extent=null;
	var $minscale=null;
	var $maxscale=null;
	var $resolutions='';
	var $resolutionoverscale=false;
	var $cache=false;
	var $unit=null;
	var $ordering=null;
	var $singletile=false;
	var $user=null;
	var $password=null;
	var $easysdiaccount_id =null;
	var $defaultvisibility=null;
	var $defaultopacity=null;
	var $metadataurl=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_baselayer', 'id', $db ) ;
	}
}

?>