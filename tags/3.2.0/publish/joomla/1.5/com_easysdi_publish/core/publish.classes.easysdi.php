<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

class featureSource extends JTable
{
	var $id=null;
	var $featureGUID=0;
	var $partner_id=0;
	var $name=null;
	var $projection=null;
	var $formatId=null;
	var $scriptId=null;
	var $fileList=null;
	var $creation_date=null;
	var $update_date=null;

	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_featuresource', 'id', $db ) ;    		
	}
}

class layer extends JTable
{
	var $id=null;
	var $featuresourceId=null;
	var $name=null;
	var $title=null;
	var $geometry=null;
	var $description=null;
	var $quality_area=null;
	var $keywords=null;
	var $layerGuid=null;
	var $creation_date=null;
	var $update_date=null;
	var $partner_id=0;
	var $wmsUrl=null;
	var $wfsUrl=null;
	var $kmlUrl=null;
	var $bbox=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_layer', 'id', $db ) ;    		
	}
}
