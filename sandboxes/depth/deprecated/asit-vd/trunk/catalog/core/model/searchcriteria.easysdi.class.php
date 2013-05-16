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


class searchcriteria extends sdiTable
{
	var $simpletab=null;
	var $advancedtab=null;
	var $relation_id=null;
	var $criteriatype_id=3;
	var $context_id=null;
	var $rendertype_id=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_searchcriteria', 'id', $db ) ;
	}
	
	function loadDefaultValue ($contextid){
		$this->_db->setQuery( "SELECT defaultvalue, defaultvaluefrom, defaultvalueto FROM  #__sdi_context_criteria WHERE context_id='".$contextid."' and criteria_id ='".$this->id."'" );
		return $this->_db->loadObject() ;
	}
}

class searchcriteriaByRelationId extends sdiTable
{
	var $simpletab=null;
	var $advancedtab=null;
	var $relation_id=null;
	var $criteriatype_id=3;
	var $context_id=null;
	var $rendertype_id=null;
	
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_searchcriteria', 'relation_id', $db ) ;
	}
}

class criteriatype extends sdiTable
{	
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_list_criteriatype', 'id', $db ) ;
	}
}
?>