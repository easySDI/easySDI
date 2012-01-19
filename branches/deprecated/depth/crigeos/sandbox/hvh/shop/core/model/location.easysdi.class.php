<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class location extends sdiTable
{	
	var $urlwfs=null;
	var $featuretype=null;
	var $fieldid=null;
	var $fieldname=null;
  	var $fieldfilter=null;
  	var $filterlocation_id=null;
	var $islocalisation=0;
	var $maxfeatures=-1;
	var $multipleselection=1;
	var $searchbox=0;
	var $sort=0;
	var $user=null;
	var $password=null;
	var $account_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_location', 'id', $db ) ;    		
	}

}
?>