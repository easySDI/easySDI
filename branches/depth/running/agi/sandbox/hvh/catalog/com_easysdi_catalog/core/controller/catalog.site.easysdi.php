<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d'Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
	var $langList = array ();
	
	function listCatalogContent(){
		global $mainframe;
		$database 	=& JFactory::getDBO();
		$user 		= JFactory::getUser();
		$language 	=& JFactory::getLanguage();
		
		// Valeurs de configuration pour le rendu des résultats
		$maxDescr 	= config_easysdi::getValue("description_length");
		$MDPag 		= config_easysdi::getValue("catalog_pagination_searchresult");
		
		$option		= JRequest::getVar("option");
		$limit 		= JRequest::getVar('limit', $MDPag );
		$context	= $option.'.listCatalogContent';
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
				
		// Langues à gérer
		$this->langList = array();
		$database->setQuery( "SELECT l.id, l.name, l.label, l.defaultlang, l.code as code, l.isocode, l.gemetlang, c.code as code_easysdi 
							  FROM #__sdi_language l, #__sdi_list_codelang c 
							  WHERE l.codelang_id=c.id 
							  		AND published=true 
							  ORDER BY l.ordering" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		
		// Récupération de toutes les traductions pour la construction des critères de recherche,
		// spécialement ceux sous forne de liste dont le contenu doit être traduit
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
		
		//Premier affichage par défaut du formulaire du catalog
		$defaultSearch= JRequest::getVar('defaultSearch',1);
		
		// Liste des critères de recherche simple
		$context= JRequest::getVar('context');
		$contextObject = new contextByCode($database);
		$contextObject->load($context);
		
		$listSimpleFilters = array();
		$database->setQuery("SELECT sc.*, 
									sc_tab.ordering as context_order , 
									r.guid as relation_guid, 
									a.id as attribute_id, 
									at.alias as attributetype_code, 
									sc.code as criteria_code, 
									rt.code as rendertype_code,
									ccc.defaultvalue as defaultvalue,
									ccc.defaultvaluefrom as defaultvaluefrom,
									ccc.defaultvalueto as defaultvalueto,
									ccc.params as params,
									c.filter as filter
					   FROM #__sdi_searchcriteria sc
					   			  LEFT OUTER JOIN #__sdi_relation r ON r.id=sc.relation_id
								  LEFT OUTER JOIN #__sdi_relation_context rc ON r.id=rc.relation_id 
								  LEFT OUTER JOIN #__sdi_context c ON c.id=rc.context_id
								  LEFT OUTER JOIN  (SELECT cc.*  FROM #__sdi_context_criteria cc INNER JOIN #__sdi_searchcriteria ccs  ON  cc.criteria_id = ccs.id WHERE cc.context_id = (SELECT id FROM #__sdi_context WHERE code='".$context."')) ccc ON ccc.criteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  LEFT OUTER JOIN #__sdi_sys_stereotype at ON at.id=a.attributetype_id
								  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON sc_tab.searchcriteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_context c_tab ON sc_tab.context_id=c_tab.id
								  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id
								  LEFT OUTER JOIN #__sdi_list_rendertype rt ON sc.rendertype_id=rt.id
					   WHERE (sc.relation_id IS NULL
					   		 OR c.code='".$context."')
					   		 AND c_tab.code='".$context."'
					   		 AND tab.code = 'simple' 
					   ORDER BY context_order");
		$listSimpleFilters = array_merge( $listSimpleFilters, $database->loadObjectList() );		
		
		// Liste des critères de recherche avancés
		$listAdvancedFilters = array();
		$database->setQuery("SELECT sc.*, 
									sc_tab.ordering as context_order , 
									r.guid as relation_guid, 
									a.id as attribute_id, 
									at.alias as attributetype_code, 
									sc.code as criteria_code, 
									rt.code as rendertype_code,
									ccc.defaultvalue as defaultvalue,
									ccc.defaultvaluefrom as defaultvaluefrom,
									ccc.defaultvalueto as defaultvalueto,
									ccc.params as params,
									c.filter as filter
					   FROM #__sdi_searchcriteria sc
					   			  LEFT OUTER JOIN #__sdi_relation r ON r.id=sc.relation_id
								  LEFT OUTER JOIN #__sdi_relation_context rc ON r.id=rc.relation_id 
								  LEFT OUTER JOIN #__sdi_context c ON c.id=rc.context_id
								  LEFT OUTER JOIN  (SELECT cc.*  FROM #__sdi_context_criteria cc INNER JOIN #__sdi_searchcriteria ccs  ON  cc.criteria_id = ccs.id WHERE cc.context_id = (SELECT id FROM #__sdi_context WHERE code='".$context."')) ccc ON ccc.criteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
								  LEFT OUTER JOIN #__sdi_sys_stereotype at ON at.id=a.attributetype_id
								  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON sc_tab.searchcriteria_id=sc.id
								  LEFT OUTER JOIN #__sdi_context c_tab ON sc_tab.context_id=c_tab.id
								  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id
								  LEFT OUTER JOIN #__sdi_list_rendertype rt ON sc.rendertype_id=rt.id
					   WHERE (sc.relation_id IS NULL
					   		 OR c.code='".$context."')
					   		 AND c_tab.code='".$context."'
					   		 AND tab.code = 'advanced' 
					   ORDER BY context_order");
		$listAdvancedFilters = array_merge( $listAdvancedFilters, $database->loadObjectList() );	

		// Liste des critères de recherche masqués
		$listHiddenFilters = array();
		$database->setQuery("SELECT sc.*, 
									sc_tab.ordering as context_order , 
									r.guid as relation_guid, 
									a.id as attribute_id, 
									at.alias as attributetype_code, 
									sc.code as criteria_code, 
									rt.code as rendertype_code,
									ccc.defaultvalue as defaultvalue,
									ccc.defaultvaluefrom as defaultvaluefrom,
									ccc.defaultvalueto as defaultvalueto,
									ccc.params as params,
									c.filter as filter
							   FROM #__sdi_searchcriteria sc
							   			  LEFT OUTER JOIN #__sdi_relation r ON r.id=sc.relation_id
										  LEFT OUTER JOIN #__sdi_relation_context rc ON r.id=rc.relation_id 
										  LEFT OUTER JOIN #__sdi_context c ON c.id=rc.context_id
										  LEFT OUTER JOIN  (SELECT cc.*  FROM jos_sdi_context_criteria cc INNER JOIN jos_sdi_searchcriteria ccs  ON  cc.criteria_id = ccs.id WHERE cc.context_id = (SELECT id FROM jos_sdi_context WHERE code='".$context."')) ccc ON ccc.criteria_id=sc.id
										  LEFT OUTER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
										  LEFT OUTER JOIN #__sdi_sys_stereotype at ON at.id=a.attributetype_id
										  LEFT OUTER JOIN #__sdi_searchcriteria_tab sc_tab ON sc_tab.searchcriteria_id=sc.id
										  LEFT OUTER JOIN #__sdi_context c_tab ON sc_tab.context_id=c_tab.id
										  LEFT OUTER JOIN #__sdi_list_searchtab tab ON tab.id=sc_tab.tab_id
										  LEFT OUTER JOIN #__sdi_list_rendertype rt ON sc.rendertype_id=rt.id
							   WHERE (sc.relation_id IS NULL
							   		 OR c.code='".$context."')
							   		 AND c_tab.code='".$context."'
							   		 AND tab.code = 'hidden' 
							   ORDER BY context_order");
		$listHiddenFilters = array_merge( $listHiddenFilters, $database->loadObjectList() );
		
		// Flag pour déterminer s'il y a ou pas des filtres
		$empty = true;
		$condList = array();
		
		// Construction de la requête pour récupérer les résultats
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$ogcfilter_fileid = config_easysdi::getValue("catalog_search_ogcfilterfileid");
		$xmlHeader = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
				
		$ogcsearchsorting="";
		$database->setQuery("SELECT cs.ogcsearchsorting
		   FROM #__sdi_context_sort cs
		   LEFT OUTER JOIN #__sdi_context c ON cs.context_id=c.id
		   LEFT OUTER JOIN #__sdi_language l ON cs.language_id=l.id
		   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
		   WHERE c.code='".$context."'
		   		 AND cl.code='".$language->_lang."'");
		$ogcsearchsorting = $database->loadResult();
		
		// Construction de la bbox obligatoire
		$minX = JRequest::getVar('bboxMinX', "-180" );
		$minY = JRequest::getVar('bboxMinY', "-90" );
		$maxX = JRequest::getVar('bboxMaxX', "180" );
		$maxY = JRequest::getVar('bboxMaxY', "90" );
		
		// Tableau contenant les guid des métadonnées sur lesquelles la recherche va porter
		$arrFilterMd = array();
		
		$simple_filterfreetextcriteria = JRequest::getVar('simple_filterfreetextcriteria');
							
		// Selon que l'utilisateur est loggé ou pas, on ne fera pas la recherche sur le même set de métadonnées
		// Récupération du compte, si l'utilisateur est loggé
		$account = new accountByUserId($database);
		if (!$user->guest){
			$account->load($user->id);
		}else{
			$account->id = 0;
		}
		
		// Recherche du cas où on a changé la langue et que les tableaux en GET ont sauté
		// On se base sur les champs contenant la valeur "Array". S'il y en a, on saute tous les traitement et on redirige la page sur la recherche de départ
		$languageChanged = false;
		$languageChanged = in_array("Array", $_REQUEST);
		$key = array_search("Array", $_REQUEST);

		/* Construction de la requête de recherche */
		// Ne retourner des résultats que si l'utilisateur a soumis une requête
		// ou que l'option de recherche par défault est défini sur le context 
		if(		(isset($_REQUEST['simple_search_button']) && (isset($_REQUEST['context']) || isset($_GET['context'])) && !$languageChanged)
			 || (isset($_REQUEST['limitstart']) && (isset($_REQUEST['context']) || isset($_GET['context'])) && !$languageChanged)
			 || (($contextObject->runinitsearch == 1) && (!isset($_REQUEST['simple_search_button'])) && (!isset($_REQUEST['limitstart'])) && (isset($_REQUEST['context']) || isset($_GET['context'])) && !$languageChanged)
		  ){
			// Si aucun utilisateur n'est loggé, ne retourner que les métadonnées publiques
			if($account->id == 0){
				//No user logged, display only external products
				$mysqlFilter = " AND (v.code='public') ";
			}else{
				// Si l'utilisateur est loggé, retourner toutes les métadonnées publiques
				// + les métadonnées privées à son compte racine
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
			$query =  "SELECT sc.*, cc.defaultvalue,  cc.params FROM #__sdi_searchcriteria sc
								INNER JOIN #__sdi_context_criteria cc ON cc.criteria_id = sc.id 	
								INNER JOIN #__sdi_context c ON c.id=	cc.context_id	
								where sc.code ='definedBoundary'
								AND cc.context_id = $contextObject->id";
			$database->setQuery( $query);
			$boundaryFilter = $database->loadObject() ;
			
			$selectedBoundary = $defaultSearch? $boundaryFilter->defaultvalue : JRequest::getVar('systemfilter_'.$boundaryFilter->guid,"");
			if($selectedBoundary!=""){
					$definedBoundary = array();
					
					$query =  "SELECT * FROM #__sdi_boundary where guid ='".$selectedBoundary."'" ;
					$database->setQuery( $query);
					$definedBoundary = $database->loadObject() ;
					
					$searchtype = json_decode($boundaryFilter->params)->boundarysearch;
					switch ($searchtype){
						case "bbox" :
							if($definedBoundary){
								$minY = $definedBoundary->southbound;
								$minX = $definedBoundary->westbound;
								$maxY = $definedBoundary->northbound;
								$maxX =$definedBoundary->eastbound;
							
								$bboxfilter ="
								<ogc:BBOX>
									<ogc:PropertyName>iso:BoundingBox</ogc:PropertyName>
									<gml:Envelope xmlns:gml=\"http://www.opengis.net/gml\">
										<gml:lowerCorner>".$minX." ".$minY."</gml:lowerCorner>
										<gml:upperCorner>".$maxX." ". $maxY."</gml:upperCorner>
									</gml:Envelope>
								</ogc:BBOX>";
							}
							break;
						case "id":
							//build the filter
							$query =  "SELECT title FROM #__sdi_translation where element_guid ='".$selectedBoundary."'" ;
							$database->setQuery( $query);
							$boundaryTitle = $database->loadResult() ;
							
							$query =  "SELECT alias FROM #__sdi_boundarycategory WHERE id = ".$definedBoundary->category_id ;
							$database->setQuery( $query);
							$categoryLabel = $database->loadResult() ;
							
							$boundarysearchfield = json_decode($boundaryFilter->params)->boundarysearchfield;
							$categorysearchfield = json_decode($boundaryFilter->params)->categorysearchfield;
							
							$bboxfilter ="
							<ogc:and>
								<ogc:PropertyIsEqualTo>
									<ogc:PropertyName>".$boundarysearchfield."</ogc:PropertyName>
									<ogc:Literal>".$boundaryTitle."</ogc:Literal>
								</ogc:PropertyIsEqualTo>
								<ogc:PropertyIsEqualTo>
									<ogc:PropertyName>".$categorysearchfield."</ogc:PropertyName>
									<ogc:Literal>".$categoryLabel."</ogc:Literal>
								</ogc:PropertyIsEqualTo>
							</ogc:and>
							";
							
							//Get the hierachy boundary
							if(isset($definedBoundary->parent_id)){
								$bboxfilter = "<ogc:Or>".SITE_catalog::getFilterForBoundaryParent($bboxfilter, $definedBoundary->parent_id, $boundarysearchfield, $categorysearchfield)."</ogc:Or>";
							}
							break;
					}
					
					
					
			}else{
				$bboxfilter ="";
			}
			
			if ($bboxfilter <> "")
				$condList[]=$bboxfilter;
			
			if (isset($contextObject->filter) && strlen($contextObject->filter) > 0){
				//Add the user defined filter
				$condList[]= $contextObject->filter;
			}
			
			$boundaryFilter ="";
			
			
			// Listes qui vont potentiellement contenir des guid de métadonnées
			$arrFreetextMd = null;
			$arrObjectNameMd = null;
			$arrAccountsMd = null;
			$arrManagersMd = null;
			$arrCreatedMd = null;
			$arrPublishedMd = null;
			$hasObjectTypeFilter = false;
			$arrObjecttypeMd = null;
			$arrVersionMd = null;
			$objecttype_id = 0;
			$arrDownloadableMd = null;
			$arrFreeMd = null;
			$arrOrderableMd = null;
			$isDownloadable = false;
			$isFree=false;
			$isOrderable=false;
			
			// Construction des filtres basés sur l'onglet simple
			$cswSimpleFilter="";
			$countSimpleFilters = 0;
			foreach($listSimpleFilters as $searchFilter)
			{
				SITE_catalog::buildFilterConstraint(true,
													$database,
													$language,
													$defaultSearch,
													$searchFilter,
													$mysqlFilter, 
													$context,
													$countSimpleFilters, 
													$cswSimpleFilter,
													$arrFreetextMd,
													$arrObjectNameMd,
													$arrAccountsMd,
													$arrManagersMd,
													$arrCreatedMd,
													$arrPublishedMd,
													$hasObjectTypeFilter,
													$arrObjecttypeMd,
													$arrVersionMd,
													$objecttype_id,
													$empty,
													$arrDownloadableMd,
													$arrFreeMd,
													$arrOrderableMd,
													$isDownloadable,
													$isFree,
													$isOrderable);
			}
			if ($cswSimpleFilter <> "")
				$condList[]=$cswSimpleFilter;
			
			// Construction des filtres basés sur l'onglet avancé
			$advancedSrch = JRequest::getVar('advancedSrch',0);
			if($advancedSrch == 1){
				$cswAdvancedFilter="";
				$countAdvancedFilters = 0;
				foreach($listAdvancedFilters as $searchFilter)
				{
					SITE_catalog::buildFilterConstraint(false,
														$database,
														$language,
														$defaultSearch,
														$searchFilter,
														$mysqlFilter, 
														$context,
														$countAdvancedFilters, 
														$cswAdvancedFilter,
														$arrFreetextMd,
														$arrObjectNameMd,
														$arrAccountsMd,
														$arrManagersMd,
														$arrCreatedMd,
														$arrPublishedMd,
														$hasObjectTypeFilter,
														$arrObjecttypeMd,
														$arrVersionMd,
														$objecttype_id,
														$empty,
														$arrDownloadableMd,
														$arrFreeMd,
														$arrOrderableMd,
														$isDownloadable,
														$isFree,
														$isOrderable);
				}
				if ($cswAdvancedFilter <> "")
					$condList[]=$cswAdvancedFilter;
			}
			// Construction des filtres basés sur l'onglet caché
			$cswHiddenFilter="";
			$countHiddenFilters = 0;
			foreach($listHiddenFilters as $searchFilter)
			{
				SITE_catalog::buildFilterConstraint(false,
													$database,
													$language,
													true,
													$searchFilter,
													$mysqlFilter,
													$context,
													$countHiddenFilters,
													$cswHiddenFilter,
													$arrFreetextMd,
													$arrObjectNameMd,
													$arrAccountsMd,
													$arrManagersMd,
													$arrCreatedMd,
													$arrPublishedMd,
													$hasObjectTypeFilter,
													$arrObjecttypeMd,
													$arrVersionMd,
													$objecttype_id,
													$empty,
													$arrDownloadableMd,
													$arrFreeMd,
													$arrOrderableMd,
													$isDownloadable,
													$isFree,
													$isOrderable);
			}
			if ($cswHiddenFilter <> "")
				$condList[]=$cswHiddenFilter;
			
			//Pour inclure uniquement les types d'object du context même si le critère de recherche type d'objet n'est pas affiché
			if(!$hasObjectTypeFilter ){
				$objecttypes = array();
				if ($context <> ""){
					// Récupérer tous les types d'objets du contexte
					$database->setQuery("SELECT id FROM #__sdi_objecttype WHERE id IN
										(SELECT co.objecttype_id 
										FROM #__sdi_context_objecttype co
										INNER JOIN #__sdi_context c ON c.id=co.context_id 
										WHERE c.code = '".$context."')
								   ORDER BY name");
				}else{
					// Récupérer tous les types d'objets définis
					$database->setQuery("SELECT id FROM #__sdi_objecttype ORDER BY name");
				}
				$objecttypes = $database->loadResultArray();
				
				// Récupérer toutes les métadonnées de ces types d'objets
				$query = "SELECT m.guid as metadata_id 
						  FROM #__sdi_objectversion ov 
						  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
						  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
						  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
						  WHERE o.objecttype_id IN (".implode(",", $objecttypes).") "
						.$mysqlFilter;
				$database->setQuery( $query);
				$list_id = $database->loadObjectList() ;
				$arrObjecttypeMd=array();
				
				if ($database->getErrorNum()){
					$msg = $database->getErrorMsg();
				}
			
				foreach ($list_id as $md_id)
				{
					$arrObjecttypeMd[] = $md_id->metadata_id;
				}
			}
				
			//Si le filtre sur les versions n'a pas été pris en compte (non défini comme filtre actif ou dans la recherche avancée mais non utilisée)
			//Rajouter un comportement par défaut pour ne pas renvoyer les métadonnées non publiées
			if ($arrVersionMd == null)
			{
				$query = "SELECT o.id
				FROM #__sdi_object o
				INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
				WHERE 1 "
				.$mysqlFilter;
				$database->setQuery( $query);
				$objectlist = $database->loadObjectList() ;
				$arrVersionMd =array();
				// Pour chaque objet, sélectionner toutes ses versions publiées
				foreach ($objectlist as $object)
				{
					$query = "SELECT m.guid as metadata_id, ms.code, m.published
					FROM #__sdi_objectversion ov
					INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
					INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
					INNER JOIN #__sdi_object o ON ov.object_id=o.id
					INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
					WHERE o.id=".$object->id.
					"		AND ((ms.code='published'
					AND m.published <='".date('Y-m-d')."')
					OR
					(ms.code='archived'
					AND m.archived >'".date('Y-m-d')."')
					)
					ORDER BY m.published DESC
					LIMIT 0, 1";
					$database->setQuery( $query);
					$versionlist = $database->loadObjectList() ;
			
					if (count($versionlist))
					{
						// Si la dernière version est publiée à la date courante, on l'utilise
						$arrVersionMd[] = $versionlist[0]->metadata_id;
					}
				}
			}
			
			// Prendre l'intersection de tous les guid listés
			$arrSearchableMd=array(); // Scope de recherche
			 
			//Build the filter
			if (!isset($arrObjecttypeMd ) ) // Pas de types d'objet
				$arrSearchableMd = $arrVersionMd;
			else if (!isset($arrVersionMd )) // Pas de versions
				$arrSearchableMd = $arrObjecttypeMd;
			else // Faire l'intersection
				$arrSearchableMd = array_intersect($arrObjecttypeMd, $arrVersionMd);
			
			//Prendre en compte les MD autorisées par les critères : téléchargeable, commandable et gratuit
			if (isset($arrDownloadableMd))
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrDownloadableMd);
			if (isset($arrFreeMd))
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrFreeMd);
			if (isset($arrOrderableMd))
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrOrderableMd);
			
			// Objectname
			if (isset($arrObjectNameMd) ){
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrObjectNameMd);
			}
			
			// Accounts
			if (isset($arrAccountsMd)){
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrAccountsMd);
			}
			
			// Managers
			if (isset($arrManagersMd ) ){
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrManagersMd);
			}
			
			// Created
			if (isset($arrCreatedMd)) {
					$arrSearchableMd = array_intersect($arrSearchableMd, $arrCreatedMd);
			}
			
			// Published
			if (isset($arrPublishedMd ) ) {
				$arrSearchableMd = array_intersect($arrSearchableMd, $arrPublishedMd);
			}
			
			$cswMdCond = "";
			//Le scope de recherche c'est:
			//Les md de l'array $arrSearchableMd
			foreach ($arrSearchableMd as $md_id)
			{
				//keep it so to keep the request "small"
				$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>$ogcfilter_fileid</ogc:PropertyName><ogc:Literal>$md_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
			}
			if(count($arrSearchableMd) > 1)
				$cswMdCond = "<ogc:Or>".$cswMdCond."</ogc:Or>";
			$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>harvested</ogc:PropertyName><ogc:Literal>false</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
			$cswMdCond = "<ogc:And>".$cswMdCond."</ogc:And>";
			
			//Si aucun filtre touchant des attributs systèmes n'est utilisé dans cette recherche 
			//(attribut faisant référence à des données stockées dana la base MySQL EasySDI)
			//on retourne aussi les données harvestées.
			//Attributs sytèmes :
			//- objecttype
			//- isDownloadable
			//- isFree
			//- isOrderable
			//- metadata_created
			//- metadata_updated
			//- object_name
			//- account_id
			if (!isset($arrObjectNameMd) && 
				!isset($arrAccountsMd ) && 
				!isset($arrCreatedMd ) && 
				!isset($arrPublishedMd ) &&
				( !isset($objecttype_id)|| !$hasObjectTypeFilter )&& 
				$isDownloadable == 0 && 
				$isFree == 0 && 
				$isOrderable == 0){ 
				$cswMdCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>harvested</ogc:PropertyName><ogc:Literal>true</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
				$cswMdCond = "<ogc:Or>".$cswMdCond."</ogc:Or>";
			}

			if(count($arrSearchableMd) == 0)
			{
				//Pas de metadonnées dans le tableau des Id à rechercher
				//Si des types d'objet ont été sélectionnés ou l'option téléchargeable sélectionnée, on doit écarter les données harvestées
				if (isset($arrObjectNameMd ) || 
					isset($arrAccountsMd ) || 
					isset($arrCreatedMd ) || 
					isset($arrPublishedMd ) ||
					( isset($objecttype_id ) || $hasObjectTypeFilter )|| 
					$isDownloadable != 0 || 
					$isFree != 0 || 
					$isOrderable != 0){
					$condList[] = "<ogc:And>
									<ogc:PropertyIsEqualTo>
										<ogc:PropertyName>$ogcfilter_fileid</ogc:PropertyName>
										<ogc:Literal>-1</ogc:Literal>
									</ogc:PropertyIsEqualTo>\r\n
									<ogc:PropertyIsEqualTo>
										<ogc:PropertyName>harvested</ogc:PropertyName>
										<ogc:Literal>false</ogc:Literal>
									</ogc:PropertyIsEqualTo>\r\n
								</ogc:And>
								";
				}else{//Sinon on inclu les données harvestées
					$condList[] = "<ogc:PropertyIsEqualTo>
									<ogc:PropertyName>harvested</ogc:PropertyName>
									<ogc:Literal>true</ogc:Literal>
								</ogc:PropertyIsEqualTo>\r\n
							";
				}
			}
			
			if(count($arrSearchableMd) > 0)
				$condList[] = $cswMdCond;
			$cswMdCond = "";
		
			$cswfilterCond = "";
			foreach ($condList as $cond){
				$cswfilterCond .= $cond;
			}
			
			//Don't put an <and> if no other condition is requested
			if (count($condList) > 1) 
				$cswfilterCond = "<ogc:And>\r\n".$cswfilterCond."</ogc:And>\r\n";
			
			$cswfilter = "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
			$cswfilter .= $cswfilterCond;
			$cswfilter .= "</ogc:Filter>\r\n";
			
			$xmlBody = SITE_catalog::BuildCSWRequest($limit, $limitstart+1, "results", "gmd:MD_Metadata", "full", "1.1.0", $cswfilter, $ogcsearchsorting, "ASC");
			
			$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
			$cswResults= simplexml_load_string($xmlResponse);
			$myDoc = new DomDocument();
			$myDoc->loadXML($cswResults->asXML());
			
			$total = 0;
			if ($cswResults !=null)
			{
				// Contrôler si le XML ne contient pas une erreur
				if ($myDoc->childNodes->item(0)->nodeName== "ows:ExceptionReport")
				{
					$msg = $myDoc->childNodes->item(0)->nodeValue;
					$mainframe->enqueueMessage($msg,"ERROR");
					
					// Comportement identique que si aucune recherche n'a été faite
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

					// Si le nombre de résultats retournés a changé, adapter la page affichée
					if ($limitstart >= $total)
					{
						$limitstart = ( $limit != 0 ? ((floor($total / $limit) * $limit)-1) : 0 );
						$mainframe->setUserState('limitstart', $limitstart);
					}	
					
					if ($limitstart < 0)
					{
						$limitstart = 0;
						$mainframe->setUserState('limitstart', $limitstart);
					}
					
					$pageNav = new JPagination($total,$limitstart,$limit);
					$cswResults = DOMDocument::loadXML($xmlResponse);
				}
			}
		}
		else if ($languageChanged) // Si on a fait un changement de langue, recharger la page de départ
		{
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listCatalogContent&context='.$context), false ), $msg);
		}
		else // Si la recherche n'a pas été lancée, afficher une liste de résultats vide
		{
			$total=0;
			$pageNav=new JPagination($total,$limitstart,$limit);
			$cswResults = null;
		}
		
		$allVersions=true;
		
		HTML_catalog::listCatalogContentWithPan($pageNav,$cswResults,$option,$total,$simple_filterfreetextcriteria,$maxDescr, $allVersions, $listSimpleFilters, $listAdvancedFilters,$listHiddenFilters);
		
	}

	function BuildCSWRequest($maxRecords, $startPosition, $resultType, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder, $mode = 'CORE')
	{
		//Bug: If we have accents, we must specify ISO-8859-1
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$req .= "";
		
		//Get Records section
		$req .=  "<csw:GetRecords 
					xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" 
					service=\"CSW\" 
					version=\"2.0.2\" ";
		
		if ($resultType != "")
		{
			$req .= "resultType=\"$resultType\" 
					outputSchema=\"csw:IsoRecord\" 
					content=\"".$mode."\" ";
		}

		// add max records if not 0
		if($maxRecords != 0)
			$req .= "maxRecords=\"".$maxRecords."\" ";
		
		//add start position
		if($startPosition != 0)
			$req .= "startPosition=\"".$startPosition."\" ";
		
		$req .= "xmlns:ows=\"http://www.opengis.net/ows\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xsi:schemaLocation=\"http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd\">\r\n";
	
		//Query section
		//Types name
		$req .= "<csw:Query typeNames=\"".$typeNames."\">\r\n";
		if($elementSetName != "")
		{
			//ElementSetName
			$req .= "<csw:ElementSetName>".$elementSetName."</csw:ElementSetName>\r\n";
		}
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
		
		//echo htmlspecialchars(utf8_encode($req));
		return utf8_encode($req);
	}	
	
	function buildFilterConstraint ($isSimple,  
									$database,
									$language,
									$defaultSearch,
									$searchFilter,
									$mysqlFilter,
									$context,
									&$countFilters,
									&$cswFilter,
									&$arrFreetextMd,
									&$arrObjectNameMd,
									&$arrAccountsMd,
									&$arrManagersMd,
									&$arrCreatedMd,
									&$arrPublishedMd,
									&$hasObjectTypeFilter,
									&$arrObjecttypeMd,
									&$arrVersionMd,
									&$objecttype_id,
									&$empty,
									&$arrDownloadableMd,
									&$arrFreeMd,
									&$arrOrderableMd,
									&$isDownloadable,
									&$isFree,
									&$isOrderable){
		
		$filter = JRequest::getVar('filter_'.$searchFilter->guid);
		$lowerFilter = JRequest::getVar('create_cal_'.$searchFilter->guid);
		$upperFilter = JRequest::getVar('update_cal_'.$searchFilter->guid);
		
		$ogcfilter_fileid = config_easysdi::getValue("catalog_search_ogcfilterfileid");

		if ( 	isset($_REQUEST['filter_'.$searchFilter->guid]) or
				isset($_REQUEST['create_cal_'.$searchFilter->guid]) or
				isset($_REQUEST['update_cal_'.$searchFilter->guid]) ){
			switch ($searchFilter->attributetype_code){
				case "guid":
				case "text":
				case "locale":
				case "number":
				case "link":
					$countFilters++;
					/* Fonctionnement texte*/
					//Break the space in the request and split it in many terms
					$filter = $defaultSearch? $searchFilter->defaultvalue :$filter;
					$kwords = explode(" ", trim($filter));
					$ogcsearchfilter="";
					$database->setQuery("SELECT cscf.ogcsearchfilter
									   FROM #__sdi_context_sc_filter cscf
									   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
									   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
									   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
									   WHERE c.code='".$context."'
									   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
									   		 AND cl.code='".$language->_lang."'");
					$ogcsearchfilter = $database->loadResult();
						
					$terms=0;
					$cswTerm="";
					foreach ($kwords as $word)
					{
						if ($word <> "")
						{
							$cswTerm .= "
							 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
										<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
										<ogc:Literal>%$word%</ogc:Literal>
									</ogc:PropertyIsLike>\r\n
								";
							$terms++;
						}
					}
						
					// Réunir les critères
					if ($terms > 1)
					$cswTerm = "<ogc:And>".$cswTerm."</ogc:And>";
					$cswFilter.=$cswTerm;
					$empty = false;
					break;
				case "textchoice":
				case "localechoice":
					/* Fonctionnement liste de choix*/
					$filter = $defaultSearch? json_decode($searchFilter->defaultvalue) :$filter;
					if (count($filter) > 0 and $filter[0] <> "")
					{
						$countFilters++;
		
						$ogcsearchfilter="";
						$database->setQuery("SELECT cscf.ogcsearchfilter
										   FROM #__sdi_context_sc_filter cscf
										   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
										   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
										   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
										   WHERE c.code='".$context."'
										   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
										   		 AND cl.code='".$language->_lang."'");
						$ogcsearchfilter = $database->loadResult();
		
						foreach($filter as $f)
						{
							$localechoice ="";
							$list = array();
							$database->setQuery( "SELECT t.content
																  FROM #__sdi_attribute a, 
																  	   #__sdi_sys_stereotype at,  
																  	   #__sdi_codevalue c, 
																  	   #__sdi_translation t, 
																  	   #__sdi_language l, 
																  	   #__sdi_list_codelang cl 
																  WHERE a.id=c.attribute_id 
																  	    AND a.attributetype_id=at.id 
																  	    AND c.guid=t.element_guid 
																  	    AND t.language_id=l.id 
																  	    AND l.codelang_id=cl.id 
																  	    AND (at.code='textchoice' OR at.code='localechoice') 
																  	    AND c.id=".$f." 
																  	    AND c.published=true 
																  ORDER BY c.name" );
							$list = $database->loadObjectList();
		
							foreach ($list as $element)
							{
								$localechoice .= "
													<ogc:PropertyIsEqualTo>
														<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
														<ogc:Literal>$element->content</ogc:Literal>
													</ogc:PropertyIsEqualTo>
												";
							}
							if (count($list) > 1)
							$localechoice = "<ogc:Or>".$localechoice."</ogc:Or>";
							$cswFilter .= $localechoice;
						}
						if (count($filter) > 1)
						$cswFilter = "<ogc:Or>".$cswFilter."</ogc:Or>";
					}
						
					$empty = false;
					break;
				case "list":
					/* Fonctionnement liste*/
					$filter = $defaultSearch? json_decode($searchFilter->defaultvalue) :$filter;
					if (count($filter) > 0 and $filter[0] <> "")
					{
						$countFilters++;
						$ogcsearchfilter="";
						$database->setQuery("SELECT cscf.ogcsearchfilter
										   FROM #__sdi_context_sc_filter cscf
										   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
										   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
										   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
										   WHERE c.code='".$context."'
										   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
										   		 AND cl.code='".$language->_lang."'");
						$ogcsearchfilter = $database->loadResult();
		
						foreach($filter as $f)
						{
							$cswFilter .= "<ogc:PropertyIsEqualTo>
											<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
											<ogc:Literal>$f</ogc:Literal>
											</ogc:PropertyIsEqualTo> ";
						}
						if (count($filter) > 1)
						$cswFilter = "<ogc:Or>".$cswFilter."</ogc:Or>";
					}
						
					$empty = false;
					break;
				case "date":
				case "datetime":
					/* Fonctionnement période
					 * Format de date: 2001-01-15T20:07:48.11
					 * */
					$lowerFilter = $defaultSearch? ($searchFilter->defaultvaluefrom  == '0000-00-00')? null : $searchFilter->defaultvaluefrom :$lowerFilter;
					$upperFilter = $defaultSearch? ($searchFilter->defaultvalueto  == '0000-00-00')? null : $searchFilter->defaultvalueto :$upperFilter;
					
					$ogcsearchfilter="";
					$database->setQuery("SELECT cscf.ogcsearchfilter
									   FROM #__sdi_context_sc_filter cscf
									   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
									   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
									   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
									   WHERE c.code='".$context."'
									   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
									   		 AND cl.code='".$language->_lang."'");
					$ogcsearchfilter = $database->loadResult();
					if($lowerFilter != "" || $upperFilter != ""){
						if ($lowerFilter == "") // Seulement la borne sup
						{
							$countFilters++;
							$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
							$cswFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
											<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
											<ogc:Literal>$upperFilter</ogc:Literal>
											</ogc:PropertyIsLessThanOrEqualTo> ";
						}
						else if ($upperFilter == "") // Seulement la borne inf
						{
							$countFilters++;
							$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
							$cswFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
											<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
											<ogc:Literal>$lowerFilter</ogc:Literal>
											</ogc:PropertyIsGreaterThanOrEqualTo> ";
						}
						else // Les deux bornes
						{
							$countFilters++;
							$lowerFilter = date('Y-m-d', strtotime($lowerFilter))."T00:00:00.00";
							$upperFilter = date('Y-m-d', strtotime($upperFilter))."T23:59:59.59";
							$cswFilter .= "<ogc:PropertyIsBetween>
											<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
											<ogc:LowerBoundary>
												<ogc:Literal>$lowerFilter</ogc:Literal>
											</ogc:LowerBoundary>
											<ogc:UpperBoundary>
												<ogc:Literal>$upperFilter</ogc:Literal>
											</ogc:UpperBoundary>
											</ogc:PropertyIsBetween> ";
						}
					}
						
					$empty = false;
					break;
				default:
					break;
			}
		}else // Traiter les filtres qui ne sont pas liés à des relations, si ils existent
		{
			if ($searchFilter->criteriatype_id == 1) {
				// Filtres systèmes
				// Récupération des filtres standards
				switch ($searchFilter->code){
					case "fulltext":
						$simple_filterfreetextcriteria = $defaultSearch? $searchFilter->defaultvalue : JRequest::getVar('simple_filterfreetextcriteria');
						if ($simple_filterfreetextcriteria <> "")
						{
							$countFilters++;
							// Filtre sur le texte (Critères de recherche simple)
							$kwords = explode(" ", trim($simple_filterfreetextcriteria));
		
							// Filtres OGC directs pour les champs titre(title), description(abstract) et GEMET (keyword)
							//Break the space in the request and split it in many terms
							$title=0;
							$keyword=0;
							$abstract=0;
							$cswFreeFilters = 0;
							$cswTitle="";
							$cswKeyword="";
							$cswAbstract="";
							$cswObjectName="";
							$cswAccountId="";
								
							$defaultLang=false;
							$suffix="";
								
							if ($isSimple){
								foreach($this->langList as $lang){
									if ($lang->code_easysdi == $language->_lang){
										if ($lang->defaultlang)
										$defaultLang=true;
										else
										$suffix="_".$lang->code;
									}
								}
									
								foreach ($kwords as $word){
									if ($word <> ""){
										$word = utf8_encode(strtolower(utf8_decode($word)));
										if ($defaultLang){
											$cswTitle .= "
										 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
													<ogc:PropertyName>mainsearch</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>\r\n
											";
											$title++;
											$cswKeyword .= "
										 		<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
													<ogc:PropertyName>mainsearch</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>\r\n
											";
											$keyword++;
											$cswAbstract .= "
										 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
											     	<ogc:PropertyName>mainsearch</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>
											";
											$abstract++;
								
											$cswFreeFilters++;
										}else{
											$cswTitle .= "
										 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
													<ogc:PropertyName>mainsearch$suffix</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>\r\n
											";
											$title++;
											$cswKeyword .= "
										 		<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
													<ogc:PropertyName>mainsearch$suffix</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>\r\n
											";
											$keyword++;
											$cswAbstract .= "
										 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
											     	<ogc:PropertyName>mainsearch$suffix</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>
											";
											$abstract++;
											$cswFreeFilters++;
										}
									}
								}
							}else{
								foreach($this->langList as $lang){
									if ($lang->defaultlang)
									if ($lang->code_easysdi == $language->_lang)
									$defaultLang=true;
								}
								
								foreach ($kwords as $word){
									if ($word <> ""){
										$word = utf8_encode(strtolower(utf8_decode($word)));
										if ($defaultLang){
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
										else
										{
											$cswTitle .= "
										 	 	<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escapeChar=\"\\\">
													<ogc:PropertyName>title_trad</ogc:PropertyName>
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
											     	<ogc:PropertyName>abstract_trad</ogc:PropertyName>
													<ogc:Literal>%$word%</ogc:Literal>
												</ogc:PropertyIsLike>
											";
											$abstract++;
											$cswFreeFilters++;
										}
									}
								}
							}
							
							// Réunir chaque critère
							if ($title > 1){
								$cswTitle = "<ogc:And>".$cswTitle."</ogc:And>";
							}
							if ($keyword > 1){
								$cswKeyword = "<ogc:And>".$cswKeyword."</ogc:And>";
							}
							if ($abstract > 1){
								$cswAbstract = "<ogc:And>".$cswAbstract."</ogc:And>";
							}
								
							// Filtres sur les guid de métadonnées pour le code et le fournisseur
							// Sélectionner tous les objets dont le nom ressemble au texte saisi
							$query = "SELECT o.id
										  FROM #__sdi_object o 
										  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
										  WHERE o.name LIKE '%".$simple_filterfreetextcriteria."%' ";
							$database->setQuery( $query);
							$objectlist = $database->loadObjectList() ;
								
							$arrFreetextMd=array();
							// Construire la liste des guid à filtrer
							// Pour chaque objet, sélectionner toutes ses versions
							foreach ($objectlist as $object){
								$query = "SELECT m.guid as metadata_id
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.id=".$object->id
								.$mysqlFilter.
										 " ORDER BY ov.created DESC";
								$database->setQuery( $query);
								$objectnamelist = $database->loadObjectList() ;
		
								if (count($objectnamelist) > 1)
								$cswObjectName.= "<ogc:Or>";
								foreach ($objectnamelist as $on){
									$arrFreetextMd[] = $on->metadata_id;
									$empty = false;
									$cswObjectName .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>$ogcfilter_fileid</ogc:PropertyName><ogc:Literal>$on->metadata_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
								}
								if (count($objectnamelist) > 1)
								$cswObjectName.= "</ogc:Or>";
							}
								
							// Sélectionner tous les objets dont le nom du fournisseur ressemble au texte saisi
							$query = "SELECT o.id
										  FROM #__sdi_object o 
										  INNER JOIN #__sdi_account a ON a.id=o.account_id
										  WHERE a.name LIKE '%".$simple_filterfreetextcriteria."%' ";
							$database->setQuery( $query);
							$objectlist = $database->loadObjectList() ;
								
							// Construire la liste des guid à filtrer
							// Pour chaque objet, sélectionner toutes ses versions
							foreach ($objectlist as $object){
								$query = "SELECT m.guid as metadata_id
											  FROM #__sdi_objectversion ov 
											  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
											  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
											  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
											  WHERE o.id=".$object->id
								.$mysqlFilter.
											 " ORDER BY ov.created DESC";
								$database->setQuery( $query);
								$accountlist = $database->loadObjectList() ;
		
								if (count($accountlist) > 1)
								$cswAccountId.= "<ogc:Or>";
								foreach ($accountlist as $a){
									$arrFreetextMd[] = $a->metadata_id;
									$empty = false;
									$cswAccountId .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>$ogcfilter_fileid</ogc:PropertyName><ogc:Literal>$a->metadata_id</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
								}
								if (count($accountlist) > 1)
								$cswAccountId.= "</ogc:Or>";
							}
								
							// Réunir tous les critères
							if($cswFreeFilters > 0)
								$cswFilter = "<ogc:Or>".$cswTitle.$cswKeyword.$cswAbstract.$cswObjectName.$cswAccountId."</ogc:Or>";
						}
						break;
					case "isDownloadable":
						$isDownloadable = $defaultSearch? $searchFilter->defaultvalue : JRequest::getVar('systemfilter_'.$searchFilter->guid, 0);
						if ($isDownloadable !=0 )
						{
							$query = "SELECT m.guid as metadata_id
														  FROM #__sdi_objectversion ov 
														  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
														  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
														  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
														  INNER JOIN #__sdi_product p ON ov.id=p.objectversion_id
														  WHERE p.available = 1 and p.published=1"
							.$mysqlFilter;
							$database->setQuery( $query);
							$arrDownloadableMd=array();
							$arrDownloadableMd = $database->loadResultArray() ;
						}
						break;
					case "isFree":
						$isFree = $defaultSearch? $searchFilter->defaultvalue : JRequest::getVar('systemfilter_'.$searchFilter->guid, 0);
						if ($isFree !=0 )
						{
							$query = "SELECT m.guid as metadata_id
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  INNER JOIN #__sdi_product p ON ov.id=p.objectversion_id
												  WHERE p.free = 1 and p.published=1"
							.$mysqlFilter;
							$database->setQuery( $query);
							$arrFreeMd = array();
							$arrFreeMd = $database->loadResultArray() ;
						}
						break;
					case "isOrderable":
						$isOrderable = $defaultSearch? $searchFilter->defaultvalue : JRequest::getVar('systemfilter_'.$searchFilter->guid, 0);
						if ($isOrderable !=0 )
						{
							$query = "SELECT m.guid as metadata_id
												  FROM #__sdi_objectversion ov 
												  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
												  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
												  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
												  INNER JOIN #__sdi_product p ON ov.id=p.objectversion_id
												  WHERE p.available = 0 and p.published=1"
							.$mysqlFilter;
							$database->setQuery( $query);
							$arrOrderableMd = array();
							$arrOrderableMd = $database->loadResultArray() ;
						}
						break;
					case "objecttype":
						$objecttype_id = $defaultSearch? json_decode($searchFilter->defaultvalue) :JRequest::getVar('systemfilter_'.$searchFilter->guid);
		
						//Des types d'objets ont été sélectionnés dans l'interface de recherche
						//On filtre selon ces types et on écarte automatiquement les données harvestées (ceci est fait plus loin dans la construction de la requête)
						if (count($objecttype_id) > 0 ){
							$hasObjectTypeFilter = true;
							//$arrObjecttypeMd=null;
		
							$list_id=array();
							if (implode(",", $objecttype_id) <> "")
							{
								$query = "SELECT m.guid as metadata_id
														  FROM #__sdi_objectversion ov 
														  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
														  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
														  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
														  WHERE o.objecttype_id IN (".implode(",", $objecttype_id).") "
								.$mysqlFilter;
								$database->setQuery( $query);
								$list_id = $database->loadObjectList() ;
							}
		
							$arrObjecttypeMd =array();
							if(count($list_id) == 0)
							{
								$arrObjecttypeMd[] = -1;
								break;
							}
								
						}else if (  !array_key_exists('objecttype_id', $_REQUEST) and 
									!array_key_exists('bboxMinX', $_REQUEST)) {// Cas du premier appel. Rechercher sur tous les types
							$hasObjectTypeFilter = false;
							$objecttypes = array();
							if ($context <> "")
							{
								// Récupérer tous les types d'objets du contexte
								$database->setQuery("SELECT id FROM #__sdi_objecttype WHERE id IN
																	(SELECT co.objecttype_id 
																	FROM #__sdi_context_objecttype co
																	INNER JOIN #__sdi_context c ON c.id=co.context_id 
																	WHERE c.code = '".$context."')
															   ORDER BY name");
							}
							else
							{
								// Récupérer tous les types d'objets définis
								$database->setQuery("SELECT id FROM #__sdi_objecttype ORDER BY name");
							}
							$objecttypes = $database->loadResultArray();
								
							// Récupérer toutes les métadonnées de ces types d'objets
							$query = "SELECT m.guid as metadata_id
													  FROM #__sdi_objectversion ov 
													  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
													  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
													  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
													  WHERE o.objecttype_id IN (".implode(",", $objecttypes).") "
							.$mysqlFilter;
							$database->setQuery( $query);
							$list_id = $database->loadObjectList() ;
						}else if (count($objecttype_id) == 0){ //Aucun type d'objet sélectionné, on récupère donc TOUS les types d'objet du context
							$hasObjectTypeFilter = false;
							$objecttypes = array();
							if ($context <> "")
							{
								// Récupérer tous les types d'objets du contexte
								$database->setQuery("SELECT id FROM #__sdi_objecttype WHERE id IN
																	(SELECT co.objecttype_id 
																	FROM #__sdi_context_objecttype co
																	INNER JOIN #__sdi_context c ON c.id=co.context_id 
																	WHERE c.code = '".$context."')
															   ORDER BY name");
							}
							else
							{
								// Récupérer tous les types d'objets définis
								$database->setQuery("SELECT id FROM #__sdi_objecttype ORDER BY name");
							}
							$objecttypes = $database->loadResultArray();
								
							// Récupérer toutes les métadonnées de ces types d'objets
							$query = "SELECT m.guid as metadata_id
													  FROM #__sdi_objectversion ov 
													  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
													  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
													  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
													  WHERE o.objecttype_id IN (".implode(",", $objecttypes).") "
							.$mysqlFilter;
							$database->setQuery( $query);
							$list_id = $database->loadObjectList() ;
						}
		
						$arrObjecttypeMd =array();
						foreach ($list_id as $md_id)
						{
							$arrObjecttypeMd[] = $md_id->metadata_id;
						}
		
						if(count($list_id)> 0)
							$empty = false;
		
						break;
					case "versions":
						$versions = $defaultSearch? $searchFilter->defaultvalue :JRequest::getVar('systemfilter_'.$searchFilter->guid);
						if ($versions == "0" or !array_key_exists('systemfilter_'.$searchFilter->guid, $_REQUEST)) 
						// Cas du premier appel et des versions actuelles. Rechercher sur les dernières versions publiées à la date courante
						{
							// Si l'utilisateur a choisi de ne chercher que sur les versions actuelles,
							// ajouter un filtre. Sinon ne rien faire
							// Sélectionner tous les objets
							$query = "SELECT o.id
									  FROM #__sdi_object o 
									  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
									  WHERE 1 "
										.$mysqlFilter;
							$database->setQuery( $query);
							$objectlist = $database->loadObjectList() ;
							// Construire la liste des guid à filtrer
							
							$arrVersionMd =array();
							// Pour chaque objet, sélectionner toutes ses versions publiées
							foreach ($objectlist as $object)
							{
								$query = "SELECT m.guid as metadata_id, ms.code, m.published
															  FROM #__sdi_objectversion ov 
															  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
															  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
															  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
															  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
															  WHERE o.id=".$object->id.
															 "		AND ((ms.code='published'
															 			AND m.published <='".date('Y-m-d')."')
															 		OR
															 			(ms.code='archived'
															 			AND m.archived >'".date('Y-m-d')."')
															 		) 
															  ORDER BY m.published DESC
															  LIMIT 0, 1";
								$database->setQuery( $query);
								$versionlist = $database->loadObjectList() ;
								
								if (count($versionlist))
								{
									// Si la dernière version est publiée à la date courante, on l'utilise
									$arrVersionMd[] = $versionlist[0]->metadata_id;
									$empty = false;
								}
							}
						}
						else if ($versions == "1") // Rechercher sur toutes les versions publiées à la date courante
						{
							// Sélectionner tous les objets
							$query = "SELECT o.id
										  FROM #__sdi_object o 
										  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
										  WHERE o.published=1 or o.published=0 "
							.$mysqlFilter;
							$database->setQuery( $query);
							$objectlist = $database->loadObjectList() ;
							// Construire la liste des guid à filtrer
							$arrVersionMd = array();
								
							// Pour chaque objet, sélectionner toutes ses versions
							foreach ($objectlist as $object)
							{
								$query = "SELECT m.guid as metadata_id, ms.code, m.published
															  FROM #__sdi_objectversion ov 
															  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
															  INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
															  INNER JOIN #__sdi_object o ON ov.object_id=o.id 
															  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
															  WHERE o.id=".$object->id.
															 "		AND ((ms.code='published'
															 			AND m.published <='".date('Y-m-d')."')
															 		OR
															 			(ms.code='archived'
															 			AND m.archived >'".date('Y-m-d')."')
															 		) 
															  ORDER BY m.published DESC";
								$database->setQuery( $query);
								$versionlist = $database->loadObjectList() ;
								
								foreach ($versionlist as $v)
								{
									$arrVersionMd[] = $v->metadata_id;
										
									$empty = false;
								}
							}
						}
						break;
					case "object_name":
						$object_name = $defaultSearch? $searchFilter->defaultvalue :JRequest::getVar('systemfilter_'.$searchFilter->guid);
						if ($object_name <> "")
						{
							$countFilters++;
							// Sélectionner tous les objets dont le nom ressemble au texte saisi
							$query = "SELECT o.id
													  FROM #__sdi_object o 
													  INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
													  WHERE o.name LIKE '%".$object_name."%' ";
							$database->setQuery( $query);
							$objectlist = $database->loadObjectList() ;
							$arrObjectNameMd=array();
							// Construire la liste des guid à filtrer
							// Pour chaque objet, sélectionner toutes ses versions
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
								$objectnamelist = $database->loadObjectList() ;
								
								foreach ($objectnamelist as $on)
								{
									$arrObjectNameMd[] = $on->metadata_id;
		
									$empty = false;
								}
							}
						}
						break;
					case "metadata_created":
						$lower = $defaultSearch? ($searchFilter->defaultvaluefrom  == '0000-00-00')? null : $searchFilter->defaultvaluefrom : JRequest::getVar('systemfilter_create_cal_'.$searchFilter->guid);
						$upper = $defaultSearch? ($searchFilter->defaultvalueto  == '0000-00-00')? null : $searchFilter->defaultvalueto : JRequest::getVar('systemfilter_update_cal_'.$searchFilter->guid);

						// Sélectionner toutes les métadonnées créées dans l'intervalle indiqué
						if ($lower == "" and $upper <> "") // Seulement la borne sup
						{
							$countFilters++;
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
							$arrCreatedMd =array();
							
							foreach ($mdlist as $md){
								$arrCreatedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						else if ($upper == "" and $lower <> "") // Seulement la borne inf
						{
							$countFilters++;
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
							$arrCreatedMd =array();
							foreach ($mdlist as $md){
								$arrCreatedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						else if ($upper <> "" and $lower <> "") // Les deux bornes
						{
								
							$countFilters++;
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
							$arrCreatedMd = array();
							
							foreach ($mdlist as $md)
							{
								$arrCreatedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						break;
					case "metadata_published":
						$lower = $defaultSearch? ($searchFilter->defaultvaluefrom  == '0000-00-00')? null : $searchFilter->defaultvaluefrom : JRequest::getVar('systemfilter_create_cal_'.$searchFilter->guid);
						$upper = $defaultSearch? ($searchFilter->defaultvalueto  == '0000-00-00')? null : $searchFilter->defaultvalueto : JRequest::getVar('systemfilter_update_cal_'.$searchFilter->guid);
						
						// Sélectionner toutes les métadonnées créées dans l'intervalle indiqué
						if ($lower == "" and $upper <> "") // Seulement la borne sup
						{
							$countFilters++;
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
							$arrPublishedMd =array();
							foreach ($mdlist as $md)
							{
								$arrPublishedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						else if ($upper == "" and $lower <> "") // Seulement la borne inf
						{
							$countFilters++;
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
							$arrPublishedMd =array();
							foreach ($mdlist as $md)
							{
								$arrPublishedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						else if($upper <> "" and $lower <> "") // Les deux bornes
						{
							$countFilters++;
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
							$arrPublishedMd =array();
							foreach ($mdlist as $md)
							{
								$arrPublishedMd[] = $md->metadata_id;
								$empty = false;
							}
						}
						break;
					case "managers":
						$managers = $defaultSearch? $searchFilter->defaultvalue :JRequest::getVar('systemfilter_'.$searchFilter->guid);
		
						if (count($managers) > 0 and $managers[0] <> "")
						{
							$countFilters++;
							$objectlist = array();
							if (implode(",", $managers) <> "")
							{
								// Sélectionner tous les objets dont le nom ressemble au texte saisi
								$query = "SELECT o.id
														  FROM #__sdi_object o 
														  INNER JOIN #__sdi_manager_object mo ON mo.object_id=o.id
														  WHERE mo.account_id IN (".implode(", ", $managers).") ";
								$database->setQuery( $query);
								$objectlist = $database->loadObjectList() ;
							}
							$arrManagersMd = array();
							// Construire la liste des guid à filtrer
							// Pour chaque objet, sélectionner toutes ses versions
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
								$managerlist = $database->loadObjectList() ;
								
								foreach ($managerlist as $m)
								{
									$arrManagersMd[] = $m->metadata_id;
		
									$empty = false;
								}
							}
						}
						break;
					case "title":
						$metadata_title = $defaultSearch? $searchFilter->defaultvalue :JRequest::getVar('systemfilter_'.$searchFilter->guid);
		
						if ($metadata_title <> "")
						{
							$countFilters++;
							$ogcsearchfilter="";
							$database->setQuery("SELECT cscf.ogcsearchfilter
											   FROM #__sdi_context_sc_filter cscf
											   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
											   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
											   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
											   WHERE c.code='".$context."'
											   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
											   		 AND cl.code='".$language->_lang."'");
							$ogcsearchfilter = $database->loadResult();
								
							$cswFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
											<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
											<ogc:Literal>%$metadata_title%</ogc:Literal>
											</ogc:PropertyIsLike> ";
								
							$empty = false;
						}
						break;
					case "account_id":
						$accounts = $defaultSearch? json_decode($searchFilter->defaultvalue) :JRequest::getVar('systemfilter_'.$searchFilter->guid);

						if (count($accounts) > 0 and $accounts[0] <> "")
						{
							$countFilters++;
							$objectlist=array();
							if (implode(",", $accounts) <> "")
							{
								// Sélectionner tous les objets dont le nom ressemble au texte saisi
								$query = "SELECT o.id
											  FROM #__sdi_object o 
											  WHERE o.account_id IN (".implode(", ", $accounts).") ";
								$database->setQuery( $query);
								$objectlist = $database->loadObjectList() ;
							}
							$arrAccountsMd = array();
							// Construire la liste des guid à filtrer
							// Pour chaque objet, sélectionner toutes ses versions
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
								$accountlist = $database->loadObjectList() ;
								if ($database->getErrorNum())
								{
									$msg = $database->getErrorMsg();
								}
								
								foreach ($accountlist as $a)
								{
									$arrAccountsMd[] = $a->metadata_id;
		
									$empty = false;
								}
							}
								
							//If no result, give an unexisting id back
							if (count($objectlist) == 0)
							{
								$arrAccountsMd[] = -1;
							}
							
							
						}
						break;
					default:
						break;
				}
			}
			else // Cas des attributs OGC qui ne sont pas liés à une relation
			{
				switch ($searchFilter->rendertype_code)
				{
					case "date":
						$ogcsearchfilter="";
						$database->setQuery("SELECT cscf.ogcsearchfilter
										   FROM #__sdi_context_sc_filter cscf
										   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
										   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
										   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
										   WHERE c.code='".$context."'
										   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
										   		 AND cl.code='".$language->_lang."'");
						$ogcsearchfilter = $database->loadResult();
		
						$lower = $defaultSearch? $searchFilter->defaultvaluefrom :JRequest::getVar('filter_create_cal_'.$searchFilter->guid);
						$upper = $defaultSearch? $searchFilter->defaultvalueto :JRequest::getVar('filter_update_cal_'.$searchFilter->guid);
		
						// Sélectionner toutes les métadonnées créées dans l'intervalle indiqué
						if ($lower == "" and $upper <> "") // Seulement la borne sup
						{
							$countFilters++;
							$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
								
							$cswFilter .= "<ogc:PropertyIsLessThanOrEqualTo>
												<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>$upper</ogc:Literal>
												</ogc:PropertyIsLessThanOrEqualTo> ";
						}
						else if ($upper == "" and $lower <> "") // Seulement la borne inf
						{
							$countFilters++;
							$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
								
							$cswFilter .= "<ogc:PropertyIsGreaterThanOrEqualTo>
												<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>$lower</ogc:Literal>
												</ogc:PropertyIsGreaterThanOrEqualTo> ";
						}
						else if ($upper <> "" and $lower <> "") // Les deux bornes
						{
							$countFilters++;
							$lower = date('Y-m-d', strtotime($lower)); //."T00:00:00";
							$upper = date('Y-m-d', strtotime($upper)); //."T23:59:59";
								
							$cswFilter .= "<ogc:PropertyIsBetween>
												<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
												<ogc:LowerBoundary><ogc:Literal>$lower</ogc:Literal></ogc:LowerBoundary>
												<ogc:UpperBoundary><ogc:Literal>$upper</ogc:Literal></ogc:UpperBoundary>
												</ogc:PropertyIsBetween> ";
						}
						break;
					case "textbox":
					default:
						$filter = $defaultSearch? $searchFilter->defaultvalue :JRequest::getVar('filter_'.$searchFilter->guid);
		
						if ($filter <> "")
						{
							$countFilters++;
								
							$ogcsearchfilter="";
							$database->setQuery("SELECT cscf.ogcsearchfilter
											   FROM #__sdi_context_sc_filter cscf
											   LEFT OUTER JOIN #__sdi_context c ON cscf.context_id=c.id
											   LEFT OUTER JOIN #__sdi_language l ON cscf.language_id=l.id
											   LEFT OUTER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
											   WHERE c.code='".$context."'
											   		 AND cscf.searchcriteria_id='".$searchFilter->id."' 
											   		 AND cl.code='".$language->_lang."'");
							$ogcsearchfilter = $database->loadResult();
								
							$cswFilter .= "<ogc:PropertyIsLike wildCard=\"%\" singleChar=\"_\" escape=\"\\\">
												<ogc:PropertyName>$ogcsearchfilter</ogc:PropertyName>
												<ogc:Literal>%$filter%</ogc:Literal>
												</ogc:PropertyIsLike> ";
							$empty = false;
						}
						break;
				}
			}
		}
	
	}

	function getFilterForBoundaryParent(&$filter, $parent_id, $boundarysearchfield, $categorysearchfield){
		$database =& JFactory::getDBO();
		$query =  "SELECT * FROM #__sdi_boundary where id ='".$parent_id."'" ;
		$database->setQuery( $query);
		$parentBoundary = $database->loadObject() ;
		
		//build the filter
		$query =  "SELECT title FROM #__sdi_translation where element_guid ='".$parentBoundary->guid."'" ;
		$database->setQuery( $query);
		$boundaryTitle = $database->loadResult() ;
			
		$query =  "SELECT alias FROM #__sdi_boundarycategory WHERE id = ".$parentBoundary->category_id ;
		$database->setQuery( $query);
		$categoryLabel = $database->loadResult() ;
			
			
		$filter .="
		<ogc:and>
		<ogc:PropertyIsEqualTo>
		<ogc:PropertyName>".$boundarysearchfield."</ogc:PropertyName>
		<ogc:Literal>".$boundaryTitle."</ogc:Literal>
		</ogc:PropertyIsEqualTo>
		<ogc:PropertyIsEqualTo>
		<ogc:PropertyName>".$categorysearchfield."</ogc:PropertyName>
		<ogc:Literal>".$categoryLabel."</ogc:Literal>
		</ogc:PropertyIsEqualTo>
		</ogc:and>
		";
		
		
		if(isset($parentBoundary->parent_id)){
			return getFilterForBoundaryParent($filter,$parentBoundary->parent_id, $boundarysearchfield, $categorysearchfield);
		}else{
			return $filter;
		}
	}
}
?>