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


class objecttypelink extends JTable
{
	var $id=null;
	var $guid=null;
	var $parent_id=null;
	var $child_id=null;
	var $created=null;
	var $updated=null;
	var $createdby=null;
	var $updatedby=null;
	var $ordering=0;
	var $flowdown_versioning=null;
	var $escalate_versioning_update=null;
	var $checked_out=null;
	var $checked_out_time=null;
	var $parentbound_lower=0;
	var $parentbound_upper=999;
	var $childbound_lower=0;
	var $childbound_upper=999;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_objecttypelink', 'id', $db ) ;
	}
}
?>