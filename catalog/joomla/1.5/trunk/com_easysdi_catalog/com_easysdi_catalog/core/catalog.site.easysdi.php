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
		global $mainframe;
		$empty = true;
		
		$maxDescr = config_easysdi::getValue("description_length");
		$MDPag = config_easysdi::getValue("pagination_metadata");
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.1&resultType=results&namespace=csw:http://www.opengis.net/cat/csw&outputSchema=csw:IsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0"; //&sortby=".$sortString;
		$catalogUrlGetRecordsCount =  $catalogUrlGetRecords . "&startPosition=1&maxRecords=1";

		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', $MDPag );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$filterfreetextcriteria = JRequest::getVar('filterfreetextcriteria', '' );
		$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria', '');
		$minX = JRequest::getVar('bboxMinX', "-180" );
		$minY = JRequest::getVar('bboxMinY', "-90" );
		$maxX = JRequest::getVar('bboxMaxX', "180" );
		$maxY = JRequest::getVar('bboxMaxY', "90" );
		$partner_id  = JRequest::getVar('partner_id');
		$filter_theme = JRequest::getVar('filter_theme');
		$filter_visible = JRequest::getVar('filter_visible');
		$filter_orderable = JRequest::getVar('filter_orderable');

		if ($minX == "-180" && $minY == "-90" && $maxX == "180" && $maxY == "90"){
			$bboxfilter ="";
		}
		else{
			$bboxfilter ="<BBOX>
			<PropertyName>ows:BoundingBox</PropertyName>
			<gml:Envelope><gml:lowerCorner>$minY $minX</gml:lowerCorner><gml:upperCorner>$maxY $maxX</gml:upperCorner></gml:Envelope>
			</BBOX>";
		}
		
		$cswThemeFilter="";
		if($filter_theme)
		{
			$cswThemeFilter = "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
			<PropertyName>any</PropertyName>
			<Literal>$filter_theme</Literal>
			</PropertyIsLike> ";
			
			$empty = false;
		}
		
		$cswPartnerFilter = "";
		if( $partner_id )
		{
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE partner_id = ".$partner_id;
			$db->setQuery( $query);
			$list_id = $db->loadObjectList() ;
			if ($db->getErrorNum())
			{
				echo "<div class='alert'>";
				echo 	$db->getErrorMsg();
				echo "</div>";
			}
			foreach ($list_id as $md_id)
			{
				$cswPartnerFilter .= "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>fileId</PropertyName>
				<Literal>$md_id->metadata_id</Literal>
				</PropertyIsLike> ";
			}
			$empty = false;
		}
		

		$cswVisibleFilter = "";
		if($filter_visible )
		{	
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE previewWmsUrl is not null";
			$db->setQuery( $query);
			$list_id = $db->loadObjectList() ;
			if ($db->getErrorNum())
			{
				echo "<div class='alert'>";
				echo 	$db->getErrorMsg();
				echo "</div>";
			}
			foreach ($list_id as $md_id)
			{
				$cswVisibleFilter .= "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>fileId</PropertyName>
				<Literal>$md_id->metadata_id</Literal>
				</PropertyIsLike> ";
			}
			$empty = false;
		}
		
		$cswOrderableFilter = "";
		if($filter_orderable)
		{
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE orderable = 1 AND published = 1"; 
			$db->setQuery( $query);
			$list_id = $db->loadObjectList() ;
			if ($db->getErrorNum())
			{
				echo "<div class='alert'>";
				echo 	$db->getErrorMsg();
				echo "</div>";
			}
			foreach ($list_id as $md_id)
			{
				$cswOrderableFilter .= "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>fileId</PropertyName>
				<Literal>$md_id->metadata_id</Literal>
				</PropertyIsLike> ";
			}
			$empty = false;
		}
		
		$cswfilter = "<Filter xmlns=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">
						<Or>";
		if ( $filterfreetextcriteria  || $empty )
		{
			$cswfilter = $cswfilter."<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<PropertyName>any</PropertyName>
						<Literal>$filterfreetextcriteria%</Literal>
						</PropertyIsLike> ";
		}
		if ($simple_filterfreetextcriteria || $empty)		
		{		
			$cswfilter = $cswfilter."<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<PropertyName>any</PropertyName>
						<Literal>$simple_filterfreetextcriteria%</Literal>
						</PropertyIsLike>
						";
		}
				$cswfilter = $cswfilter.$cswPartnerFilter;
				$cswfilter = $cswfilter.$cswThemeFilter;
				$cswfilter = $cswfilter.$cswOrderableFilter;
				$cswfilter = $cswfilter.$cswVisibleFilter;
				$cswfilter = $cswfilter.$bboxfilter;
				$cswfilter = $cswfilter."</Or></Filter>";//.$sortBy;
		
						
		$cswResults= simplexml_load_file($catalogUrlGetRecordsCount."&constraint=".urlencode($cswfilter));


		if ($cswResults !=null){
			$total = 0;
			foreach($cswResults->children("http://www.opengis.net/cat/csw")->SearchResults->attributes() as $a => $b) {
				if ($a=='numberOfRecordsMatched'){
					$total = $b;
				}
			}
						
			$pageNav = new JPagination($total,$limitstart,$limit);
			
			// S�paration en n �l�ments par page
			$catalogUrlGetRecordsMD = $catalogUrlGetRecords ."&startPosition=".($limitstart+1)."&maxRecords=".$limit."&constraint=".urlencode($cswfilter);
			$cswResults = DOMDocument::load($catalogUrlGetRecordsMD);

			// Tri avec XSLT
			// Chargement du fichier XSL
			/*$xsl = new DOMDocument();
			$xsl->load(dirname(__FILE__)."\..\xsl\sortMetadata.xsl");
			// Import de la feuille XSL
			$xslt = new XSLTProcessor();
			$xslt->importStylesheet($xsl);
			
			$cswResults = DOMDocument::loadXML($xslt->transformToXml($cswResults));*/
			// Fin du tri
		}
		HTML_catalog::listCatalogContentWithPan($pageNav,$cswResults,$option,$total,$filterfreetextcriteria,$maxDescr);
	}
	
	function Post($url,$array){
		$args = http_build_query($array);
		$url = parse_url($url);
		if(isset($url['port'])){
			$port = $url['port'];
		}else{
			$port = 80;
		}

		if (!$fp = fsockopen ($url['host'], $port, $errno, $errstr)){
			$out = false;
		}else{
			$size = strlen($args);
			$request = "POST ".$url['path']." HTTP/1.1\n";
			$request .= "Host: ".$url['host']."\n";
			$request .= "Connection: Close\r\n";
			$request .= "Content-type: application/x-www-form-urlencoded\n";
			$request .= "Content-length: ".$size."\n\n";
			$request .= $args."\n";
			$fput = fputs($fp, $request);
			fclose ($fp);
			$out = true;
		}
		return $out;
	}

}
?>