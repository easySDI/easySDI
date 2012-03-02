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

class historyassign extends JTable
{	
	var $id=null;
	var $guid=null;
	var $assigned=null;
	var $assignedby=null;
	var $object_id=null;
	var $account_id=null;
	var $information=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_history_assign', 'id', $db ) ;    		
	}
}

class historyassignByObjectId extends JTable
{	
	var $id=null;
	var $guid=null;
	var $assigned=null;
	var $assignedby=null;
	var $object_id=null;
	var $account_id=null;
	var $information=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_history_assign', 'object_id', $db ) ;    		
	}
}
?>
