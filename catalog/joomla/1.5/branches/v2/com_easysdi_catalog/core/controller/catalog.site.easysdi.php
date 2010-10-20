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
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		$language =& JFactory::getLanguage();
		
		// Valeurs de configuration pour le rendu des r�sultats
		$maxDescr = config_easysdi::getValue("description_length");
		$MDPag = config_easysdi::getValue("pagination_metadata");
		
		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', $MDPag );
		$limitstart = JRequest::getVar('limitstart', 0 );
		
		// R�cup�ration de toutes les traductions pour la construction des crit�res de recherche,
		// sp�cialement ceux sous forne de liste dont le contenu doit �tre traduit
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
		
		// Liste des crit�res de recherche simple
		$context= JRequest::getVar('context');
		$listSimpleFilters = array();
		$database->setQuery("SELECT sc.*, r.guid as relation_guid, a.id as attribute_id, at.code as attributetype_code, sc.code as criteria_code, rt.code as rendertype_code
					   FROM #__sdi_searchcriteria sc
					   			  LEFT OUTER JOIN #__sdi_relation r ON r.id=sc.relation_id
								  LEFT OUTER JOIN #__sdi_relation_context rc ON r.id=rc.relation_id 
								  LEFT OUTER JOIN #__sdi_context c ON c.id=rc.context_id
								  LEFT OUTER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  LEFT OUTER JOIN #__sdi_list_attributetype at ON at.id=a.attributetype_id
								  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON sc_tab.searchcriteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_context c_tab ON sc_tab.context_id=c_tab.id
								  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id
								  LEFT OUTER JOIN #__sdi_list_rendertype rt ON sc.rendertype_id=rt.id
					   WHERE (sc.relation_id IS NULL
					   		 OR c.code='".$context."')
					   		 AND tab.code = 'simple' 
					   ORDER BY sc.ordering");
		//echo $database->getQuery()."<br>";
		$listSimpleFilters = array_merge( $listSimpleFilters, $database->loadObjectList() );		
		//print_r($listSimpleFilters);echo "<hr>";
		
		// Liste des crit�res de recherche avanc�s
		$listAdvancedFilters = array();
		/*$database->setQuery("SELECT sc.*, r.*, a.id as attribute_id, at.code as attributetype_code
					   FROM #__sdi_context c 
								  INNER JOIN #__sdi_relation_context rc ON c.id=rc.context_id 
								  INNER JOIN #__sdi_relation r ON r.id=rc.relation_id
								  INNER JOIN #__sdi_searchcriteria sc ON r.id=sc.relation_id
								  INNER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  INNER JOIN #__sdi_list_attributetype at ON at.id=a.attributetype_id
					   WHERE c.code='".$context."'
					         AND sc.advancedtab = 1 
					   ORDER BY r.ordering");*/
		$database->setQuery("SELECT sc.*, r.guid as relation_guid, a.id as attribute_id, at.code as attributetype_code, sc.code as criteria_code, rt.code as rendertype_code
					   FROM #__sdi_searchcriteria sc
					   			  LEFT OUTER JOIN #__sdi_relation r ON r.id=sc.relation_id
								  LEFT OUTER JOIN #__sdi_relation_context rc ON r.id=rc.relation_id 
								  LEFT OUTER JOIN #__sdi_context c ON c.id=rc.context_id
								  LEFT OUTER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  LEFT OUTER JOIN #__sdi_list_attributetype at ON at.id=a.attributetype_id
								  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON sc_tab.searchcriteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_context c_tab ON sc_tab.context_id=c_tab.id
								  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id
								  LEFT OUTER JOIN #__sdi_list_rendertype rt ON sc.rendertype_id=rt.id
					   WHERE (sc.relation_id IS NULL
					   		 OR c.code='".$context."')
					   		 AND tab.code = 'advanced' 
					   ORDER BY sc.ordering");
		//echo $database->getQuery()."<br>";
		$listAdvancedFilters = array_merge( $listAdvancedFilters, $database->loadObjectList() );		
		//print_r($listAdvancedFilters);echo "<hr>";
		
		// Flag pour d�terminer s'il y a ou pas des filtres
		$empty = true;
		$condList = array();
		
		// Construction de la requ�te pour r�cup�rer les r�sultats
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		//$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.2&resultType=results&namespace=csw:http://www.opengis.net/cat/csw/2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0";
		$xmlHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		//$catalogUrlGetRecordsCount =  $catalogUrlGetRecords . "&startPosition=1&maxRecords=1";

		// R�cup�ration des filtres standards
		//$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
		//$account_id  = JRequest::getVar('account_id');
		//$objecttype_id = JRequest::getVar('objecttype_id[]');
		
		// Construction de la bbox obligatoire
		$minX = JRequest::getVar('bboxMinX', "-180" );
		$minY = JRequest::getVar('bboxMinY', "-90" );
		$maxX = JRequest::getVar('bboxMaxX', "180" );
		$maxY = JRequest::getVar('bboxMaxY', "90" );
		
		// Tableau contenant les guid des m�tadonn�es sur lesquelles la recherche va porter
		$arrFilterMd = array();
		
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
		
		$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
							
		// Selon que l'utilisateur est logg� ou pas, on ne fera pas la recherche sur le m�me set de m�tadonn�es
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.usermanager.class.php');

		// R�cup�ration du compte, si l'utilisateur est logg�
		$account = new accountByUserId($database);
		if (!$user->guest){
			$account->load($user->id);
		}else{
			$account->id = 0;
		}
		
		/* Construction de la requ�te de recherche */
		// Ne retourner des r�sultats que si l'utilisateur a soumis une requ�te
		// ou qu'un contexte est d�fini
		if(isset($_GET['simple_search_button']) || isset($_GET['limitstart']) || isset($_GET['context']))
		{
			// Si aucun utilisateur n'est logg�, ne retourner que les m�tadonn�es publiques
			if($account->id == 0)
			{
				//No user logged, display only external products
				$mysqlFilter = " AND (v.code='public') ";
			}
			else
			{
				// Si l'utilisateur est logg�, retourner toutes les m�tadonn�es publiques
				// + les m�tadonn�es priv�es � son compte racine
				$mysqlFilter = " AND 
							(
								v.code='public'
								OR
								(
									v.code='private' AND
									(
										o.account_id =  $account->id
										OR
										o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
										OR 
										o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
										OR
										o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
									)
								)
								OR
								(
									v.code='protected' AND
									(
										o.account_id =  $account->id
										OR
										o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
										OR 
										o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
										OR
										o.account_id  IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ) 
									)
								)
							) ";
			}

			// Construction du filtre sur la BBOX
			if ($minX == "-180" && $minY == "-90" && $maxX == "180" && $maxY == "90")
			{
				$bboxfilter ="";
			}
			else
			{
				$bboxfilter ="<ogc:BBOX>
								<ogc:PropertyName>ows:BoundingBox</ogc:PropertyName>
								<gml:Envelope>
									<gml:lowerCorner>$minY $minX</gml:lowerCorner>
									<gml:upperCorner>$maxY $maxX</gml:upperCorner>
								</gml:Envelope>
							  </ogc:BBOX>";
			}
			
			if ($bboxfilter <> "")
				$condList[]=$bboxfilter;
			//echo"CondList BBox: <br>"; print_r($condList); echo"<br>";
			// Listes qui vont potentiellement contenir des guid de m�tadonn�es
			$arrFreetextMd = array();
			$arrObjectNameMd = array();
			$arrAccountsMd = array();
			$arrManagersMd = array();
			$arrCreatedMd = array();
			$arrPublishedMd = array();
				
			// Construction des filtres bas�s sur l'onglet simple
			$cswSimpleFilter="";
			$countSimpleFilters = 0;
			foreach($listSimpleFilters as $searchFilter)
			{
				$filter = JRequest::getVar('filter_'.$searchFilter->guid);
				//echo "<br>".'filter_'.$searchFilter->guid.": ".$mysqlFilter ;
				$lowerFilter = JRequest::getVar('create_cal_'.$searchFilter->guid);
				$upperFilter = JRequest::getVar('update_cal_'.$searchFilter->guid);
							
				if (isset($_GET['filter_'.$searchFilter->guid]) or isset($_GET['create_cal_'.$searchFilter->guid]) or isset($_GET['update_cal_'.$searchFilter->guid]))
				{
					switch ($searchFilter->attributetype_code)
					{
						case "guid":
						case "text":
						case "locale":
						case "number":
						case "link":
							$countSimpleFilters++;
							/* Fonctionnement texte*/
							//Break the space in the request and split it in many terms
							$kwords = explode(" ", trim($filter));
							//echo ", ".count($kwords).",  "; print_r($kwords);echo "<hr>";
							
							$terms=0;
							$cswTerm="";
							foreach ($kwords as $word) 
							{
								if ($word <> "")
								{
									$cswTerm .= "
									 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
												<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>%$word%</ogc:Literal>
											</ogc:PropertyIsLike>\r\n
										";
									$terms++;
								}
							}
							
							// R�unir les crit�res
							if ($terms > 1)
								$cswTerm = "<ogc:And>".$cswTerm."</ogc:And>";
							
							$cswSimpleFilter.=$cswTerm;
							$empty = false;
							break;
						case "textchoice":
						case "localechoice":
							/* Fonctionnement liste de choix*/
							if (count($filter) > 0 and $filter[0] <> "")
							{
								$countSimpleFilters++;
								foreach($filter as $f)
								{
									$localechoice ="";
									$list = array();
									$database->setQuery( "SELECT t.content FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id AND (at.code='textchoice' OR at.code='localechoice') AND c.id=".$f." AND c.published=true ORDER BY c.name" );
									$list = $database->loadObjectList();
	
									foreach ($list as $element)
									{
										$localechoice .= "
											<ogc:PropertyIsEqualTo>
												<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>$element->content</ogc:Literal>
											</ogc:PropertyIsEqualTo>
										";
									}
									if (count($list) > 1)
										$localechoice = "<ogc:Or>".$localechoice."</ogc:Or>";
									$cswSimpleFilter .= $localechoice;
								}
								if (count($filter) > 1)
									$cswSimpleFilter = "<ogc:Or>".$cswSimpleFilter."</ogc:Or>";
							}
							
							$empty = false;
							break;
						case "list":
							/* Fonctionnement liste*/
							if (count($filter) > 0 and $filter[0] <> "")
							{
								$countSimpleFilters++;
								foreach($filter as $f)
								{
									$cswSimpleFilter .= "<ogc:PropertyIsEqualTo>
									<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
									<ogc:Literal>$f</ogc:Literal>
									</ogc:PropertyIsEqualTo> ";
								}
								if (count($filter) > 1)
									$cswSimpleFilter = "<ogc:Or>".$cswSimpleFilter."</ogc:Or>";
							}
							
							$empty = false;
							break;
						case "date":
						case "datetime":
							/* Fonctionnement p�riode
							 * Format de date: 2001-01-15T20:07:48.11
							 * */
							//echo $lowerFilter."<br>";
							//echo $upperFilter."<br>";
							if ($lowerFilter == "") // Seulement la borne sup
							{
								$countSimpleFilters++;
								$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
								$cswSimpleFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:Literal>$upperFilter</ogc:Literal>
								</ogc:PropertyIsLessThanOrEqualTo> ";
							}
							else if ($upperFilter == "") // Seulement la borne inf
							{
								$countSimpleFilters++;
								$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
								$cswSimpleFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:Literal>$lowerFilter</ogc:Literal>
								</ogc:PropertyIsGreaterThanOrEqualTo> ";
							}
							else // Les deux bornes
							{
								$countSimpleFilters++;
								$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
								$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
								$cswSimpleFilter .= "<ogc:PropertyIsBetween>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:LowerBoundary>
									<ogc:Literal>$lowerFilter</ogc:Literal>
								</ogc:LowerBoundary>
								<ogc:UpperBoundary>
									<ogc:Literal>$upperFilter</ogc:Literal>
								</ogc:UpperBoundary>
								</ogc:PropertyIsBetween> ";
							}
							
							$empty = false; 
							break;
						default:
							break;
					}
				}
				else // Traiter les filtres qui ne sont pas li�s � des relations, si ils existent
				{
					if ($searchFilter->criteriatype_id == 1) // Filtres syst�mes
					{
						// R�cup�ration des filtres standards
						//$account_id  = JRequest::getVar('account_id');
						
						switch ($searchFilter->code)
						{
							case "fulltext":
								$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
								if ($simple_filterfreetextcriteria <> "")
								{
									$countSimpleFilters++;
									// Filtre sur le texte (Crit�res de recherche simple)
									//$cswFreeTextFilter="";
									//echo ", recherche simple:".$simple_filterfreetextcriteria."  ";
									
									$kwords = explode(" ", trim($simple_filterfreetextcriteria));
									//echo count($kwords).":<br>"; print_r($kwords);echo "<hr>";
									
									// Filtres OGC directs pour les champs titre(title), description(abstract) et GEMET (keyword)
									//Break the space in the request and split it in many terms
									$title=0;
									$keyword=0;
									$abstract=0;
									$cswFreeFilters = 0;
									
									$cswTitle="";
									$cswKeyword="";
									$cswAbstract="";
									
									foreach ($kwords as $word) 
									{
										if ($word <> "")
										{
											$cswTitle .= "
											 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
														<ogc:PropertyName>title</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>\r\n
												";
											$title++;
											$cswKeyword .= "
											 		<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
														<ogc:PropertyName>keyword</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>\r\n
												";
											$keyword++;
											$cswAbstract .= "
											 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
												     	<ogc:PropertyName>abstract</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>
												";
											$abstract++;
											
											$cswFreeFilters++;
										}
									}
									//echo $cswTitle."<br>";
									//echo $cswKeyword."<br>";
									//echo $cswAbstract."<br>";
									
									// R�unir chaque crit�re
									if ($title > 1)
									{
										$cswTitle = "<ogc:And>".$cswTitle."</ogc:And>";
										//$cswFreeFilters++;
									}
									if ($keyword > 1)
									{
										$cswKeyword = "<ogc:And>".$cswKeyword."</ogc:And>";
										//$cswFreeFilters++;
									}
									if ($abstract > 1)
									{
										$cswAbstract = "<ogc:And>".$cswAbstract."</ogc:And>";
										//$cswFreeFilters++;
									}
									
									// R�unir les trois crit�res
									if($cswFreeFilters > 0)
										$cswSimpleFilter = "<ogc:Or>".$cswTitle.$cswKeyword.$cswAbstract."</ogc:Or>";
									//print_r($cswSimpleFilter);echo "<br>";
								
									// Filtres sur les guid de m�tadonn�es pour le code et le fournisseur
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.name LIKE '%".$simple_filterfreetextcriteria."%' ";
									$database->setQuery( $query);
									//echo "<br>".$database->getQuery()."<br>";
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectnamelist = $database->loadObjectList() ;
										
										foreach ($objectnamelist as $on)
										{
											$arrFreetextMd[] = $on->metadata_id;
										
											$empty = false;
										}
									}
									
									// S�lectionner tous les objets dont le nom du fournisseur ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_account a ON a.id=o.account_id
											  WHERE a.name LIKE '%".$simple_filterfreetextcriteria."%' ";
									$database->setQuery( $query);
									//echo "<br>".$database->getQuery()."<br>";
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$accountlist = $database->loadObjectList() ;
										
										foreach ($accountlist as $a)
										{
											$arrFreetextMd[] = $a->metadata_id;
										
											$empty = false;
										}
									}
									
									//If no result, give an unexisting id back
									/*if(count($objectnamelist) == 0 and count($accountlist) == 0)
										$arrFreetextMd[] = -1;*/
								}
								break;
							case "objecttype":
								//$objecttype_id = JRequest::getVar('objecttype_id');
								$objecttype_id = JRequest::getVar($searchFilter->guid);
								
								// Construire la liste des guid � filtrer
								$arrObjecttypeMd = array();
								
								//echo "objecttype_id: ".count($objecttype_id);
								if (count($objecttype_id) > 0 )
								{
									//echo "<b>Cas1:</b><br>";
									//$countSimpleFilters++;
									$arrObjecttypeMd=null;
								
									//echo ", objecttype";
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.objecttype_id IN (".implode(",", $objecttype_id).") "
											.$mysqlFilter;
									$database->setQuery( $query);
									//echo "list_id:".$database->getQuery()."<hr>";
									$list_id = $database->loadObjectList() ;
									//echo "list_id:". htmlspecialchars($list_id)."<hr>";
						
									if ($database->getErrorNum())
									{
										echo "<div class='alert'>";
										echo 	$database->getErrorMsg();
										echo "</div>";
									}
									
								}
								else if (!array_key_exists('objecttype_id', $_GET) and !array_key_exists('bboxMinX', $_GET)) // Cas du premier appel. Rechercher sur tous les types
								{
									//echo "<b>Cas2:</b><br>";
									//$countSimpleFilters++;
									
									$objecttypes = array();
									if ($context <> "")
									{
										// R�cup�rer tous les types d'objets du contexte
										$database->setQuery("SELECT id FROM #__sdi_objecttype WHERE id IN
															(SELECT co.objecttype_id 
															FROM #__sdi_context_objecttype co
															INNER JOIN #__sdi_context c ON c.id=co.context_id 
															WHERE c.code = '".$context."')
													   ORDER BY name");
									}
									else
									{
										// R�cup�rer tous les types d'objets d�finis
										$database->setQuery("SELECT id FROM #__sdi_objecttype ORDER BY name");
									}
									//echo "objecttypes:".$database->getQuery()."<hr>";
									$objecttypes = $database->loadResultArray();
									
									// R�cup�rer toutes les m�tadonn�es de ces types d'objets
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.objecttype_id IN (".implode(",", $objecttypes).") "
											.$mysqlFilter;
									$database->setQuery( $query);
									//echo "list_id:".$database->getQuery()."<hr>";
									$list_id = $database->loadObjectList() ;
									//echo "list_id:". htmlspecialchars($list_id)."<hr>";
									if ($database->getErrorNum())
									{
										echo "<div class='alert'>";
										echo 	$database->getErrorMsg();
										echo "</div>";
									}
								}
								else if (count($objecttype_id) == 0)
								{
									$list_id=array();
									$arrObjecttypeMd[] = -1;
								}
								
								//If no result, give an unexisting id back
								/*if(count($list_id) == 0)
								{
									$arrObjecttypeMd[] = -1;
									break;
								}*/
								foreach ($list_id as $md_id)
								{
									$arrObjecttypeMd[] = $md_id->metadata_id;
								}
								
								if(count($list_id)> 0)
									$empty = false;
								
								//echo "arrObjecttypeMd:<br>"; print_r($arrObjecttypeMd);echo "<hr>";
								break;
							case "versions":
								//$versions = JRequest::getVar('versions');
								$versions = JRequest::getVar($searchFilter->guid);
								//print_r("<pre>".var_dump($versions)."</pre>");
								if ($versions == "0" or !array_key_exists($searchFilter->guid, $_GET)) // Cas du premier appel et des versions actuelles. Rechercher sur les versions actuelles publi�es � la date courante 
								{
									//$countSimpleFilters++;
									// Si l'utilisateur a choisi de ne chercher que sur les versions actuelles,
									// ajouter un filtre. Sinon ne rien faire
									//if ($versions == 0)
									//{
										//echo "<b>CasA:</b><br>";
										// S�lectionner tous les objets
										$query = "SELECT o.id 
												  FROM #__sdi_object o 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE 1 "
												.$mysqlFilter;
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectlist = $database->loadObjectList() ;
										//echo "objectlist:<br>";print_r($objectlist);echo "<hr>";
										// Construire la liste des guid � filtrer
										$arrVersionMd = array();
										
										// Pour chaque objet, s�lectionner toutes ses versions
										foreach ($objectlist as $object)
										{
											$query = "SELECT m.guid as metadata_id, ms.code, m.published
													  FROM #__sdi_objectversion ov 
													  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
													  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
													  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
													  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
													  WHERE o.id=".$object->id.
													 " ORDER BY ov.created DESC";
											$database->setQuery( $query);
											//echo "<br>".$database->getQuery()."<br>";
											$versionlist = $database->loadObjectList() ;
											//echo "versionlist:<br>";print_r($versionlist);echo "<hr>";
											
											if (count($versionlist))
											{
												//print_r($versionlist[0]);
												//echo "<br>";
												// Si la derni�re version est publi�e � la date courante, on l'utilise
												if ($versionlist[0]->code=='published'and $versionlist[0]->published <= date('Y-m-d'))
													$arrVersionMd[] = $versionlist[0]->metadata_id;
											
												$empty = false;
											}
										}
									//}
								}
								else if ($versions == "1") // Rechercher sur toutes les versions publi�es � la date courante
								{
									//$countSimpleFilters++;
									//echo "<b>CasB:</b><br>";
									// S�lectionner tous les objets
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.published=1 or o.published=0 "
											.$mysqlFilter;
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									$arrVersionMd = array();
									
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
												  WHERE ms.code='published'
												  		AND m.published <= '".date('Y-m-d')."' 
												  		AND o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$versionlist = $database->loadObjectList() ;
											
										foreach ($versionlist as $v)
										{
											//print_r($versionlist[0]);
											//echo "<br>";
											$arrVersionMd[] = $v->metadata_id;
											
											$empty = false;
										}
									}
								}
								
								//If no result, give an unexisting id back
								/*if(count($arrVersionMd)== 0)
								{
									$arrVersionMd[] = -1;
									break;
								}*/
								
								break;
							case "object_name":
								//$object_name = JRequest::getVar('object_name');
								$object_name = JRequest::getVar($searchFilter->guid);
								
								if ($object_name <> "")
								{
									$countSimpleFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.name LIKE '%".$object_name."%' ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectnamelist = $database->loadObjectList() ;
										
										foreach ($objectnamelist as $on)
										{
											$arrObjectNameMd[] = $on->metadata_id;
										
											$empty = false;
										}
									}
									
									//If no result, give an unexisting id back
									/*if(count($objectnamelist)== 0)
										$arrObjectNameMd[] = -1;*/
								}
								break;
							case "metadata_created":
								$lower = JRequest::getVar('create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countSimpleFilters++;
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created>='".$lower."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper <> "" and $lower <> "") // Les deux bornes
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created>='".$lower."'".
										 " 			AND m.created<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								break;
							case "metadata_published":
								$lower = JRequest::getVar('create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countSimpleFilters++;
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published>='".$lower."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if($upper <> "" and $lower <> "") // Les deux bornes
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published>='".$lower."'".
										 " 			AND m.published<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
									
									
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								break;
							case "managers":
								//$managers = JRequest::getVar('managers');
								$managers = JRequest::getVar($searchFilter->guid);
								
								if (count($managers) > 0 and $managers[0] <> "")
								{
									$countSimpleFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_manager_object mo ON mo.object_id=o.id
											  WHERE mo.account_id IN (".implode(", ", $managers).") ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$managerlist = $database->loadObjectList() ;
										
										foreach ($managerlist as $m)
										{
											$arrManagersMd[] = $m->metadata_id;
										
											$empty = false;
										}
									}
									
									//If no result, give an unexisting id back
									/*if(count($managerlist)== 0)
										$arrManagersMd[] = -1;*/
								}
								break;
							case "title":
								//$metadata_title = JRequest::getVar('title');
								$metadata_title = JRequest::getVar($searchFilter->guid);
								
								if ($metadata_title <> "")
								{
									$countSimpleFilters++;
									$cswSimpleFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
									<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
									<ogc:Literal>%$metadata_title%</ogc:Literal>
									</ogc:PropertyIsLike> ";
									
									$empty = false;
								}
								break;
							case "account_id":
								//$accounts = JRequest::getVar('account_id');
								$accounts = JRequest::getVar($searchFilter->guid);
								
								if (count($accounts) > 0 and $accounts[0] <> "")
								{
									$countSimpleFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  WHERE o.account_id IN (".implode(", ", $accounts).") ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$accountlist = $database->loadObjectList() ;
										
										
										foreach ($accountlist as $a)
										{
											$arrAccountsMd[] = $a->metadata_id;
										
											$empty = false;
										}
									}
									
									//If no result, give an unexisting id back
									/*if(count($accountlist)== 0)
										$arrAccountsMd[] = -1;*/		
								}
								break;
							default:
								break;
						}
					}
					else // Cas des attributs OGC qui ne sont pas li�s � une relation
					{
						switch ($searchFilter->rendertype_code)
						{
							case "date":
								$lower = JRequest::getVar('filter_create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('filter_update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countSimpleFilters++;
									$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
									
									$cswSimpleFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>$upper</ogc:Literal>
										</ogc:PropertyIsLessThanOrEqualTo> ";
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
									
									$cswSimpleFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>$lower</ogc:Literal>
										</ogc:PropertyIsGreaterThanOrEqualTo> ";
								}
								else if ($upper <> "" and $lower <> "") // Les deux bornes
								{
									$countSimpleFilters++;
									$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
									$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
									
									$cswSimpleFilter .= "<ogc:PropertyIsBetween>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:LowerBoundary><ogc:Literal>$lower</ogc:Literal></ogc:LowerBoundary>
										<ogc:UpperBoundary><ogc:Literal>$upper</ogc:Literal></ogc:UpperBoundary>
										</ogc:PropertyIsBetween> ";
								}
								break;
							case "textbox":
							default:
								$filter = JRequest::getVar('filter_'.$searchFilter->guid);
								
								if ($filter <> "")
								{
									$countSimpleFilters++;
									$cswSimpleFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>%$filter%</ogc:Literal>
										</ogc:PropertyIsLike> ";
									$empty = false;
								}
								break;
						}
					}
				}
			}
			/*// Ajouter les balises And si on a trouv� au moins deux conditions
			if ($countSimpleFilters > 1)
				$cswSimpleFilter = "<ogc:And>".$cswSimpleFilter."</ogc:And>";*/
			
			if ($cswSimpleFilter <> "")
				$condList[]=$cswSimpleFilter;
			//echo"CondList SimpleFilter: <br>"; print_r($condList); echo"<br>";	
			//echo "Criteres simples: ".htmlspecialchars($cswSimpleFilter)."<hr>";
			
			// Construction des filtres bas�s sur l'onglet avanc�
			$cswAdvancedFilter="";
			$countAdvancedFilters = 0;
			foreach($listAdvancedFilters as $searchFilter)
			{
				//print_r($searchFilter);echo "<br>";
				//echo $countAdvancedFilters."<br>";
				//echo $cswAdvancedFilter."<br>";
				$filter = JRequest::getVar('filter_'.$searchFilter->guid);
				//echo "<br>".'filter_'.$searchFilter->guid.": ".$mysqlFilter ;
				$lowerFilter = JRequest::getVar('create_cal_'.$searchFilter->guid);
				$upperFilter = JRequest::getVar('update_cal_'.$searchFilter->guid);
							
				if ($filter <> "" or $lowerFilter <> "" or $upperFilter <> "")
				{
					//echo "<br>"."Attributetype_code: ".$searchFilter->attributetype_code ;
				
					switch ($searchFilter->attributetype_code)
					{
						case "guid":
						case "text":
						case "locale":
						case "number":
						case "link":
							$countAdvancedFilters++;
							/* Fonctionnement texte*/
							//Break the space in the request and split it in many terms
							$kwords = explode(" ", trim($filter));
							//echo ", ".count($kwords).",  "; print_r($kwords);echo "<hr>";
							
							$terms=0;
							$cswTerm="";
							foreach ($kwords as $word) 
							{
								if ($word <> "")
								{
									$cswTerm .= "
									 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
												<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>%$word%</ogc:Literal>
											</ogc:PropertyIsLike>\r\n
										";
									$terms++;
								}
							}
							
							// R�unir les crit�res
							if ($terms > 1)
								$cswTerm = "<ogc:And>".$cswTerm."</ogc:And>";
							
							$cswAdvancedFilter.=$cswTerm;
							$empty = false;
							break;
						case "textchoice":
						case "localechoice":
							/* Fonctionnement liste de choix*/
							if (count($filter) > 0 and $filter[0] <> "")
							{
								$countAdvancedFilters++;
								foreach($filter as $f)
								{
									$localechoice ="";
									$list = array();
									$database->setQuery( "SELECT t.content FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id AND (at.code='textchoice' OR at.code='localechoice') AND c.id=".$f." AND c.published=true ORDER BY c.name" );
									$list = $database->loadObjectList();
	
									foreach ($list as $element)
									{
										$localechoice .= "
											<ogc:PropertyIsEqualTo>
												<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>$element->content</ogc:Literal>
											</ogc:PropertyIsEqualTo>
										";
									}
									if (count($list) > 1)
										$localechoice = "<ogc:Or>".$localechoice."</ogc:Or>";
									$cswAdvancedFilter .= $localechoice;
								}
								if (count($filter) > 1)
									$cswAdvancedFilter = "<ogc:Or>".$cswAdvancedFilter."</ogc:Or>";
							}
							
							$empty = false;
							break;
						case "list":
							/* Fonctionnement liste*/
							if (count($filter) > 0 and $filter[0] <> "")
							{
								$countAdvancedFilters++;
								foreach($filter as $f)
								{
									$cswAdvancedFilter .= "<ogc:PropertyIsEqualTo>
									<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
									<ogc:Literal>$f</ogc:Literal>
									</ogc:PropertyIsEqualTo> ";
								}
								if (count($filter) > 1)
									$cswAdvancedFilter = "<ogc:Or>".$cswAdvancedFilter."</ogc:Or>";
							}
							
							$empty = false; 
							break;
						case "date":
						case "datetime":
							/* Fonctionnement p�riode
							 * Format de date: 2001-01-15T20:07:48.11
							 * */
							//echo $lowerFilter."<br>";
							//echo $upperFilter."<br>";
							if ($lowerFilter == "") // Seulement la borne sup
							{
								$countAdvancedFilters++;
								$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
								$cswAdvancedFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:Literal>$upperFilter</ogc:Literal>
								</ogc:PropertyIsLessThanOrEqualTo> ";
							}
							else if ($upperFilter == "") // Seulement la borne inf
							{
								$countAdvancedFilters++;
								$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
								$cswAdvancedFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:Literal>$lowerFilter</ogc:Literal>
								</ogc:PropertyIsGreaterThanOrEqualTo> ";
							}
							else // Les deux bornes
							{
								$countAdvancedFilters++;
								$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
								$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
								$cswAdvancedFilter .= "<ogc:PropertyIsBetween>
								<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
								<ogc:LowerBoundary>
									<ogc:Literal>$lowerFilter</ogc:Literal>
								</ogc:LowerBoundary>
								<ogc:UpperBoundary>
									<ogc:Literal>$upperFilter</ogc:Literal>
								</ogc:UpperBoundary>
								</ogc:PropertyIsBetween> ";
							}
							
							$empty = false; 
							break;
						default:
							break;
					}
				}
				else // Traiter les filtres qui ne sont pas li�s � une relation, si ils existent
				{
					if ($searchFilter->criteriatype_id == 1) // Filtres syst�mes
					{
						// R�cup�ration des filtres standards
						//$account_id  = JRequest::getVar('account_id');
						//print_r($searchFilter);
						switch ($searchFilter->code)
						{
							case "fulltext":
								$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
								if ($simple_filterfreetextcriteria <> "")
								{
									$countAdvancedFilters++;
									// Filtre sur le texte (Crit�res de recherche simple)
									//$cswFreeTextFilter="";
									//echo ", recherche simple:".$simple_filterfreetextcriteria."  ";
									
									$kwords = explode(" ", trim($simple_filterfreetextcriteria));
									//echo count($kwords).":<br>"; print_r($kwords);echo "<hr>";
									
									// Filtres OGC directs pour les champs titre(title), description(abstract) et GEMET (keyword)
									//Break the space in the request and split it in many terms
									$title=0;
									$keyword=0;
									$abstract=0;
									$cswFreeFilters = 0;
									
									$cswTitle="";
									$cswKeyword="";
									$cswAbstract="";
									
									foreach ($kwords as $word) 
									{
										if ($word <> "")
										{
											$cswTitle .= "
											 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
														<ogc:PropertyName>title</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>\r\n
												";
											$title++;
											$cswKeyword .= "
											 		<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
														<ogc:PropertyName>keyword</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>\r\n
												";
											$keyword++;
											$cswAbstract .= "
											 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
												     	<ogc:PropertyName>abstract</ogc:PropertyName>
														<ogc:Literal>%$word%</ogc:Literal>
													</ogc:PropertyIsLike>
												";
											$abstract++;
											
											$cswFreeFilters++;
										}
									}
									//echo $cswTitle."<br>";
									//echo $cswKeyword."<br>";
									//echo $cswAbstract."<br>";
									
									// R�unir chaque crit�re
									if ($title > 1)
									{
										$cswTitle = "<ogc:And>".$cswTitle."</ogc:And>";
										//$cswFreeFilters++;
									}
									if ($keyword > 1)
									{
										$cswKeyword = "<ogc:And>".$cswKeyword."</ogc:And>";
										//$cswFreeFilters++;
									}
									if ($abstract > 1)
									{
										$cswAbstract = "<ogc:And>".$cswAbstract."</ogc:And>";
										//$cswFreeFilters++;
									}
									
									// R�unir les trois crit�res
									if($cswFreeFilters > 0)
										$cswAdvancedFilter = "<ogc:Or>".$cswTitle.$cswKeyword.$cswAbstract."</ogc:Or>";
									//print_r($cswAdvancedFilter);echo "<br>";
								
									// Filtres sur les guid de m�tadonn�es pour le code et le fournisseur
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.name LIKE '%".$simple_filterfreetextcriteria."%' ";
									$database->setQuery( $query);
									//echo "<br>".$database->getQuery()."<br>";
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectnamelist = $database->loadObjectList() ;
										
										foreach ($objectnamelist as $on)
										{
											$arrFreetextMd[] = $on->metadata_id;
										
											$empty = false;
										}
									}
									
									// S�lectionner tous les objets dont le nom du fournisseur ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_account a ON a.id=o.account_id
											  WHERE a.name LIKE '%".$simple_filterfreetextcriteria."%' ";
									$database->setQuery( $query);
									//echo "<br>".$database->getQuery()."<br>";
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$accountlist = $database->loadObjectList() ;
										
										foreach ($accountlist as $a)
										{
											$arrFreetextMd[] = $a->metadata_id;
										
											$empty = false;
										}
									}
									
									//If no result, give an unexisting id back
									/*if(count($objectnamelist) == 0 and count($accountlist) == 0)
										$arrFreetextMd[] = -1;*/
								}
								break;
							case "objecttype":
								//$objecttype_id = JRequest::getVar('objecttype_id');
								$objecttype_id = JRequest::getVar($searchFilter->guid);
								
								// Construire la liste des guid � filtrer
								$arrObjecttypeMd = array();
								
								//echo "objecttype_id: ".count($objecttype_id);
								if (count($objecttype_id) > 0)
								{
									//echo "<b>Cas1:</b><br>";
									//$countAdvancedFilters++;
									$arrObjecttypeMd=null;
								
									//echo ", objecttype";
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.objecttype_id IN (".implode(",", $objecttype_id).") "
											.$mysqlFilter;
									$database->setQuery( $query);
									//echo "list_id:".$database->getQuery()."<hr>";
									$list_id = $database->loadObjectList() ;
									//echo "list_id:". htmlspecialchars($list_id)."<hr>";
						
									if ($database->getErrorNum())
									{
										echo "<div class='alert'>";
										echo 	$database->getErrorMsg();
										echo "</div>";
									}
									
								}
								else if (!array_key_exists('objecttype_id', $_GET) and !array_key_exists('bboxMinX', $_GET)) // Cas du premier appel. Rechercher sur tous les types
								{
									//echo "<b>Cas2:</b><br>";
									//$countAdvancedFilters++;
									
									$objecttypes = array();
									if ($context <> "")
									{
										// R�cup�rer tous les types d'objets du contexte
										$database->setQuery("SELECT id FROM #__sdi_objecttype WHERE id IN
															(SELECT co.objecttype_id 
															FROM #__sdi_context_objecttype co
															INNER JOIN #__sdi_context c ON c.id=co.context_id 
															WHERE c.code = '".$context."')
													   ORDER BY name");
									}
									else
									{
										// R�cup�rer tous les types d'objets d�finis
										$database->setQuery("SELECT id FROM #__sdi_objecttype ORDER BY name");
									}
									//echo "objecttypes:".$database->getQuery()."<hr>";
									$objecttypes = $database->loadResultArray();
									
									// R�cup�rer toutes les m�tadonn�es de ces types d'objets
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.objecttype_id IN (".implode(",", $objecttypes).") "
											.$mysqlFilter;
									$database->setQuery( $query);
									//echo "list_id:".$database->getQuery()."<hr>";
									$list_id = $database->loadObjectList() ;
									//echo "list_id:". htmlspecialchars($list_id)."<hr>";
									if ($database->getErrorNum())
									{
										echo "<div class='alert'>";
										echo 	$database->getErrorMsg();
										echo "</div>";
									}
								}
								else if (count($objecttype_id) == 0)
								{
									$list_id=array();
									$arrObjecttypeMd[] = -1;
								}
								
								//If no result, give an unexisting id back
								/*if(count($list_id) == 0)
								{
									$arrObjecttypeMd[] = -1;
									break;
								}*/
								foreach ($list_id as $md_id)
								{
									$arrObjecttypeMd[] = $md_id->metadata_id;
								}
								
								if(count($list_id)> 0)
									$empty = false;
								
								break;
							case "versions":
								//$versions = JRequest::getVar('versions');
								$versions = JRequest::getVar($searchFilter->guid);
								//print_r("<pre>".var_dump($versions)."</pre>");
								if ($versions == "0" or !array_key_exists($searchFilter->guid, $_GET)) // Cas du premier appel et des versions actuelles. Rechercher sur les versions actuelles publi�es � la date courante 
								{
									//$countAdvancedFilters++;
									// Si l'utilisateur a choisi de ne chercher que sur les versions actuelles,
									// ajouter un filtre. Sinon ne rien faire
									//if ($versions == 0)
									//{
										//echo "<b>CasA:</b><br>";
										// S�lectionner tous les objets
										$query = "SELECT o.id 
												  FROM #__sdi_object o 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE 1 "
												.$mysqlFilter;
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectlist = $database->loadObjectList() ;
										//echo "objectlist:<br>";print_r($objectlist);echo "<hr>";
										// Construire la liste des guid � filtrer
										$arrVersionMd = array();
										
										// Pour chaque objet, s�lectionner toutes ses versions
										foreach ($objectlist as $object)
										{
											$query = "SELECT m.guid as metadata_id, ms.code, m.published
													  FROM #__sdi_objectversion ov 
													  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
													  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
													  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
													  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
													  WHERE o.id=".$object->id.
													 " ORDER BY ov.created DESC";
											$database->setQuery( $query);
											//echo "<br>".$database->getQuery()."<br>";
											$versionlist = $database->loadObjectList() ;
											//echo "versionlist:<br>";print_r($versionlist);echo "<hr>";
											
											if (count($versionlist))
											{
												//print_r($versionlist[0]);
												//echo "<br>";
												// Si la derni�re version est publi�e � la date courante, on l'utilise
												if ($versionlist[0]->code=='published'and $versionlist[0]->published <= date('Y-m-d'))
													$arrVersionMd[] = $versionlist[0]->metadata_id;
											
												$empty = false;
											}
										}
									//}
								}
								else if ($versions == "1") // Rechercher sur toutes les versions publi�es � la date courante
								{
									//$countAdvancedFilters++;
									//echo "<b>CasB:</b><br>";
									// S�lectionner tous les objets
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.published=1 or o.published=0 "
											.$mysqlFilter;
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									$arrVersionMd = array();
									
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
												  WHERE ms.code='published'
												  		AND m.published <= '".date('Y-m-d')."' 
												  		AND o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$versionlist = $database->loadObjectList() ;
											
										foreach ($versionlist as $v)
										{
											//print_r($versionlist[0]);
											//echo "<br>";
											$arrVersionMd[] = $v->metadata_id;
											
											$empty = false;
										}
									}
								}
								
								//If no result, give an unexisting id back
								/*if(count($arrVersionMd)== 0)
								{
									$arrVersionMd[] = -1;
									break;
								}*/
								
								break;
							case "object_name":
								//$object_name = JRequest::getVar('object_name');
								$object_name = JRequest::getVar($searchFilter->guid);
								
								if ($object_name <> "")
								{
									$countAdvancedFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.name LIKE '%".$object_name."%' ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id.
												//.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$objectnamelist = $database->loadObjectList() ;
										//echo "<br>"; print_r($objectnamelist); echo "<br>";
										
										foreach ($objectnamelist as $on)
										{
											$arrObjectNameMd[] = $on->metadata_id;
										
											$empty = false;
										}
									}
								}
								
								//If no result, give an unexisting id back
								/*if(count($arrObjectNameMd)== 0)
									$arrObjectNameMd[] = -1;*/
								
								break;
							case "metadata_created":
								$lower = JRequest::getVar('create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countAdvancedFilters++;
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created>='".$lower."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($lower <>"" and $upper <> "") // Les deux bornes
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.created>='".$lower."'".
										 " 			AND m.created<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrCreatedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrCreatedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								break;
							case "metadata_published":
								$lower = JRequest::getVar('create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countAdvancedFilters++;
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published>='".$lower."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								else if ($upper <> "" and $lower <> "") // Les deux bornes
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower))." 00:00:00";
									$upper = date('Y-m-d', strtotime($upper))." 23:59:59";
									
									$query = "SELECT m.guid as metadata_id 
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE m.published>='".$lower."'".
										 " 			AND m.published<='".$upper."'"
											.$mysqlFilter;
									$database->setQuery( $query);
									$mdlist = $database->loadObjectList() ;
									
									//If no result, give an unexisting id back
									/*if(count($mdlist)== 0)
										$arrPublishedMd[] = -1;*/
										
									foreach ($mdlist as $md)
									{
										$arrPublishedMd[] = $md->metadata_id;
										$empty = false;
									}
								}
								break;
							case "managers":
								//$managers = JRequest::getVar('managers');
								$managers = JRequest::getVar($searchFilter->guid);
								
								if (count($managers) > 0 and $managers[0] <> "")
								{
									$countAdvancedFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  INNER JOIN #__sdi_manager_object mo ON mo.object_id=o.id
											  WHERE mo.account_id IN (".implode(", ", $managers).") ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$managerlist = $database->loadObjectList() ;
										
										//If no result, give an unexisting id back
										/*if(count($managerlist)== 0)
											$arrManagersMd[] = -1;*/
											
										foreach ($managerlist as $m)
										{
											$arrManagersMd[] = $m->metadata_id;
										
											$empty = false;
										}
									}
								}
								break;
							case "title":
								//$metadata_title = JRequest::getVar('title');
								$metadata_title = JRequest::getVar($searchFilter->guid);
								
								if ($metadata_title <> "")
								{
									$countAdvancedFilters++;
									$cswAdvancedFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
									<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
									<ogc:Literal>%$metadata_title%</ogc:Literal>
									</ogc:PropertyIsLike> ";
									
									$empty = false;
								}
								break;
							case "account_id":
								//$accounts = JRequest::getVar('account_id');
								$accounts = JRequest::getVar($searchFilter->guid);
								//print_r(JRequest::getVar($searchFilter->guid));
								
								if (count($accounts) > 0 and $accounts[0] <> "")
								{
									$countAdvancedFilters++;
									// S�lectionner tous les objets dont le nom ressemble au texte saisi
									$query = "SELECT o.id 
											  FROM #__sdi_object o 
											  WHERE o.account_id IN (".implode(", ", $accounts).") ";
									$database->setQuery( $query);
									$objectlist = $database->loadObjectList() ;
									
									// Construire la liste des guid � filtrer
									// Pour chaque objet, s�lectionner toutes ses versions
									foreach ($objectlist as $object)
									{
										$query = "SELECT m.guid as metadata_id 
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  WHERE o.id=".$object->id
												.$mysqlFilter.
												 " ORDER BY ov.created DESC";
										$database->setQuery( $query);
										//echo "<br>".$database->getQuery()."<br>";
										$accountlist = $database->loadObjectList() ;
										
										//If no result, give an unexisting id back
										/*if(count($accountlist)== 0)
											$arrAccountsMd[] = -1;*/
											
										foreach ($accountlist as $a)
										{
											$arrAccountsMd[] = $a->metadata_id;
										
											$empty = false;
										}
									}
								}
								break;
							default:
								break;
						}
					}
					else // Cas des attributs OGC qui ne sont pas li�s � une relation
					{
						switch ($searchFilter->rendertype_code)
						{
							case "date":
								$lower = JRequest::getVar('filter_create_cal_'.$searchFilter->guid);
								$upper = JRequest::getVar('filter_update_cal_'.$searchFilter->guid);
								
								// S�lectionner toutes les m�tadonn�es cr��es dans l'intervalle indiqu�
								if ($lower == "" and $upper <> "") // Seulement la borne sup
								{
									$countAdvancedFilters++;
									$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
									
									$cswAdvancedFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>$upper</ogc:Literal>
										</ogc:PropertyIsLessThanOrEqualTo> ";
								}
								else if ($upper == "" and $lower <> "") // Seulement la borne inf
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
									
									$cswAdvancedFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>$lower</ogc:Literal>
										</ogc:PropertyIsGreaterThanOrEqualTo> ";
								}
								else if ($upper <> "" and $lower <> "") // Les deux bornes
								{
									$countAdvancedFilters++;
									$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
									$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
									
									$cswAdvancedFilter .= "<ogc:PropertyIsBetween>
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:LowerBoundary><ogc:Literal>$lower</ogc:Literal></ogc:LowerBoundary>
										<ogc:UpperBoundary><ogc:Literal>$upper</ogc:Literal></ogc:UpperBoundary>
										</ogc:PropertyIsBetween> ";
								}
								break;
							case "textbox":
							default:
								$filter = JRequest::getVar('filter_'.$searchFilter->guid);
								
								if ($filter <> "")
								{
									$countAdvancedFilters++;
									$cswAdvancedFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
										<ogc:PropertyName>$searchFilter->ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>%$filter%</ogc:Literal>
										</ogc:PropertyIsLike> ";
									$empty = false;
								}
								break;
						}
					}
				}
			}
			/*// Ajouter les balises And si on a trouv� au moins deux conditions
			if ($countAdvancedFilters > 1)
				$cswAdvancedFilter = "<ogc:And>".$cswAdvancedFilter."</ogc:And>";*/
			
			if ($cswAdvancedFilter <> "")
				$condList[]=$cswAdvancedFilter;
			//echo"CondList AdvancedFilter: <br>"; print_r($condList); echo"<br>";
			//echo "Criteres avances: ".htmlspecialchars($cswAdvancedFilter)."<hr>";
			//echo "filterMd:".htmlspecialchars($arrFilterMd)."<hr>";
				
			// Prendre l'intersection de tous les guid list�s
			$arrSearchableMd=array(); // Scope de recherche
			$arrFilteredMd=array(); // Filtres
			 
			//Build the filter
			/*for ($i=0; $i < count($arrFilterMd); $i++)
			{
				if($i == 0){
					$arrSearchableMd = $arrFilterMd[$i];
					continue;
				}
				$arrSearchableMd = array_intersect($arrObjecttypeMd, $arrVersionMd);
			}*/
			if (count($arrObjecttypeMd) == 0) // Pas de types d'objet
				$arrSearchableMd = $arrVersionMd;
			else if (count($arrVersionMd) == 0) // Pas de versions
				$arrSearchableMd = $arrObjecttypeMd;
			else // Faire l'intersection
				$arrSearchableMd = array_intersect($arrObjecttypeMd, $arrVersionMd);
			
			//echo "arrObjecttypeMd<br>";print_r($arrObjecttypeMd);echo "<hr>";
			//echo "arrVersionMd<br>";print_r($arrVersionMd);echo "<hr>";
			//echo "arrSearchableMd<br>";print_r($arrSearchableMd);echo "<hr>";
			//echo "arrFilteredMd<br>";print_r($arrFilteredMd);echo "<hr>";
				
			// Freetext
			if (count($arrFreetextMd) <> 0) 
				if (count($arrFilteredMd) == 0) // Liste vide pour l'instant
					$arrFilteredMd[] = $arrFreetextMd;
				else // Faire l'intersection
					$arrFilteredMd[] = array_intersect($arrFreetextMd, $arrFilteredMd);
		
			//echo "arrFreetextMd<br>";print_r($arrFreetextMd);echo "<hr>";
			//echo "arrFilteredMd<br>";print_r($arrFilteredMd);echo "<hr>";
			
			// Objectname
			if (count($arrObjectNameMd) <> 0) 
			{
				if (count($arrFilteredMd) == 0) // Liste vide pour l'instant
				{
					$arrFilteredMd[] = $arrObjectNameMd;
				}
				else // Faire l'intersection
				{
					$intersect = array_intersect($arrObjectNameMd, $arrFilteredMd);
					if (count($intersect) > 0)
						$arrFilteredMd[] = $intersect;
				}
			}
		
			//echo "arrObjectNameMd<br>";print_r($arrObjectNameMd);echo "<hr>";
			//echo "arrFilteredMd<br>";print_r($arrFilteredMd);echo "<hr>";
			
			// Accounts
			if (count($arrAccountsMd) <> 0)
			{
				if (count($arrFilteredMd) == 0) // Liste vide pour l'instant
				{
					$arrFilteredMd[] = $arrAccountsMd;
				}
				else  // Faire l'intersection
				{
					$intersect = array_intersect($arrAccountsMd, $arrFilteredMd);
					if (count($intersect) > 0)
						$arrFilteredMd[] = $intersect;
				}
			}
			
			//echo "arrAccountsMd<br>";print_r($arrAccountsMd);echo "<hr>";
			//echo "arrFilteredMd<br>";print_r($arrFilteredMd);echo "<hr>";
			
			// Managers
			if (count($arrManagersMd) <> 0)
			{
				if (count($arrFilteredMd) == 0) // Liste vide pour l'instant
				{
					$arrFilteredMd[] = $arrManagersMd;
				}
				else  // Faire l'intersection
				{
					$intersect = array_intersect($arrManagersMd, $arrFilteredMd);
					if (count($intersect) > 0)
						$arrFilteredMd[] = $intersect;
				}
			}
			
			//print_r($arrManagersMd);echo "<hr>";
			//print_r($arrFilteredMd);echo "<hr>";
			
			// Created
			if (count($arrCreatedMd) <> 0) 
			{
				if (count($arrSearchableMd) == 0) // Liste vide pour l'instant
				{
					$arrFilteredMd[] = $arrCreatedMd;
				}
				else // Faire l'intersection
				{
					$intersect = array_intersect($arrCreatedMd, $arrFilteredMd);
					if (count($intersect) > 0)
						$arrFilteredMd[] = $intersect;
				}
			}
			
			//print_r($arrCreatedMd);echo "<hr>";
			//print_r($arrFilteredMd);echo "<hr>";
			
			// Published
			if (count($arrPublishedMd) <> 0) 
			{
				if (count($arrSearchableMd) == 0) // Liste vide pour l'instant
				{
					$arrFilteredMd[] = $arrPublishedMd;
				}
				else // Faire l'intersection
				{
					$intersect = array_intersect($arrPublishedMd, $arrFilteredMd);
					if (count($intersect) > 0)
						$arrFilteredMd[] = $intersect;
				}
			}
			
			//print_r($arrPublishedMd);echo "<hr>";
			//print_r($arrFilteredMd);echo "<hr>";
			
			$cswMdCond = "";
			foreach ($arrSearchableMd as $md_id)
			{
				//keep it so to keep the request "small"
				$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$md_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
			}
			if(count($arrSearchableMd) > 1)
				$cswMdCond = "<ogc:Or>".$cswMdCond."</ogc:Or>";
			
			if((count($arrSearchableMd) == 0))
			{
				$condList[] = "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>-1</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
				//echo "CondList Vide: <br>"; print_r($condList); echo"<br>";
			}
			
			if(count($arrSearchableMd) > 0)
				$condList[] = $cswMdCond;
			//echo"CondList SearchableMd: <br>"; print_r($condList); echo"<br>";	
			$cswMdCond = "";
			/*foreach ($arrFilteredMd as $md_id)
			{
				//keep it so to keep the request "small"
				$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$md_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
			}
			if(count($arrFilteredMd) > 1)
				$cswMdCond = "<ogc:Or>".$cswMdCond."</ogc:Or>";
			*/
			//echo count($arrFilteredMd)." and ".$countAdvancedFilters." or ".$countSimpleFilters." or ".count($condList)."<br>";
			if((count($arrFilteredMd) == 0) and ($countAdvancedFilters <> 0 or $countSimpleFilters <> 0) and count($condList) == 0)
			{
				$condList[] = "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>-1</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
				//echo "CondList Vide: <br>"; print_r($condList); echo"<br>";
			}
			else
			{
				foreach ($arrFilteredMd as $filteredMd)
				{
					if (count($filteredMd) > 1)
						$cswMdCond.= "<ogc:Or>";
					foreach ($filteredMd as $md_id)
					{
						//keep it so to keep the request "small"
						$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$md_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
					}
					if (count($filteredMd) > 1)
						$cswMdCond.= "</ogc:Or>";
				}
				
				// Si l'intersection des filtres est nulle, mais qu'il y avait bien des filtres, forcer la rechercher sur
				// un guid inexistant pour �tre s�r qu'aucun r�sultat ne soit retourn�
											
				if(count($arrFilteredMd) > 1)
					$cswMdCond = "<ogc:And>".$cswMdCond."</ogc:And>";
				
				if(count($arrFilteredMd) > 0)
					$condList[] = $cswMdCond;
				//echo"CondList FilteredMd: <br>"; print_r($condList); echo"<br>";
			}
			
			$cswfilterCond = "";
			//echo "condList<br>";print_r($condList);echo "<hr>";
			foreach ($condList as $cond)
			{
				$cswfilterCond .= $cond;
			}
			//$cswfilterCond .= $cswSimpleFilter;
			//$cswfilterCond .= $cswAdvancedFilter;
			//$cswfilterCond .= $bboxfilter;
			//echo "searchableMd:". htmlspecialchars($arrSearchableMd)."<hr>";
			
			//Don't put an <and> if no other condition is requested
			//if($cswSimpleFilter != "" || $cswAdvancedFilter != "" || $bboxfilter != "")
			if (count($condList) > 1) 
				$cswfilterCond = "<ogc:And>\r\n".$cswfilterCond."</ogc:And>\r\n";
			
			$cswfilter = "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
			$cswfilter .= $cswfilterCond;
			$cswfilter .= "</ogc:Filter>\r\n";
			
			//echo "cswfilter:". htmlspecialchars($cswfilter);
			
			// BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder)
			$xmlBody = SITE_catalog::BuildCSWRequest(10, 1, "datasetcollection dataset application service", "full", "1.1.0", $cswfilter, "title", "ASC");
			
			//Get the result from the server, only for count
			/*
			$myFile = "/home/sites/joomla.asitvd.ch/web/components/com_easysdi_shop/core/myFile_cat.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
		
			fwrite($fh, $xmlBody);
			fwrite($fh, $xmlResponse);
			
			fclose($fh);
			*/
			
			//echo "xmlbody:". htmlspecialchars($xmlBody);
			
			$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
			// SimpleXMLElement
			$cswResults= simplexml_load_string($xmlResponse);
			//echo var_dump($cswResults->saveXML())."<br>";
			// DOMDocument
			$myDoc = new DomDocument();
			$myDoc->loadXML($xmlBody);
			//$myDoc->save("C:\\RecorderWebGIS\\searchRequest.xml");
			$myDoc = new DomDocument();
			$myDoc->loadXML($cswResults->asXML());
			//$myDoc->save("C:\\RecorderWebGIS\\searchResult.xml");
			//echo "dump the file: <br/><br/><br/>";
			//echo $cswResults->asXML()."<br>";
			//echo htmlspecialchars($myDoc->saveXML())."<br>";
			//echo "<br/> end dump the file";
			
			$total = 0;
			if ($cswResults !=null)
			{
				// Contr�ler si le XML ne contient pas une erreur
				if ($myDoc->childNodes->item(0)->nodeName== "ows:ExceptionReport")
				{
					$msg = $myDoc->childNodes->item(0)->nodeValue;
					$mainframe->enqueueMessage($msg,"ERROR");
					
					// Comportement identique que si aucune recherche n'a �t� faite
					$total=0;
					$pageNav=new JPagination($total,$limitstart,$limit);
					$cswResults = null;
				}
				else
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
					
					$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
	
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
		}
		else // Si la recherche n'a pas �t� lanc�e, afficher une liste de r�sultats vide
		{
			$total=0;
			$pageNav=new JPagination($total,$limitstart,$limit);
			$cswResults = null;
		}
		
		$allVersions=true;
		
		HTML_catalog::listCatalogContentWithPan($pageNav,$cswResults,$option,$total,$simple_filterfreetextcriteria,$maxDescr, $allVersions, $listSimpleFilters, $listAdvancedFilters);
		
	}

	function BuildCSWRequest($maxRecords, $startPosition, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder)
	{
		//Bug: If we have accents, we must specify ISO-8859-1
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$req .= "";
		
		//Get Records section
		$req .=  "<csw:GetRecords xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\" resultType=\"results\" outputSchema=\"csw:IsoRecord\" content=\"COMPLETE\" ";
		
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
}
?>