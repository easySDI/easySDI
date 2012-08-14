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


class relation extends JTable
{
	var $id=null;
	var $guid=null;
	var $parent_id=null;
	var $classchild_id=null;
	var $attributechild_id=null;
	var $objecttypechild_id=null;
	var $name=null;
	var $description=null;
	var $created=null;
	var $updated=null;
	var $createdby=null;
	var $updatedby=null;
	var $ordering=0;
	var $lowerbound=0;
	var $upperbound=999;
	var $rendertype_id=null;
	var $relationtype_id=null;
	var $classassociation_id=null;
	var $isocode=null;
	var $published=0;
	var $checked_out=null;
	var $checked_out_time=null;
	var $namespace_id=null;
	var $issearchfilter=0;
	var $editable=0;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_relation', 'id', $db ) ;
	}
}
?>