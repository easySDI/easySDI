<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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
		
				
		// Conditions pour la visibilité publique/privée de la métadonnée
		require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
				
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		
		$partner = new partnerByUserId($db);
		
		if (!$user->guest){
			$partner->load($user->id);
		}else{
			$partner->partner_id = 0;
		}
		
		$filter = "";
		if($partner->partner_id == 0)
		{
			//No user logged, display only external products
			$filter .= " AND (metadata_external=1) ";
		}
		else
		{
			//User logged, display products according to users's rights
			if(userManager::hasRight($partner->partner_id,"REQUEST_EXTERNAL"))
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (metadata_external=1
					OR
					(metadata_internal =1 AND
					(partner_id =  $partner->partner_id
					OR
					partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					OR 
					partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
					OR
					partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
					
					))) ";
				}
				else
				{
					$filter .= " AND (metadata_external=1 AND 
					(partner_id <>  $partner->partner_id
					AND
					partner_id <> (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					AND 
					partner_id NOT IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
					AND
					partner_id NOT IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
					)
					) ";
				}
			}
			else
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (metadata_internal =1 AND
					(partner_id =  $partner->partner_id
					OR
					partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					OR 
					partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
					OR
					partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
					)) ";
									
				}
				else
				{
					//no command right
					$filter .= " AND (metadata_external = 10 AND metadata_internal = 10) ";
				}
			}
		} 
		
		// Pour chaque requête qui récupère des id de métadonnées selon certains critères sur le produit, il faut tester que le produit est publié
		
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
		// Filtre sur la thématique (Critères de recherche avancés)
		if($filter_theme)
		{
			$cswThemeFilter = "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
			<PropertyName>any</PropertyName>
			<Literal>$filter_theme</Literal>
			</PropertyIsLike> ";
			
			$empty = false;
		}
		
		// Filtre sur l'id du fournisseur (Critères de recherche avancés)
		$cswPartnerFilter = "";
		if( $partner_id )
		{
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE published=1 and partner_id = ".$partner_id.$filter;
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
		// Filtre sur la visibilité du produit (Critères de recherche avancés)
		if($filter_visible )
		{	
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE published=1 and previewWmsUrl is not null".$filter;
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
		// Filtre sur la possibilité de commander le produit (Critères de recherche avancés)
		if($filter_orderable)
		{
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE orderable = 1 AND published = 1".$filter; 
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
		
		
		$propertyTitle="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString";
		/*$sortBy='
           			<ogc:SortProperty xmlns:ogc=\"http://www.opengis.net/ogc\">
						<ogc:PropertyName>title</ogc:PropertyName>
                		<ogc:SortOrder>ASC</ogc:SortOrder>
            		</ogc:SortProperty>
        		';
		*/
		
	// Filtre minimum: Produits publiés
		$cswMinimumFilter = "";
		if( $cswPartnerFilter == "" and $cswThemeFilter == "" and $cswOrderableFilter == ""  and $cswVisibleFilter == "" )
		{
			$db =& JFactory::getDBO();
			$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE published=1".$filter;
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
				//echo $md_id->metadata_id."<br>";
				$cswPartnerFilter .= "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>fileId</PropertyName>
				<Literal>$md_id->metadata_id</Literal>
				</PropertyIsLike> ";
			}
			$empty = false;
		}
		
		$cswfilter = "<Filter xmlns=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">
						<Or>";
		// Filtre sur le texte (Critères de recherche avancés)
		if ( $filterfreetextcriteria  || $empty )
		{
			$cswfilter = $cswfilter."<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<PropertyName>any</PropertyName>
						<Literal>$filterfreetextcriteria%</Literal>
						</PropertyIsLike> ";
		}
		// Filtre sur le texte (Critères de recherche simple)
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
		$cswfilter = $cswfilter.$cswMinimumFilter;
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
			
			// Séparation en n éléments par page
			$catalogUrlGetRecordsMD = $catalogUrlGetRecords ."&startPosition=".($limitstart+1)."&maxRecords=".$limit."&constraint=".urlencode($cswfilter);
			//echo "<hr>".$catalogUrlGetRecordsMD."<br>";
								
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