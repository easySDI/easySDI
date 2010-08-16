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
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		$language =& JFactory::getLanguage();
		
		$newTraductions = array();
		$database->setQuery( "SELECT t.element_guid, t.label, t.defaultvalue, t.information, t.regexmsg, t.title, t.content FROM #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang c WHERE t.language_id=l.id AND l.codelang_id=c.id AND c.code='".$language->_lang."'" );
		$newTraductions = array_merge( $newTraductions, $database->loadObjectList() );
		
		$array = array();
		foreach ($newTraductions as $newTraduction)
		{
			if ($newTraduction->label <> "" and $newTraduction->label <> null)
				$array[strtoupper($newTraduction->element_guid."_LABEL")] = $newTraduction->label;
			
			if ($newTraduction->defaultvalue <> "" and $newTraduction->defaultvalue <> null)
				$array[strtoupper($newTraduction->element_guid."_DEFAULTVALUE")] = $newTraduction->defaultvalue;
			
			if ($newTraduction->information <> "" and $newTraduction->information <> null)
				$array[strtoupper($newTraduction->element_guid."_INFORMATION")] = $newTraduction->information;
			
			if ($newTraduction->regexmsg <> "" and $newTraduction->regexmsg <> null)
				$array[strtoupper($newTraduction->element_guid."_REGEXMSG")] = $newTraduction->regexmsg;
			
			if ($newTraduction->title <> "" and $newTraduction->title <> null)
				$array[strtoupper($newTraduction->element_guid."_TITLE")] = $newTraduction->title;
			
			if ($newTraduction->content <> "" and $newTraduction->content <> null)
				$array[strtoupper($newTraduction->element_guid."_CONTENT")] = $newTraduction->content;
		}
		$language->_strings = array_merge( $language->_strings, $array);
		
		$context= JRequest::getVar('context');
		$listSearchFilters = array();
		$database->setQuery("SELECT r.*, a.id as attribute_id, at.code as attributetype_code
					   FROM #__sdi_context c 
								  INNER JOIN #__sdi_relation_context rc ON c.id=rc.context_id 
								  INNER JOIN #__sdi_relation r ON r.id=rc.relation_id
								  INNER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  INNER JOIN #__sdi_list_attributetype at ON at.id=a.attributetype_id
					   WHERE c.code='".$context."' 
					   ORDER BY r.ordering");
		$listSearchFilters = array_merge( $listSearchFilters, $database->loadObjectList() );		
		//print_r($listSearchFilters);
		$empty = true;
		
		$maxDescr = config_easysdi::getValue("description_length");
		$MDPag = config_easysdi::getValue("pagination_metadata");
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.2&resultType=results&namespace=csw:http://www.opengis.net/cat/csw/2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0";
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
		//$account_id  = JRequest::getVar('account_id');
		$objecttype_id = JRequest::getVar('objecttype_id[]');
		
		$cswAdvancedFilter="";
		$cswAdvancedFilter .= "<ogc:Or>";
		foreach($listSearchFilters as $searchFilter)
		{
			$filter = JRequest::getVar('filter_'.$searchFilter->name);
			//echo "<br>".'filter_'.$searchFilter->name.": ".$filter ;
			if ($filter <> "")
			{
				switch ($searchFilter->attributetype_code)
				{
					case "guid":
					case "text":
					case "locale":
					case "number":
					case "link":
					case "textchoice":
					case "localechoice":
						/* Fonctionnement texte*/
						//Break the space in the request and split it in many terms
						$cswAdvancedFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<ogc:PropertyName>$searchFilter->lucenesearchfilter</ogc:PropertyName>
						<ogc:Literal>$filter</ogc:Literal>
						</ogc:PropertyIsLike> ";
						
						$empty = false;
						break;
					case "list":
						/* Fonctionnement liste*/
						$cswAdvancedFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<ogc:PropertyName>$searchFilter->lucenesearchfilter</ogc:PropertyName>
						<ogc:Literal>$filter</ogc:Literal>
						</ogc:PropertyIsLike> ";
						
						$empty = false; 
						break;
					case "date":
					case "datetime":
						/* Fonctionnement période*/
						$cswAdvancedFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
						<ogc:PropertyName>$searchFilter->lucenesearchfilter</ogc:PropertyName>
						<ogc:Literal>$filter</ogc:Literal>
						</ogc:PropertyIsLike> ";
						
						$empty = false; 
						break;
					default:
						break;
				}
			}
		}
		$cswAdvancedFilter .= "</ogc:Or>";
		
		//echo "<hr>".htmlspecialchars($cswAdvancedFilter);
			
		//$filter_theme = JRequest::getVar('filter_theme');
		//$filter_visible = JRequest::getVar('filter_visible');
		//$filter_orderable = JRequest::getVar('filter_orderable');
		/*$filter_createdate = JRequest::getVar('create_cal');
		$filter_createdate_comparator = JRequest::getVar('create_select');
		$filter_date = JRequest::getVar('update_cal');
		$filter_date_comparator = JRequest::getVar('update_select');
		*/
		/* Todo, push the date format in EasySDI config and
		set it here accordingly */
		/*if($filter_date){
			$temp = explode(".", $filter_date);
			$filter_date = $temp[2]."-".$temp[1]."-".$temp[0];
		}*/
		
		// Conditions pour la visibilit? publique/priv?e de la m?tadonn?e
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');
				
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		
		$account = new accountByUserId($db);
		
		if (!$user->guest){
			$account->load($user->id);
		}else{
			$account->id = 0;
		}
		
		//do not display result if no request is done
		//(simple_filterfreetextcriteria or filterfreetextcriteria have not been hit once)
		//print_r($_GET);
		//echo "<br><br>";
		$display_internal_orderable = false;
		if(isset($_GET['simple_search_button']) || isset($_GET['limitstart']))
		{
			$filter = "";
			if($account->id == 0)
			{
				//No user logged, display only external products
				$filter .= " AND (o.visibility_id=1) ";
			}
			else
			{
				// User logged in. (Return also products of the root's current account)
				$filter .= " AND (o.visibility_id=1
						OR
						(o.visibility_id =2 AND
						(o.account_id =  $account->id
						OR
						o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
						OR 
						o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
						OR
						o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
						
						))) ";
						$display_internal_orderable =true;
			}
						
			// Pour chaque requ?te qui r?cup?re des id de m?tadonn?es selon certains crit?res sur le produit, il faut tester que le produit est publi?
			// On rempli un tableau avec les m?tadonn?es recherchable, on traverse les filtres et on ajoute ou retire du tableau les id n?cessaires
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
			
			/*$cswThemeFilter = null;
			// Filtre sur la th?matique (Crit?res de recherche avanc?s)
			if($filter_theme)
			{
				//echo ", theme";
				$cswThemeFilter = "<PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
				<PropertyName>any</PropertyName>
				<Literal>$filter_theme</Literal>
				</PropertyIsLike> ";
				
				$empty = false;
			}
			*/
			/*
			// Filtre sur l'id du fournisseur (Crit?res de recherche avanc?s)
			$arrFilterMd =array();
			$arrAccountMd=null;
			if( $account_id )
			{
				//echo ", account";
				$db =& JFactory::getDBO();
				$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.published=1 AND o.account_id = ".$account_id.$filter;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrAccountMd = array();
				//If no result, give an unexisting id back
				if(count($list_id == 0))
					$arrAccountMd[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrAccountMd[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrAccountMd != null)
				$arrFilterMd[] = $arrAccountMd;
			*/	
			
			// Filtre sur le type d'objet (Crit?res de recherche simple)
			$arrObjecttypeMd=null;
			if( $objecttype_id )
			{
				//echo ", objecttype";
				$db =& JFactory::getDBO();
				$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.published=1 AND o.objecttype_id IN (".implode(",", $objecttype_id).") ".$filter;
				$db->setQuery( $query);
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrObjecttypeMd = array();
				//If no result, give an unexisting id back
				if(count($list_id == 0))
					$arrObjecttypeMd[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrObjecttypeMd[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrObjecttypeMd != null)
				$arrFilterMd[] = $arrObjecttypeMd;
				
			
			/*$arrCswVisibleMd=null;
			// Filtre sur la visibilit? du produit (Crit?res de recherche avanc?s)
			if($filter_visible )
			{	
				//echo ", object visibily";
				$db =& JFactory::getDBO();
				$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.previewWmsUrl != '' AND o.published=1".$filter; //
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
			*/
			/*$arrCswOrderableFilterMd = null;
			// Filtre sur la possibilit? de commander le produit (Crit?res de recherche avanc?s)
			if($filter_orderable)
			{
				//echo ", orderable";
				$db =& JFactory::getDBO();
				$query = "";
				if($display_internal_orderable)
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.orderable = 1 AND o.published = 1 AND o.visibility_id=2".$filter; 
				else
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.orderable = 1 AND o.published = 1 AND o.visibility_id=1".$filter;
				
				
				$db->setQuery( $query);
				//echo "<br>".$db->getQuery()."<br>";
				$list_id = $db->loadObjectList() ;
				//print_r($list_id);echo "<hr>";
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
			*/
			/*	
			// Filtre sur la date de création
			$arrCswCreateDateFilter=null;
			if($filter_createdate)
			{
				//echo ", date";
				$db =& JFactory::getDBO();
				$query = "";
				if($filter_createdate_comparator == "equal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.created like '".$filter_createdate."%' AND o.published = 1".$filter; 
				if($filter_createdate_comparator == "different")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.created not like '".$filter_createdate."%' AND o.published = 1".$filter; 
				if($filter_createdate_comparator == "greaterorequal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND (o.created >= '".$filter_createdate."' OR o.created like '".$filter_createdate."%') AND o.published = 1".$filter; 
				if($filter_createdate_comparator == "smallerorequal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND (o.created <= '".$filter_createdate."' OR o.created like '".$filter_createdate."%') AND o.published = 1".$filter; 
				//echo "date query: ".$query." with comparator:".$filter_date_comparator;
				$db->setQuery( $query);
				//echo "<br>".$db->getQuery()."<br>";
				$list_id = $db->loadObjectList() ;
				if ($db->getErrorNum())
				{
					echo "<div class='alert'>";
					echo 	$db->getErrorMsg();
					echo "</div>";
				}
				$arrCswCreateDateFilter = array();
				if(count($list_id == 0))
					$arrCswCreateDateFilter[] = -1;
				foreach ($list_id as $md_id)
				{
					$arrCswCreateDateFilter[] = $md_id->metadata_id;
				}
				$empty = false;
			}
			if($arrCswCreateDateFilter != null)
				$arrFilterMd[] = $arrCswCreateDateFilter;
			*/
			/*
			// Filtre sur la date de mise à jour
			$arrCswDateFilter=null;
			if($filter_date)
			{
				//echo ", date";
				$db =& JFactory::getDBO();
				$query = "";
				if($filter_date_comparator == "equal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.updated like '".$filter_date."%' AND o.published = 1".$filter; 
				if($filter_date_comparator == "different")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.updated not like '".$filter_date."%' AND o.published = 1".$filter; 
				if($filter_date_comparator == "greaterorequal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND (o.updated >= '".$filter_date."' OR o.updated like '".$filter_date."%') AND o.published = 1".$filter; 
				if($filter_date_comparator == "smallerorequal")
					$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND (o.updated <= '".$filter_date."' OR o.updated like '".$filter_date."%') AND o.published = 1".$filter; 
				//echo "date query: ".$query." with comparator:".$filter_date_comparator;
				$db->setQuery( $query);
				//echo "<br>".$db->getQuery()."<br>";
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
			*/
			
			// Filtre minimum: Produits publi?s avec métadonnée publiée et date de publication >= aujourd'hui
			// Si aucun filtre n'a renvoy? de r?sultat ou si aucun filtre n'a ?t? demand?.
			$arrCswMinMd=null;
			if( !is_Array($arrAccountMd) and !is_Array($arrCswDateFilter) and !is_Array($arrCswVisibleMd) and !is_Array($arrCswOrderableFilterMd))
			{
				//echo ", minimum";
				$db =& JFactory::getDBO();
				$query = "SELECT m.guid as metadata_id FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.published=1 ".$filter;
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
				$cswAccountFilter=null;
				if(count($list_id == 0))
					$arrCswMinMd[] = -1;
				if(count($list_id > 0)){
					$cswAccountFilter .= "<Or>";
				}
				foreach ($list_id as $md_id)
				{
					$arrCswMinMd[] = $md_id->metadata_id;
					$cswAccountFilter .= "<PropertyIsEqualTo>
					<PropertyName>fileId</PropertyName>
					<Literal>$md_id->metadata_id</Literal>
					</PropertyIsEqualTo> ";
				}
				if(count($list_id > 0))
					$cswAccountFilter .= "</Or>";
				$empty = false;
				
				//reinitialize the filters (this was a hack because php doesn't make any difference for:
				//s=null <=> s=""
				$cswAccountFilter = "";
				//$cswThemeFilter = "";
				$cswOrderableFilter = "";
				$cswVisibleFilter = "";
				$cswDateFilter = "";
			}
			if($arrCswMinMd != null)
				$arrFilterMd[] = $arrCswMinMd;
			
			// Filtre sur le texte (Crit?res de recherche simple)
			$cswSimpleTextFilter="";
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
				 <ogc:Or>
				 <ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
				  <ogc:PropertyName>title</ogc:PropertyName>
				    <ogc:Literal>$word%</ogc:Literal>
				  </ogc:PropertyIsLike>\r\n
				  <ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
				  <ogc:PropertyName>metadata_title</ogc:PropertyName>
				    <ogc:Literal>$word%</ogc:Literal>
				  </ogc:PropertyIsLike>\r\n
				 <ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
				  <ogc:PropertyName>abstract</ogc:PropertyName>
				    <ogc:Literal>$word%</ogc:Literal>
				  </ogc:PropertyIsLike>
				  </ogc:Or>\r\n";
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
			if($cswSimpleTextFilter != "" || $bboxfilter != "") // || $cswThemeFilter != "" )
				$cswfilterCond .= "<ogc:And>\r\n";
			$cswfilterCond .= $cswSimpleTextFilter;
			//$cswfilterCond .= $cswThemeFilter;
			$cswfilterCond .= $cswAdvancedFilter;
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
			if($cswSimpleTextFilter != "" || $bboxfilter != "") // || $cswThemeFilter != "")
			$cswfilterCond .= "</ogc:And>\r\n";
			
			$cswfilter = "<ogc:Filter xmlns=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
			$cswfilter .= $cswfilterCond;
			$cswfilter .= "</ogc:Filter>\r\n";
			
			echo "cswfilter:". htmlspecialchars($cswfilter);
			
			//$propertyTitle="gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title/gmd:LocalisedCharacterString";
			/*$sortBy='
           				<ogc:SortProperty xmlns:ogc=\"http://www.opengis.net/ogc\">
							<ogc:PropertyName>title</ogc:PropertyName>
                			<ogc:SortOrder>ASC</ogc:SortOrder>
            			</ogc:SortProperty>
            			<ogc:SortProperty xmlns:ogc=\"http://www.opengis.net/ogc\">
							<ogc:PropertyName>metadata_title</ogc:PropertyName>
                			<ogc:SortOrder>ASC</ogc:SortOrder>
            			</ogc:SortProperty>
        			';
			*/
			//.$sortBy;
			
			// BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder)
			$xmlBody = SITE_catalog::BuildCSWRequest(10, 1, "datasetcollection dataset application service", "full", "1.1.0", $cswfilter, "title", "ASC");
			$postResult;
			
			//Get the result from the server, only for count
			/*
			$myFile = "/home/sites/joomla.asitvd.ch/web/components/com_easysdi_shop/core/myFile_cat.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		
			fwrite($fh, $xmlBody);
			fwrite($fh, $xmlResponse);
			
			fclose($fh);
			*/
			$xmlResponse = ADMIN_metadata::PostXMLRequest($catalogUrlBase,$xmlBody);
			$cswResults= simplexml_load_string($xmlResponse);
			//echo var_dump($cswResults->saveXML())."<br>";
			$myDoc = new DomDocument();
			$myDoc->loadXML($xmlBody);
			$myDoc->save("C:\\RecorderWebGIS\\searchRequest.xml");
			$myDoc = new DomDocument();
			$myDoc->loadXML($cswResults->asXML());
			$myDoc->save("C:\\RecorderWebGIS\\searchResult.xml");
			//echo "dump the file: <br/><br/><br/>";
			//echo $cswResults->asXML();
			//echo "<br/> end dump the file";
			
			$total = 0;
			if ($cswResults !=null)
			{
				foreach($cswResults->children("http://www.opengis.net/cat/csw/2.0.2")->SearchResults->attributes() as $a => $b) 
				{
					if ($a=='numberOfRecordsMatched')
					{
						$total = $b;
					}
				}
                	
				$pageNav = new JPagination($total,$limitstart,$limit);
				
				// S?paration en n ?l?ments par page
				$xmlBody = SITE_catalog::BuildCSWRequest($limit, $limitstart+1, "datasetcollection dataset application service", "full", "1.1.0", $cswfilter, "title", "ASC");
				//Get the result from the server
				//echo $xmlBody;
				
				$xmlResponse = ADMIN_metadata::PostXMLRequest($catalogUrlBase,$xmlBody);

				//echo "<hr>".$catalogUrlGetRecordsMD."<br>";
				$cswResults = DOMDocument::loadXML($xmlResponse);
                	//echo var_dump($cswResults->saveXML())."<br>";
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
		else
		{
			$total=0;
			$pageNav=new JPagination($total,$limitstart,$limit);
			$cswResults = null;
		}
		
		$allVersions=true;
		
		HTML_catalog::listCatalogContentWithPan($pageNav,$cswResults,$option,$total,$simple_filterfreetextcriteria,$maxDescr, $allVersions);
		
	}

	function BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder)
	{
		//Bug: If we have accents, we must specify ISO-8859-1
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$req .= "";
		
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
		
		return utf8_encode($req);
	}
	
	function PostXMLRequest($url,$xmlBody){
		$url = parse_url($url);
		//$url=parse_url("http://demo.easysdi.org:8080/proxy/ogc/geonetwork");
		
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
			$request .= "Content-type: application/xml\n";
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