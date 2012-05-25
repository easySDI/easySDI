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

defined('_JEXEC') or die('Restricted access');


class boundary extends JTable
{
	var $id=null;
	var $guid=null;
	var $code=null;
	var $name=null;
	var $description=null;
	var $created=null;
	var $updated=null;
	var $createdby=null;
	var $updatedby=null;
	var $ordering=0;
	var $northbound=null;
	var $southbound=null;
	var $eastbound=null;
	var $westbound=null;
	var $checked_out=null;
	var $checked_out_time=null;
	var $category_id=null;
	var $parent_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_boundary', 'id', $db ) ;
	}
}

class boundarycategory extends JTable
{
	var $id=null;
	var $guid=null;
	var $title=null;
	var $alias=null;
	var $parent_id=null;
	var $state=null;
	var $ordering=0;
	var $created=null;
	var $modified=null;
	var $created_by=null;
	var $modified_by=null;
	var $checked_out=null;
	var $checked_out_time=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_boundarycategory', 'id', $db ) ;
	}
}
?>