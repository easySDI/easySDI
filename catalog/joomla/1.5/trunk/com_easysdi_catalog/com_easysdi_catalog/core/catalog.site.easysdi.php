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
		
		//Address where we do the post request (catalog url)
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.2&resultType=results&namespace=csw:http://www.opengis.net/cat/csw/2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0"; //&sortby=".$sortString;
		$xmlHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		
		$catalogUrlGetRecordsCount =  $catalogUrlGetRecords . "&startPosition=1&maxRecords=1";

		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', $MDPag );
		$limitstart = JRequest::getVar('limitstart', 0 );
	//	$filterfreetextcriteria = JRequest::getVar('filterfreetextcriteria');
		$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
		$minX = JRequest::getVar('bboxMinX', "-180" );
		$minY = JRequest::getVar('bboxMinY', "-90" );
		$maxX = JRequest::getVar('bboxMaxX', "180" );
		$maxY = JRequest::getVar('bboxMaxY', "90" );
		$partner_id  = JRequest::getVar('partner_id');
		$filter_theme = JRequest::getVar('filter_theme');
		$filter_visible = JRequest::getVar('filter_visible');
		$filter_orderable = JRequest::getVar('filter_orderable');
		$filter_date = JRequest::getVar('update_cal');
		$filter_date_comparator = JRequest::getVar('update_select');
		/* Todo, push the date format in EasySDI config and
		set it here accordingly */
		if($filter_date){
			$temp = explode(".", $filter_date);
			$filter_date = $temp[2]."-".$temp[1]."-".$temp[0];
		}
		
		// Conditions pour la visibilit� publique/priv�e de la m�tadonn�e
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
		
		//do not display result if no request is done
		//(simple_filterfreetextcriteria or filterfreetextcriteria have not been hit once)
		//print_r($_GET);
		//echo "<br><br>";
		$display_internal_orderable = false;
		if(isset($_GET['simple_search_button']) || isset($_GET['limitstart'])){
			$filter = "";
			if($partner->partner_id == 0)
			{
				//No user logged, display only external products
				$filter .= " AND (metadata_external=1) ";
			}
			else
			{
				// User logged in. (Return also products of the root's current account)
				$filter .= " AND (metadata_external=1
						OR
						(metadata_internal =1 AND
						(partner_id =  $partner->partner_id
						OR
						partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
						OR 
						partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
						OR
						partner_id  IN (SELECT partner_id FROM jos_easysdi_community_partner WHERE root_id = $partner->partner_id ) 
						
						))) ";
						$display_internal_orderable =true;
			}
			/*May only be applied to the shop.
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
						partner_id  IN (SELECT partner_id FROM jos_easysdi_community_partner WHERE root_id = $partner->partner_id ) 
						
						))) ";
						$display_internal_orderable =true;
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
						partner_id NOT IN (SELECT partner_id FROM jos_easysdi_community_partner WHERE root_id = $partner->partner_id ) 
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
						partner_id  IN (SELECT partner_id FROM jos_easysdi_community_partner WHERE root_id = $partner->partner_id ) 
						)) ";
										
					}
					else
					{
						//no command right
						$filter .= " AND (metadata_external = 10 AND metadata_internal = 10) ";
					}
				}
			}
			*/
			
			// Pour chaque requ�te qui r�cup�re des id de m�tadonn�es selon certains crit�res sur le produit, il faut tester que le produit est publi�
			// On rempli un tableau avec les m�tadonn�es recherchable, on traverse les filtres et on ajoute ou retire du tableau les id n�cessaires
			//$searchableMetadata = array();
			
			
			if ($minX == "-180" && $minY == "-90" && $maxX == "180" && $maxY == "90"){
				$bboxfilter ="";
			}
			else{
				//echo "bbox";
				$bboxfilter ="<BBOX>
				<PropertyName>ows:BoundingBox</PropertyName>
				<gml:Envelope><gml:lowerCorner>$minY $minX</gml:lowerCorner><gml:upperCorner>$maxY $maxX</gml:upperCorner></gml:Envelope>
				</BBOX>";
			}
			
			$cswThemeFilter;
			// Filtre sur la th�matique (Crit�res de recherche avanc�s)
			if($filter_theme)
			{
				//echo ", theme";
				$cswThemeFilter = "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>any</PropertyName>
				<Literal>$filter_theme</Literal>
				</PropertyIsLike> ";
				
				$empty = false;
			}
			
			// Filtre sur l'id du fournisseur (Crit�res de recherche avanc�s)
			$arrFilterMd =array();
			$arrPartnerMd;
			if( $partner_id )
			{
				//echo ", partner";
				$db =& JFactory::getDBO();
				$query = "SELECT metadata_id FROM #__easysdi_product WHERE published=1 and partner_id = ".$partner_id.$filter;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrPartnerMd = array();
				//If no result, give an unexisting id back
				if(count($list_id == 0))
					$arrPartnerMd[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrPartnerMd[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrPartnerMd != null)
				$arrFilterMd[] = $arrPartnerMd;
			
			$arrCswVisibleMd;
			// Filtre sur la visibilit� du produit (Crit�res de recherche avanc�s)
			if($filter_visible )
			{	
				//echo ", prod visibily";
				$db =& JFactory::getDBO();
				$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE published=1 and previewWmsUrl != ''".$filter;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrCswVisibleMd = array();
				//If no result, give an unexisting id back
				if(count($list_id == 0))
					$arrCswVisibleMd[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrCswVisibleMd[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrCswVisibleMd != null)
				$arrFilterMd[] = $arrCswVisibleMd;
			
			$arrCswOrderableFilterMd;
			// Filtre sur la possibilit� de commander le produit (Crit�res de recherche avanc�s)
			if($filter_orderable)
			{
				//echo ", orderable";
				$db =& JFactory::getDBO();
				$query = "";
				if($display_internal_orderable)
					$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE orderable = 1 AND published = 1 and internal=1".$filter; 
				else
					$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE orderable = 1 AND published = 1 and external=1".$filter; 
				
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrCswOrderableFilterMd = array();
				if(count($list_id == 0))
					$arrCswOrderableFilterMd[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrCswOrderableFilterMd[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrCswOrderableFilterMd != null)
				$arrFilterMd[] = $arrCswOrderableFilterMd;
			
			// Filtre sur la date
			$arrCswDateFilter;
			if($filter_date)
			{
				//echo ", date";
				$db =& JFactory::getDBO();
				$query = "";
				if($filter_date_comparator == "equal")
					$query = "SELECT metadata_id FROM #__easysdi_product WHERE update_date like '".$filter_date."%' AND published = 1".$filter; 
				if($filter_date_comparator == "different")
					$query = "SELECT metadata_id FROM #__easysdi_product WHERE update_date not like '".$filter_date."%' AND published = 1".$filter; 
				if($filter_date_comparator == "greaterorequal")
					$query = "SELECT metadata_id FROM #__easysdi_product WHERE (update_date >= '".$filter_date."' OR update_date like '".$filter_date."%') AND published = 1".$filter; 
				if($filter_date_comparator == "smallerorequal")
					$query = "SELECT metadata_id FROM #__easysdi_product WHERE (update_date <= '".$filter_date."' OR update_date like '".$filter_date."%') AND published = 1".$filter; 
				echo "date query: ".$query." with comparator:".$filter_date_comparator;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrCswDateFilter = array();
				if(count($list_id == 0))
					$arrCswDateFilter[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrCswDateFilter[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrCswDateFilter != null)
				$arrFilterMd[] = $arrCswDateFilter;
			
			
			$propertyTitle="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString";
			/*$sortBy='
           				<ogc:SortProperty xmlns:ogc=\"http://www.opengis.net/ogc\">
							<ogc:PropertyName>title</ogc:PropertyName>
                			<ogc:SortOrder>ASC</ogc:SortOrder>
            			</ogc:SortProperty>
        			';
			*/
			
			
			// Filtre minimum: Produits publi�s
			// Si aucun filtre n'a renvoy� de r�sultat ou si aucun filtre n'a �t� demand�.
			$arrCswMinMd;
			if( !is_Array($arrPartnerMd) and $cswThemeFilter==null and !is_Array($arrCswDateFilter) and !is_Array($arrCswVisibleMd) and !is_Array($arrCswOrderableFilterMd))
			{
				//echo ", minimum";
				$db =& JFactory::getDBO();
				$query = "SELECT metadata_id FROM `#__easysdi_product` WHERE published=1".$filter;
				//echo "filtre minimum: ".$query;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrCswMinMd = array();
				if(count($list_id == 0))
					$arrCswMinMd[] = -1;
				if(count($list_id > 0)){
					$cswPartnerFilter .= "<Or>";
				}
				foreach ($list_id as $md_id)
				{
					$arrCswMinMd[] = $md_id->metadata_id;
					$cswPartnerFilter .= "<PropertyIsEqualTo>
					<PropertyName>fileId</PropertyName>
					<Literal>$md_id->metadata_id</Literal>
					</PropertyIsEqualTo> ";
				}
				if(count($list_id > 0))
					$cswPartnerFilter .= "</Or>";
				$empty = false;
				
				//reinitialize the filters (this was a hack because php doesn't make any difference for:
				//s=null <=> s=""
				$cswPartnerFilter == "";
				$cswThemeFilter == "";
				$cswOrderableFilter == "";
				$cswVisibleFilter == "";
				$cswDateFilter == "";
			}
			if($arrCswMinMd != null)
				$arrFilterMd[] = $arrCswMinMd;
			
			// Filtre sur le texte (Crit�res de recherche simple)
			$cswSimpleTextFilter;
			if ($simple_filterfreetextcriteria || $empty)		
			{		
				//echo ", recherche simple:".$simple_filterfreetextcriteria."  ";
				//<ogc:PropertyName>any</ogc:PropertyName>
				
				$kwords = explode(" ", $simple_filterfreetextcriteria);
				
				//Break the space in the request and split it in many terms
				if(count($kwords > 0))
					$cswSimpleTextFilter .= "<ogc:And>";
				foreach ($kwords as $word) {
					$cswSimpleTextFilter .= "
				  <ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
				  <ogc:PropertyName>any</ogc:PropertyName>
				    <ogc:Literal>$word%</ogc:Literal>
				  </ogc:PropertyIsLike>\r\n";
				}
				if(count($kwords > 0))
					$cswSimpleTextFilter .= "</ogc:And>";
				
				/*
				$cswSimpleTextFilter = "
				  <ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
				  <ogc:PropertyName>any</ogc:PropertyName>
				    <ogc:Literal>$simple_filterfreetextcriteria%</ogc:Literal>
				  </ogc:PropertyIsLike>\r\n";
				  */
			}
			
			//Appending conditions to filter 2
			//Filter array are divided, conquer them
			$arrSearchableMd = Array();
			
			//echo "<br>filtermd";
			//print_r($arrFilterMd);
			//echo "<br>";

			for ($i=0; $i < count($arrFilterMd); $i++)
			{
				if($i == 0){
					$arrSearchableMd = $arrFilterMd[$i];
					continue;
				}
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrFilterMd[$i]);
			}
						
			//Build the filter
			
			$cswfilterCond = "";
			//Don't put an <and> if no other condition is requested
			if($cswSimpleTextFilter != "" || $cswThemeFilter != "" || $bboxfilter != "")
				$cswfilterCond .= "<ogc:And>\r\n";
			$cswfilterCond .= $cswSimpleTextFilter;
			$cswfilterCond .= $cswThemeFilter;
			$cswfilterCond .= $bboxfilter;
			
			if(count($arrSearchableMd) > 1)
				$cswfilterCond .= "<ogc:Or>\r\n";
			foreach ($arrSearchableMd as $md_id)
			{
				//keep it so to keep the request "small"
				$cswfilterCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$md_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
			}
			if(count($arrSearchableMd) > 1)
				$cswfilterCond .= "</ogc:Or>\r\n";
			
			//Don't put an <and> if no other condition is requested
			if($cswSimpleTextFilter != "" || $cswThemeFilter != "" || $bboxfilter != "")
			$cswfilterCond .= "</ogc:And>\r\n";
			
			$cswfilter = "<ogc:Filter xmlns=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
			$cswfilter .= $cswfilterCond;
			$cswfilter .= "</ogc:Filter>\r\n";
			
			//echo "cswfilter:". $cswfilter;
			
			//.$sortBy;
			
			//		BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder)
			$xmlBody = SITE_catalog::BuildCSWRequest(1, 1, "datasetcollection dataset application service", "full", "1.1.0", $cswfilter, "title", "ASC");
			$postResult;
			//Get the result from the server, only for count
			$xmlResponse = SITE_catalog::PostXMLRequest($catalogUrlBase,$xmlBody);

			$cswResults= simplexml_load_string($xmlResponse);
			
			//echo "dump the file: <br/><br/><br/>";
			//echo $cswResults->asXML();
			//echo "<br/> end dump the file";
			
			if ($cswResults !=null){
				$total = 0;
				foreach($cswResults->children("http://www.opengis.net/cat/csw/2.0.2")->SearchResults->attributes() as $a => $b) {
					if ($a=='numberOfRecordsMatched'){
						$total = $b;
					}
				}
                	
				$pageNav = new JPagination($total,$limitstart,$limit);
				
				// S�paration en n �l�ments par page
				$xmlBody = SITE_catalog::BuildCSWRequest($limit, $limitstart+1, "datasetcollection dataset application service", "full", "1.1.0", $cswfilter, "", "");
				
				//Get the result from the server
				//echo $xmlBody;
				
				$xmlResponse = SITE_catalog::PostXMLRequest($catalogUrlBase,$xmlBody);

				//echo "<hr>".$catalogUrlGetRecordsMD."<br>";
				$cswResults = DOMDocument::loadXML($xmlResponse);
                	
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
		}
		HTML_catalog::listCatalogContentWithPan($pageNav,$cswResults,$option,$total,$simple_filterfreetextcriteria,$maxDescr);
		
	}
	
	//$maxRecords == 0 => no limit
	//$typeNames ex: gmd:MD_Metadata
	//$elementSetName ex: full
	//$constraintVersion ex. 1.1.0
	//$filter = a valid filter string
	//$startPosition: integer
	//$sortBy: [dc:title|dct:abstract|ows:BoundingBox]
	//$sortOrder: ASC
	
	
	function BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder){
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		$req .= "\r\n";
		
		//Get Records section
		$req .=  "<csw:GetRecords xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\" resultType=\"results\" outputSchema=\"csw:IsoRecord\" ";
		
		// add max records if not 0
		if($maxRecords != 0)
			$req .= "maxRecords=\"".$maxRecords."\" ";
		
		//add start position
		if($startPosition != 0)
			$req .= "startPosition=\"".$startPosition."\" ";
		
		$req .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xsi:schemaLocation=\"http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd\">\r\n";
	
		//Query section
		//Types name
		$req .= "<csw:Query typeNames=\"".$typeNames."\">\r\n";
		//ElementSetName
		$req .= "<csw:ElementSetName>".$elementSetName."</csw:ElementSetName>\r\n";
		//ConstraintVersion
		$req .="<csw:Constraint version=\"".$constraintVersion."\">\r\n";
		//filter
		$req .= $filter."\r\n";
		
		$req .= "</csw:Constraint>\r\n";
		
		//Sort by
		if($sortBy != "" && $sortOrder != ""){
			$req .= "<ogc:SortBy>";
			$req .= "<ogc:SortProperty>";
			$req .= "<ogc:PropertyName>".$sortBy."</ogc:PropertyName>";
			$req .= "<ogc:SortOrder>".$sortOrder."</ogc:SortOrder>";
			$req .= "</ogc:SortProperty>";
			$req .= "</ogc:SortBy>";
		}
		
		
		$req .= "</csw:Query>\r\n";
		$req .= "</csw:GetRecords>\r\n";
		
		return $req;
	}
	
	function PostXMLRequest($url,$xmlBody){
		//$args = http_build_query($array);
		$url = parse_url($url);
		if(isset($url['port'])){
			$port = $url['port'];
		}else{
			$port = 80;
		}
		//could not open socket
		if (!$fp = fsockopen ($url['host'], $port, $errno, $errstr)){
			//$out = false;
		}
		//socket ok
		else{
			//$size = strlen($args);
			$size = strlen($xmlBody);
			$request = "POST ".$url['path']." HTTP/1.1\n";
			$request .= "Host: ".$url['host']."\n";
			//add auth header if necessary
			if(isset($url['user']) && isset($url['pass'])){
			   $user = $url['user'];
			   $pass = $url['pass'];
			   $request .= "Authorization: Basic ".base64_encode("$user:$pass")."\n";
			}
			$request .= "Connection: Close\r\n";
			$request .= "Content-type: application/x-www-form-urlencoded\n";
			$request .= "Content-length: ".$size."\n\n";
			$request .= $xmlBody."\n";
			//send req
			$fput = fputs($fp, $request);
			//read response, do only send back the xml part, not the headers
			$strResponse = "";
			while (!feof($fp)) {
			   $strResponse .= fgets($fp, 128);
			}
			$out = strstr($strResponse, '<?xml');
			
			fclose ($fp);
		}
		return $out;
	}

}
?>