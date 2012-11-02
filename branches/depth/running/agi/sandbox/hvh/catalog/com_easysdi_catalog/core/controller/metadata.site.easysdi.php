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

class SITE_metadata {
	var $langList = array ();
	
	function listMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		$language =& JFactory::getLanguage();
		
		//Check user's rights
		$allow = false;
		$allow = userManager::isUserAllowed($user,"PRODUCT");
		if (!$allow)
		{
			$mainframe->_messageQueue=array(); // Seul le message lie au droit d'edition sera conserve, s'il y a lieu
			$allow = userManager::isUserAllowed($user,"METADATA");	
		}
		
		if(!$allow)
			return;
		
		$option=JRequest::getVar("option");
		$context	= $option.'.listMetadata';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		// Search and ordering
		$filter_order			= $mainframe->getUserStateFromRequest( $option.".filter_order",		'filter_order',		'name',	'word' );
		$filter_order_Dir		= $mainframe->getUserStateFromRequest( $option.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		$filter_objecttype_id 	= $mainframe->getUserStateFromRequest( $option.'filter_md_objecttype_id',	'filter_md_objecttype_id',	-1,	'int' );
		$filter_md_state_id 	= $mainframe->getUserStateFromRequest( $option.'filter_md_state_id',	'filter_md_state_id',	-1,	'int' );
		$filter_md_version 		= $mainframe->getUserStateFromRequest( $option.'filter_md_version',	'filter_md_version',	-1,	'int' );
		$search 				= $mainframe->getUserStateFromRequest( "searchMetadataName{$option}", 'searchMetadataName', '' );
		
		$filter = "";
		if ( $search ) 
		{
			if(strripos ($search,'"') != FALSE)
			{
				$searchcontent = substr($search, 1,strlen($search)-2 );
				$searchcontent = $database->getEscaped( trim( strtolower( $searchcontent ) ) );
				$filter .= " AND (o.name LIKE '%".$searchcontent."%')";
			}
			else
			{
				$search = $database->getEscaped( trim( strtolower( $search ) ) );
				$filter .= " AND (o.name = '$search')";
			}
		}
		
		// Test si le filtre est valide
		if ($filter_order <> "name" 
			and $filter_order <> "version_title"
			and $filter_order <> "state"
			and $filter_order <> "objecttype")
		{
			$filter_order		= "name";
			$filter_order_Dir	= "ASC";
		}
		
		$account = new accountByUserId($database);
		$account->load($user->id);

		$rootAccount = new account($database);
		$rootAccount->load($account->root_id);		
		
		// Si le compte n'a pas de root, c'est qu'il l'est lui-meme
		if (!$rootAccount->id)
			$rootAccount = $account;
		
		// Objecttype filter
		if ($filter_objecttype_id > 0) {
			$filter .= ' AND o.objecttype_id = ' . (int) $filter_objecttype_id;
		}
		
		// State filter
		if ($filter_md_state_id > 0) {
			$filter .= ' AND m.metadatastate_id = ' . (int) $filter_md_state_id;
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
			
		$queryCount = "	SELECT DISTINCT o.id 
						FROM 	#__sdi_metadata m, 
								#__sdi_list_metadatastate s, 
								#__sdi_objecttype ot,
								#__sdi_objectversion ov,
								#__sdi_object o
						LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
						LEFT OUTER JOIN #__sdi_editor_object e ON e.object_id=o.id
						WHERE ov.object_id=o.id
							AND ov.metadata_id=m.id
							AND m.metadatastate_id=s.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND (e.account_id = ".$account->id."
								OR (ma.account_id=".$account->id.")
								)";
		$queryCount .= $filter;
		
		$database->setQuery($queryCount);
		$total = count($database->loadObjectList());
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		// Si le nombre de resultats retournes a change, adapter la page affichee
		if ($limitstart >= $total)
		{
			$limitstart = ( $limit != 0 ? ((floor($total / $limit) * $limit)-1) : 0 );
			$mainframe->setUserState('limitstart', $limitstart);
		}	
		
		if ($limitstart < 0)
			$limitstart = 0;
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "	SELECT DISTINCT o.*,
							t.label as objecttype,
							ov.id as version_id,
							ov.title as version_title,
							s.label as state,
							m.guid as metadata_guid ,
							m.lastsynchronization as lastsynchronization,
							m.synchronizedby as synchronizedby,
							m.notification as notification
					FROM 	#__sdi_metadata m,
							#__sdi_list_metadatastate s,
							#__sdi_objectversion ov,
							#__sdi_object o
					LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
					LEFT OUTER JOIN #__sdi_editor_object e ON e.object_id=o.id
					INNER JOIN #__sdi_objecttype ot ON ot.id = o.objecttype_id
					INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
					INNER JOIN #__sdi_language l ON t.language_id=l.id
					INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
						WHERE ov.object_id=o.id
							AND ov.metadata_id=m.id
							AND m.metadatastate_id=s.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND cl.code='".$language->_lang."'
							AND (e.account_id = ".$account->id."
							OR (ma.account_id=".$account->id.")
							)";
		$query .= $filter;
		$query .= $orderby;
			
		$database->setQuery($query,$limitstart,$limit);
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
		// Version filter
		if ($filter_md_version == 0) {
			//Return only the last metadata for each object
			$arrVersionMd =array();
			foreach ($rows as $object)
			{
				$query = "SELECT m.id as metadata_id, ms.code, m.published
				FROM #__sdi_objectversion ov
				INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
				INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
				INNER JOIN #__sdi_object o ON ov.object_id=o.id
				INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
				INNER JOIN (SELECT v.id,v.object_id, max(v.created) as maxcreated from #__sdi_objectversion v group by v.object_id) vv ON vv.object_id = ov.object_id AND vv.maxcreated = ov.created
				WHERE o.id=".$object->id."
				GROUP BY ov.object_id
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
			
			if(count($arrVersionMd) == 0)
			{
				$rows = array();
			}
			else
			{
				$str_version = implode(",",$arrVersionMd);
				
				$query = "	SELECT DISTINCT o.*,
									t.label as objecttype,
									ov.id as version_id,
									ov.title as version_title,
									s.label as state,
									m.guid as metadata_guid ,
									m.lastsynchronization as lastsynchronization,
									m.notification as notification
							FROM 	#__sdi_metadata m,
									#__sdi_list_metadatastate s,
									#__sdi_objectversion ov,
									#__sdi_object o
							LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
							LEFT OUTER JOIN #__sdi_editor_object e ON e.object_id=o.id
							INNER JOIN #__sdi_objecttype ot ON ot.id = o.objecttype_id
							INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
							INNER JOIN #__sdi_language l ON t.language_id=l.id
							INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
								WHERE ov.object_id=o.id
									AND ov.metadata_id=m.id
									AND m.id IN ($str_version)
									AND m.metadatastate_id=s.id
									AND ot.id=o.objecttype_id
									AND ot.predefined=0
									AND cl.code='".$language->_lang."'
									AND (e.account_id = ".$account->id."
									OR (ma.account_id=".$account->id.")
									)";
				$query .= $filter;
				$query .= $orderby;
					
				$database->setQuery($query,$limitstart,$limit);
				$rows = $database->loadObjectList() ;
				if ($database->getErrorNum()) {
					echo "<div class='alert'>";
					echo 			$database->getErrorMsg();
					echo "</div>";
				}
			}
		}
		
		$query = "SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY t.label";
		
		$listObjectType[] = JHTML::_('select.option', '0', JText::_('CATALOG_METADATA_SELECT_OBJECTTYPE'), 'value', 'text');
		$database->setQuery($query);
		$listObjectType = array_merge($listObjectType, $database->loadObjectList());
		
		$query = "SELECT 0 as value, 'CATALOG_METADATA_SELECT_OBJECTTYPE' as text UNION 
					SELECT id AS value, label as text
					FROM #__sdi_list_metadatastate";
		$database->setQuery($query);
		$listState =  $database->loadObjectList();
		foreach ($listState as $state)
			$state->text = JText::_($state->text);
		
		// Choix radio pour les versions
		$versions = array(
			JHTML::_('select.option',  '0', JText::_( 'CATALOG_SEARCH_VERSIONS_CURRENT' ) ),
			JHTML::_('select.option',  '1', JText::_( 'CATALOG_SEARCH_VERSIONS_ALL' ) )
		);
		
		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		
		HTML_metadata::listMetadata($pageNav,$rows,$option,$rootAccount, $listObjectType, $listState, $filter_objecttype_id, $filter_md_state_id, $filter_md_version,$versions, $search, $lists);	
		
	}
	
	function editMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$language =& JFactory::getLanguage();
		if ($language->_lang == "fr-FR") 
			JHTML::script('ext-lang-fr.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else if ($language->_lang == "de-DE") 
			JHTML::script('ext-lang-de.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		
		if ($id == 0)
		{
			$msg = JText::_('CATALOG_OBJECT_SELECTMETADATA_MSG');
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ), $msg);
			exit;
		}
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObjectVersion = new objectversion( $database );
		$rowObjectVersion->load( $id );
		
		$rowObject = new object( $database );
		$rowObject->load( $rowObjectVersion->object_id );
		
		// Recuperer la metadonnee choisie par l'utilisateur
		$rowMetadata = new metadata( $database );
		$rowMetadata->load( $rowObjectVersion->metadata_id );
		
		if ($rowMetadata->id == 0)
		{
			$msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ), $msg);
		}
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowObject->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowObject->name);
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObject'), false ), $msg);
		}

		$rowObject->checkout($user->get('id'));
		
		// Stocker en memoire toutes les traductions de label, valeur par defaut et information pour la langue courante
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
		
		$metadatastates = array();
		$metadatastates[] = JHTML::_('select.option','0', JText::_("CORE_METADATASTATE_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_metadatastate ORDER BY name" );
		$metadatastates = array_merge( $metadatastates, $database->loadObjectList() );
		
		// Recuperer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Recuperer le profil lie e cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isManager = false;
		if ($total == 1)
			$isManager = true;
		
		// Est-ce que cet utilisateur est un editeur?
		$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isEditor = false;
		if ($total == 1)
			$isEditor = true;
			
		// Est-ce que la metadonnee est publiee?
		$isPublished = false;
		if ($rowMetadata->metadatastate_id == 1)
			$isPublished = true;			
			
		// Est-ce que la metadonnee est validee?
		$isValidated = false;
		if ($rowMetadata->metadatastate_id == 3)
			$isValidated = true;			
			
		// Recuperer les perimetres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Recuperer la metadonnee en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les perimetres predefinis 
		//$rowAttributeType = new attributetype($database);
		//$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		//$type_isocode = $rowAttributeType->isocode;
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$rowMetadata->guid;
		
		//.$id."
		$xmlBody= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n
			<csw:GetRecordById xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\"
			    outputSchema=\"csw:IsoRecord\">
			    <csw:Id>".$rowMetadata->guid."</csw:Id>
			</csw:GetRecordById>			
		";
		
		// Requete de type GET pour le login (conserver le token response)
		// Stocker dans un cookie le resultat de la requete precedente
		// Mettre le cookie dans l'en-tete de la requete insert
		//$xmlResponse = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlBody);

		// En POST
		//$cswResults = DOMDocument::loadXML($xmlResponse);

		// En GET
		//$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		$cswResults = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		
		// Construction du DOMXPath e utiliser pour generer la vue d'edition
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		if ($cswResults <> false and $cswResults->childNodes->item(0)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else if ($cswResults->childNodes->item(0)->nodeName == "ows:ExceptionReport")
		{
			$rowObject->checkin();
			$msg = $cswResults->childNodes->item(0)->nodeValue;
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ), $msg);
		}
		else
		{
			$rowObject->checkin();
			$msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ), $msg);
		}
		
		$xpathResults->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
        $xpathResults->registerNamespace('srv','http://www.isotc211.org/2005/srv');
        $xpathResults->registerNamespace('xlink','http://www.w3.org/1999/xlink');
        $xpathResults->registerNamespace('gts','http://www.isotc211.org/2005/gts');
        
        // Recuperation des namespaces e inclure
		$namespacelist = array();
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		 foreach ($namespacelist as $namespace)
        {
        	$xpathResults->registerNamespace($namespace->prefix,$namespace->uri);
        	// Les 3 suivantes dans la table SQL avec flag system
       		//$xpathResults->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
        	//$xpathResults->registerNamespace('gco','http://www.isotc211.org/2005/gco');
        	//$xpathResults->registerNamespace('gml','http://www.opengis.net/gml');
        	//$xpathResults->registerNamespace('bee','http://www.depth.ch/2008/bee');
        } 
        
        $query = "select value as config from #__sdi_configuration where code ='defaultBBoxConfig'";
        $database->setQuery($query);
        $defaultLayerConfig = trim($database->loadResult());

        $query = "select value as config from #__sdi_configuration where code ='defaultBBoxConfigExtentLeft'";
        $database->setQuery($query);
        $defaultExtentLeft = trim($database->loadResult());

        $query = "select value as config from #__sdi_configuration where code ='defaultBBoxConfigExtentTop'";
        $database->setQuery($query);
        $defaultExtentTop = trim($database->loadResult());

        $query = "select value as config from #__sdi_configuration where code ='defaultBBoxConfigExtentBottom'";
        $database->setQuery($query);
        $defaultExtentBottom = trim($database->loadResult());

        $query = "select value as config from #__sdi_configuration where code ='defaultBBoxConfigExtentRight'";
        $database->setQuery($query);
        $defaultExtentRight = trim($database->loadResult());

        if($defaultLayerConfig!="" &&  $defaultExtentLeft!="" && $defaultExtentBottom!="" &&  $defaultExtentTop!="" &&  $defaultExtentRight!="" ){
			$defaultBBoxConfig  = "defaultBBoxConfig ={
				getLayers : function(){
						return new Array(new OpenLayers.Layer.".$defaultLayerConfig.")
						},
				defaultExtent:{left:".$defaultExtentLeft.",bottom:".$defaultExtentBottom.",right:".$defaultExtentRight.",top:".$defaultExtentTop."}
			};";
        }        
        else{
        	$defaultBBoxConfig = "";
        }
        
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option, $defaultBBoxConfig,$rowObjectVersion);
	}
	
	function cancelMetadata($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowObject = new object( $database );
		$rowObject->load( $_GET['object_id'] );
		
		$rowObject->checkin();
		
		//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
		$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ));
	}
	
	function historyAssignMetadata($metadata_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if (JRequest::getVar('metadatata_id'))
			$metadata_id = JRequest::getVar('metadata_id');
			
		//$limit = JRequest::getVar('limit', 20, '', 'int');
		//$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$context	= $option.'.historyAssignMetadata';
		$limit		= $mainframe->getUserStateFromRequest($option.'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		// Probleme avec le retour au debut ou e la page une, quand limitstart n'est pas present dans la session.
		// La mise e zero ne se fait pas, il faut donc la forcer
		if (! isset($_REQUEST['limitstart']))
			$limitstart=0;
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$rowMetadata = new metadata($database);
		$rowMetadata->load($metadata_id);
		$rowObjectVersion = new objectversionByMetadata_id($database);
		$rowObjectVersion->load($rowMetadata->id);
		
		$query = "SELECT COUNT(*) FROM #__sdi_history_assign h
				  WHERE h.objectversion_id=".$rowObjectVersion->id;					
		$database->setQuery($query);
		$total = $database->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		$query = "SELECT h.assigned as date, aa.username as assignedby, bb.username as assignedto, o.name as object_name, ov.title as version_title, h.information as information 
                  FROM #__sdi_history_assign h
				  INNER JOIN #__sdi_objectversion ov ON h.objectversion_id=ov.id
				  INNER JOIN #__sdi_object o ON ov.object_id=o.id
				  INNER JOIN #__sdi_account a ON a.id=h.assignedby
				  INNER JOIN #__users aa ON a.user_id=aa.id
				  INNER JOIN #__sdi_account b ON b.id=h.account_id
				  INNER JOIN #__users bb ON b.user_id=bb.id
				  WHERE h.objectversion_id=".$rowObjectVersion->id." ORDER BY date DESC";
		
		$database->setQuery( $query, $pagination->limitstart, $pagination->limit);
		$rowHistory = $database->loadObjectList();
		//print_r($rowHistory);
		HTML_metadata::historyAssignMetadata($rowHistory, $pagination, $metadata_id, $option);
	}
	
	function archiveMetadata($cid ,$option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if ($cid == 0)
		{
			$msg = JText::_('CATALOG_OBJECT_ARCHIVEMETADATA_MSG');
			//$mainframe->redirect("index.php?option=$option&task=listMetadata", $msg);
			$mainframe->redirect(JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listMetadata'), false ), $msg);
			exit;
		}
		
		$metadata = new metadata($database);
		$metadata->load($cid[0]);
		$metadata->metadatastate_id=2;
		
		if (!$metadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	}
	
	function invalidateMetadata($metadata_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$account = new accountByUserId($database);
		$account->load($user->id);
		
		if (array_key_exists('metadata_id', $_POST))
		{
			$rowMetadata = new metadataByGuid($database);
			$rowMetadata->load($_POST['metadata_id']);
		}
		else
		{
			$rowMetadata = new metadata($database);
			$rowMetadata->load($metadata_id);
		}
			
		// Passer en statut en travail
		$rowMetadata->metadatastate_id=4;
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account->user_id;
		$rowMetadata->editor_id = null;
		$rowMetadata->published = null;
		$rowMetadata->notification = 0;
		
		if (!$rowMetadata->store(true)) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			exit();
		}
		
		if (array_key_exists('metadata_id', $_POST))
		{
			$response = '{
			 	   			success: true,
						    errors: {
						        xml: "Metadata invalidated"
						    }
						}';
			print_r($response);
			die();
		}
	}

	function selectAssignMetadata($option)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$user			=& JFactory::getUser();
		$user_id 		= $user->get('id');
		$object_id 		= JRequest::getVar('object_id');
		$metadata_id 	= JRequest::getVar('metadata_id');
	
		$database->setQuery( "	SELECT o.id as object_id, m.guid as metadata_id, o.name as object_name, ov.title as version_title
								FROM #__sdi_object o
								INNER JOIN  #__sdi_objectversion ov ON ov.object_id = o.id
								INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
								WHERE m.guid = '$metadata_id'
								AND o.id = $object_id 
								" );
		$sourceobject = $database->loadObject();
		
		$database->setQuery( "	SELECT child_id 
								FROM #__sdi_objectversionlink ovl
								INNER JOIN  #__sdi_objectversion ov ON ov.id = ovl.parent_id
								INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
								WHERE m.guid = '$metadata_id'
								" );
		$children = $database->loadResultArray();
		
		$editors = array();
		$listEditors = array();
		$database->setQuery( "	SELECT NULL AS value, '".JText::_("CATALOG_METADATA_ASSIGN_EDITOR_SELECTION_MESSAGE")."' AS text UNION
								SELECT DISTINCT c.id AS value, b.name AS text
								FROM #__users b, #__sdi_editor_object a
								LEFT OUTER JOIN #__sdi_account c ON a.account_id = c.id
								LEFT OUTER JOIN #__sdi_manager_object d ON d.account_id=c.id
								WHERE c.user_id=b.id AND (a.object_id=".$object_id." OR d.object_id=".$object_id.")
								AND c.user_id <> ".$user_id."
								ORDER BY text" );
		$editors = array_merge( $editors, $database->loadObjectList() );
	
		HTML_metadata::selectAssignMetadata($option,$sourceobject,$children,$editors);
	}
	
	function validateAssignMetadata($option)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$metadata_id 	= JRequest::getVar('metadata_id');
		$object_id 		= JRequest::getVar('object_id');
		$editor 		= JRequest::getVar('editor');
		$information 	= JRequest::getVar('information');
		$assignChildren	= JRequest::getVar('children');
				
		$result = SITE_metadata::assignMetadata($option, $metadata_id, $object_id, $editor, $information, $assignChildren);
		
		// Send an information email
		$rowUser = array();
		$database->setQuery( "SELECT * FROM #__sdi_account a INNER JOIN #__users u ON a.user_id=u.id WHERE a.id=".$editor );
		$rowUser	= array_merge( $rowUser, $database->loadObjectList() );
		$body 		= JText::sprintf(	"CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY",
										$user->username,
										$result[0]->rowObjectName, 
										$result[0]->rowObjectVersionTitle)."\n\n".JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY_INFORMATION").":\n".$information;
		unset($result[0]);
		if($assignChildren)
		{
			$body .= "\n\n".JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY_CHILDREN_LIST").":\n";
			foreach ($result as $r)
			{
				$body .= "\n - ".JText::sprintf("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY_CHILD",$r->rowObjectName,$r->rowObjectVersionTitle);
			}
		}
		if(! ADMIN_metadata::sendMailByEmail($rowUser[0]->email,JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_SUBJECT"),$body))
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_ASSIGN_METADATA_SEND_MAIL_ERROR"),"ERROR");
		}
		
		$mainframe->enqueueMessage(JText::_("CATALOG_ASSIGN_METADATA_DONE"));
		$mainframe->redirect("index.php?option=$option&task=listMetadata" );
	}
	
	function assignMetadata($option, $metadata_id, $object_id, $editor, $information, $assignChildren)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$result 		= array();
		
		//Assign the current metadata
		$object_parent 					= new stdClass();
		$object_parent->metadata_id 	= $metadata_id;
		$object_parent->object_id		= $object_id;
		
		$result[] = SITE_metadata::assignEachMetadata($option, $object_parent, $editor, $information);
		
		//Assign the current metadata children
		if($assignChildren)
		{
			$database->setQuery( "	SELECT ovl.child_id
					FROM #__sdi_objectversionlink ovl
					INNER JOIN  #__sdi_objectversion ov ON ov.id = ovl.parent_id
					INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
					WHERE m.guid = '$metadata_id'
					" );
			$children = $database->loadResultArray();
			
			$object_child_list = array();
			foreach ($children as $child)
			{
				$database->setQuery( "	SELECT m.guid as metadata_id, o.id as object_id
					FROM #__sdi_object o
					INNER JOIN #__sdi_objectversion ov ON ov.object_id = o.id
					INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
					WHERE ov.id = $child
					AND m.metadatastate_id NOT IN (1,3)
					" );
				$object_child = $database->loadObject();
				if($object_child)
					$object_child_list[] = $object_child;
			}
			foreach ($object_child_list as $object)
			{
				//$result[] =  SITE_metadata::assignEachMetadata($option, $object, $editor, $information);
				$result = array_merge($result , SITE_metadata::assignMetadata($option, $object->metadata_id, $object->object_id, $editor, $information, $assignChildren));
			}
		}
		return $result;
	}
	
	function assignEachMetadata($option,$object, $editor, $information)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$rowObject 		= new object($database);
		$rowObject->load($object->object_id);
		
		// Enregistrer l'éditeur auxquel la métadonnée est assignée
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($object->metadata_id);
		$rowMetadata->editor_id=$editor;
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		// Récupérer la version de l'objet liée
		$rowObjectVersion = new objectversionByMetadata_id($database);
		$rowObjectVersion->load($rowMetadata->id);
		
		// Remplir l'historique d'assignement
		$user 			= JFactory::getUser();
		$rowCurrentUser = new accountByUserId($database);
		$rowCurrentUser->load($user->get('id'));
		
		$rowHistory 					= new historyassign($database);
		$rowHistory->object_id			= $object->object_id;
		$rowHistory->objectversion_id	= $rowObjectVersion->id;
		$rowHistory->account_id			= $editor;
		$rowHistory->assigned			= date ("Y-m-d H:i:s");
		$rowHistory->assignedby			= $rowCurrentUser->id;
		$rowHistory->information		= $information;
		
		// Générer un guid
		$rowHistory->guid = helper_easysdi::getUniqueId();
		
		if (!$rowHistory->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		$result = new stdClass();
		$result->rowObjectName = $rowObject->name;
		$result->rowObjectVersionTitle = $rowObjectVersion->title;

		return $result;
	}
	
	function notifyMetadata($option)
	{
		global  $mainframe;
		$database 				=& JFactory::getDBO();
		$user					=& JFactory::getUser();
		$rowCurrentUser 		= new accountByUserId($database);
		$rowCurrentUser->load($user->get('id'));
		$objectversion_id 		= JRequest::getVar('objectversion_id');
		$includechildren 		= JRequest::getVar('includedesc', '0', 'get', 'int');
		
		//Get the current, parent, metadata informations
		$database->setQuery( "	SELECT o.id as object_id, o.name as object_name, ov.title as version_title, ov.metadata_id as metadata_id
									FROM #__sdi_object o
									INNER JOIN  #__sdi_objectversion ov ON ov.object_id = o.id
									WHERE ov.id = $objectversion_id
									" );
		$sourceobject = $database->loadObject();

		if($includechildren == 1)
		{
			//Get the children metadata informations
			$database->setQuery( "	SELECT o.name as object_name, ov.title as version_title
										FROM #__sdi_object o
										INNER JOIN  #__sdi_objectversion ov ON ov.object_id = o.id
										INNER JOIN #__sdi_objectversionlink ovl ON ovl.child_id = ov.id
										WHERE ovl.parent_id = $objectversion_id
										" );
			$children = $database->loadObjectList();
		}
		//Get the manager list of the current objet
		$database->setQuery( "	SELECT a.id as id, u.email as email
									FROM #__sdi_account a 
									INNER JOIN #__users u ON a.user_id=u.id
									INNER JOIN #__sdi_manager_object mo ON mo.account_id = a.id
									WHERE mo.object_id = $sourceobject->object_id
							 " );
		$manager = $database->loadObjectList();
		
		// Send an information email
		$body 		= JText::sprintf(	"CATALOG_NOTIFY_METADATA_MAIL_BODY",
										$user->username,
										$sourceobject->object_name, 
										$sourceobject->version_title);
		
		if($includechildren == 1 && count($children) > 0)
		{
			$body .= "\n\n".JText::_("CATALOG_NOTIFY_METADATA_MAIL_BODY_CHILDREN_LIST").":\n";
			foreach ($children as $r)
			{
				$body .= "\n - ".JText::sprintf("CATALOG_NOTIFY_METADATA_MAIL_BODY_CHILD",$r->object_name,$r->version_title);
			}
		}
		
		//Load current metadata object
		$rowMetadata = new metadata($database);
		$rowMetadata->load($sourceobject->metadata_id);
		
		//Loop on metadata managers
		foreach ($manager as $m)
		{
			if(! ADMIN_metadata::sendMailByEmail($m->email,JText::_("CATALOG_NOTIFY_METADATA_MAIL_SUBJECT"),$body))
			{
				$mainframe->enqueueMessage(JText::sprintf("CATALOG_NOTIFY_METADATA_SEND_MAIL_ERROR", $m->email),"ERROR");
			}
			else
			{
				//Update the notification state of the metadata because at least one notification has been correctly sent
				$rowMetadata->notification=1;
				//Store the notification action in the history
				$rowHistory 					= new historyassign($database);
				$rowHistory->object_id			= $sourceobject->object_id;
				$rowHistory->objectversion_id	= $objectversion_id;
				$rowHistory->account_id			= $m->id;
				$rowHistory->assigned			= date ("Y-m-d H:i:s");
				$rowHistory->assignedby			= $rowCurrentUser->id;
				if($includechildren == 1)
					$rowHistory->information	= JText::_("CATALOG_NOTIFY_METADATA_HISTORY_NOTIFICATION_WITH_CHILDREN");
				else
					$rowHistory->information	= JText::_("CATALOG_NOTIFY_METADATA_HISTORY_NOTIFICATION");
				$rowHistory->guid 				= helper_easysdi::getUniqueId();
				
				if (!$rowHistory->store()) {
					$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_UPDATE_NOTIFICATION_STATE_ERROR").$database->getErrorMsg(),"ERROR");
				}
			}
		}
		
		if($rowMetadata->notification == 1)
			$mainframe->enqueueMessage(JText::_("CATALOG_NOTIFY_METADATA_SEND_MAIL_DONE"));
		else
			$mainframe->enqueueMessage(JText::_("CATALOG_NOTIFY_METADATA_SEND_MAIL_FAIL"));
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_UPDATE_NOTIFICATION_STATE_ERROR").$database->getErrorMsg(),"ERROR");
		}
		
		$mainframe->redirect("index.php?option=$option&task=listMetadata" );
	}
	
	function metadataPublished ($option)
	{
		global  $mainframe;
		$database 				=& JFactory::getDBO();
		$user					=& JFactory::getUser();
		$guid	 				= JRequest::getVar('guid');
	
		$metadata = new metadataByGuid($database);
		$metadata->load($guid);
		
		$objectversion = new objectversionByMetadata_id($database);
		$objectversion->load($metadata->id);
		
		$object = new object($database);
		$object->load($objectversion->object_id);
		
		$objectversion = new objectversionByMetadata_id($database);
		$objectversion->load($metadata->id);
		
		HTML_metadata::metadataPublished($option,$metadata,$object,$objectversion);
	}
	
	function setMetadataPublished ($option)
	{
		global  $mainframe;
		$database 				=& JFactory::getDBO();
		$user					=& JFactory::getUser();
		$guid	 				= JRequest::getVar('guid');
		$published	 			= JRequest::getVar('published');
		
		$metadata = new metadataByGuid($database);
		$metadata->load($guid);
		$metadata->published = $published;
		
		if (!$metadata->store()) {
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_UPDATE_NOTIFICATION_STATE_ERROR").$database->getErrorMsg(),"ERROR");
		}
		
		$mainframe->redirect("index.php?option=$option&task=listMetadata" );
	}
}
?>