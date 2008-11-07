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

class SITE_catalog {
	
	
	function listCatalogContent(){
	
	$catalogUrlBase = config_easysdi::getValue("catalog_url");
	
	$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.1&resultType=results&namespace=csw:http://www.opengis.net/cat/csw&outputSchema=csw:IsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0";
	$catalogUrlGetRecordsCount =  $catalogUrlGetRecords . "&startPosition=1&maxRecords=1";
	
	global  $mainframe;
	$option=JRequest::getVar("option");
	$limit = JRequest::getVar('limit', 5 );
	$limitstart = JRequest::getVar('limitstart', 0 );
	$filterfreetextcriteria = JRequest::getVar('filterfreetextcriteria', '' ); 
		
	
	$cswfilter = "<Filter xmlns=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\"><And><PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\"><PropertyName>any</PropertyName><Literal>$filterfreetextcriteria%</Literal></PropertyIsLike></And></Filter>";


	$cswResults= simplexml_load_file($catalogUrlGetRecordsCount."&constraint=".$cswfilter);
		
	if ($cswResults !=null){
	$total = 0;
	foreach($cswResults->children("http://www.opengis.net/cat/csw")->SearchResults->attributes() as $a => $b) {
		if ($a=='numberOfRecordsMatched'){
			$total = $b;			
		}    		
	}
 

	$pageNav = new JPagination($total,$limitstart,$limit);

		
	$catalogUrlGetRecordsMD =  $catalogUrlGetRecords ."&startPosition=".($limitstart+1)."&maxRecords=".$limit."&constraint=".$cswfilter;
	
	$cswResults = DOMDocument::load($catalogUrlGetRecordsMD);
	
	
	}
	HTML_catalog::listCatalogContent($pageNav,$cswResults,$option,$total,$filterfreetextcriteria);
	}
}
?>