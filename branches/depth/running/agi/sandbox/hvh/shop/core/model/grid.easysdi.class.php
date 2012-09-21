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

class grid extends sdiTable
{	
	var $urlwms			= null;
	var $projection 	= null;
	var $unit			= null;
	var $extent			= null;
	var $minscale		= 0;
  	var $maxscale		= -1;
	var $imgformat		= null;
	var $layername		= null;
	var $urlwfs			= null;
	var $featuretype	= null;
	var $featureNS		= null;
	var $fieldname		= null;
	var $fielddetail	= null;
	var $fieldresource	= null;
	var $fieldgeom		= null;
	var $wmsuser		= null;
	var $wmspassword	= null;
	var $wmsaccount_id	= null;
	var $wfsuser		= null;
	var $wfspassword	= null;
	var $wfsaccount_id	= null;
	var $detailtooltip	= null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_grid', 'id', $db ) ;    		
	}
}


?>