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

class SITE_metadata {
	var $langList = array ();
	
	function listMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		//Check user's rights
		$allow = false;
		$allow = userManager::isUserAllowed($user,"PRODUCT");
		if (!$allow)
		{
			$mainframe->_messageQueue=array(); // Seul le message lié au droit d'édition sera conservé, s'il y a lieu
			$allow = userManager::isUserAllowed($user,"METADATA");	
		}
		
		if(!$allow)
		{
			return;
		}
		
		$option=JRequest::getVar("option");
		$limit = JRequest::getVar('limit', 20, '', 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (o.name LIKE '%$search%')";			
		}
		$account = new accountByUserId($database);
		$account->load($user->id);

		$rootAccount = new account($database);
		$rootAccount->load($account->root_id);		
		
		// Si le compte n'a pas de root, c'est qu'il l'est lui-même
		if (!$rootAccount->id)
			$rootAccount = $account;
			
		//List only the objects for which metadata manager is the current user
		/*$queryCount = "	SELECT DISTINCT o.*, ov.name as version_name, s.label as state 
						FROM 	#__sdi_editor_object e, 
								#__sdi_metadata m, 
								#__sdi_list_metadatastate s, 
								#__sdi_account a, 
								#__users u,
								#__sdi_objecttype ot,
								#__sdi_object o 
						LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
						LEFT OUTER JOIN #__sdi_objectversion ov ON ov.object_id=o.id
						WHERE e.object_id=o.id
							AND ov.metadata_id=m.id 
							AND m.metadatastate_id=s.id 
							AND e.account_id=a.id 
							AND a.user_id = u.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND (e.account_id = ".$account->id."
								OR (ma.account_id=".$account->id."
									AND s.id=1)
								)";*/
		$queryCount = "	SELECT DISTINCT o.*, s.label as state, m.guid as metadata_guid 
						FROM 	#__sdi_editor_object e, 
								#__sdi_metadata m, 
								#__sdi_list_metadatastate s, 
								#__sdi_account a, 
								#__users u,
								#__sdi_objecttype ot,
								#__sdi_object o 
						LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
						WHERE e.object_id=o.id
							AND o.metadata_id=m.id
							AND m.metadatastate_id=s.id 
							AND e.account_id=a.id 
							AND a.user_id = u.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND (e.account_id = ".$account->id."
								OR (ma.account_id=".$account->id."
									AND s.id=1)
								)";
		$queryCount .= $filter;
		
		$database->setQuery($queryCount);
		$total = count($database->loadObjectList());
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$pageNav = new JPagination($total,$limitstart,$limit);
		/*$query = "	SELECT DISTINCT o.*, ov.name as version_name, ov.created as version_created, CONCAT(o.name,' ',ov.name) as full_name, s.label as state, m.guid as metadata_guid 
						FROM 	#__sdi_editor_object e, 
								#__sdi_metadata m, 
								#__sdi_list_metadatastate s, 
								#__sdi_account a, 
								#__users u,
								#__sdi_objecttype ot,
								#__sdi_object o 
						LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
						LEFT OUTER JOIN #__sdi_objectversion ov ON ov.object_id=o.id
						WHERE e.object_id=o.id
							AND ov.metadata_id=m.id 
							AND m.metadatastate_id=s.id 
							AND e.account_id=a.id 
							AND a.user_id = u.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND (e.account_id = ".$account->id."
								OR (ma.account_id=".$account->id."
									AND s.id=1)
								)";*/
		$query = "	SELECT DISTINCT o.*, s.label as state, m.guid as metadata_guid 
						FROM 	#__sdi_editor_object e, 
								#__sdi_metadata m, 
								#__sdi_list_metadatastate s, 
								#__sdi_account a, 
								#__users u,
								#__sdi_objecttype ot,
								#__sdi_object o 
						LEFT OUTER JOIN #__sdi_manager_object ma ON ma.object_id=o.id
						WHERE e.object_id=o.id
							AND o.metadata_id=m.id
							AND m.metadatastate_id=s.id 
							AND e.account_id=a.id 
							AND a.user_id = u.id
							AND ot.id=o.objecttype_id
							AND ot.predefined=0
							AND (e.account_id = ".$account->id."
								OR (ma.account_id=".$account->id."
									AND s.id=1)
								)";
		$query .= $filter;
		//$query .= " ORDER BY o.name, ov.name ASC";
		$query .= " ORDER BY o.name ASC";
		
		$database->setQuery($query,$limitstart,$limit);
		//echo $database->getQuery();		
		$rows = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$database->getErrorMsg();
			echo "</div>";
		}	
		
		$managers = "";
		$database->setQuery( "SELECT a.object_id FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND c.user_id=".$user->id." ORDER BY a.object_id" );
		$managers = implode(", ", $database->loadResultArray());
		
		$editors = "";
		$database->setQuery( "SELECT a.object_id FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND c.user_id=".$user->id." ORDER BY a.object_id" );
		$editors = implode(", ", $database->loadResultArray());
		
		HTML_metadata::listMetadata($pageNav,$rows,$option,$rootAccount, $search);	
		
	}
	
	function editMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		if ($id == 0)
		{
			$msg = JText::_('CATALOG_OBJECT_SELECTMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=listMetadata", $msg);
			exit;
		}
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $id );
		
		// Récupérer la métadonnée choisie par l'utilisateur
		$rowMetadata = new metadata( $database );
		$rowMetadata->load( $rowObject->metadata_id );
		
		if ($rowMetadata->id == 0)
		{
			$msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=listMetadata", $msg );
		}
		
		/*
		// Récupérer la métadonnée choisie par l'utilisateur
		if (array_key_exists('version_hidden', $_POST))
		{
			$rowVersion = new objectversion($database);
			$rowVersion->load( $_POST['version_hidden'] );
			$rowMetadata = new metadata( $database );
			$rowMetadata->load( $rowVersion->metadata_id );
		}
		else if (array_key_exists('metadata_id', $_POST))
		{
			$rowMetadata = new metadataByGuid( $database );
			$rowMetadata->load( $_POST['metadata_id'] );
		}
		else
		{
			// Récupérer la seule et unique version de l'objet
			$lastVersion = array();
			$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$id." ORDER BY created DESC" );
			$lastVersion = array_merge( $lastVersion, $database->loadObjectList() );
			
			// Récupérer la métadonnée de la dernière version de l'objet
			$rowMetadata = new metadata( $database );
			if (count($lastVersion) > 0)
				$rowMetadata->load($lastVersion[0]->metadata_id);
			else
			$rowMetadata->load( $rowObject->metadata_id );
		}
		*/
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowObject->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowObject->name);
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
		}

		$rowObject->checkout($user->get('id'));
		
		
		// Stocker en mémoire toutes les traductions de label, valeur par défaut et information pour la langue courante
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
		
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isManager = false;
		if ($total == 1)
			$isManager = true;
			
		// Est-ce que la métadonnée est publiée?
		$isPublished = false;
		if ($rowMetadata->metadatastate_id == 1)
			$isPublished = true;			
			
		// Est-ce que la métadonnée est validée?
		$isValidated = false;
		if ($rowMetadata->metadatastate_id == 3)
			$isValidated = true;			
			
		// Récupérer les périmètres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmètres prédéfinis 
		//$rowAttributeType = new attributetype($database);
		//$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		//$type_isocode = $rowAttributeType->isocode;
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_list_attributetype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		//$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=158_bis"; //.$id;
		//$catalogUrlGetRecordById = "http://demo.easysdi.org:8080/proxy/ogc/geonetwork?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$rowObject->metadata_id; //.$id;
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$rowMetadata->guid;
		
		//.$id."
		$xmlBody= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n
			<csw:GetRecordById xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\"
			    outputSchema=\"csw:IsoRecord\">
			    <csw:Id>".$rowMetadata->guid."</csw:Id>
			</csw:GetRecordById>			
		";
		
		//echo "<hr>".htmlspecialchars($xmlBody)."<hr>";
		
		// Requête de type GET pour le login (conserver le token response)
		// Stocker dans un cookie le résultat de la requête précédente
		// Mettre le cookie dans l'en-tête de la requête insert
		//$xmlResponse = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlBody);

		// En POST
		//$cswResults = DOMDocument::loadXML($xmlResponse);

		// En GET
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		/*
		$cswResults = new DOMDocument();
		echo var_dump($cswResults->load($catalogUrlGetRecordById))."<br>";
		echo var_dump($cswResults->saveXML())."<br>";
		echo var_dump($cswResults)."<br>";
		*/
		
		// Construction du DOMXPath à utiliser pour générer la vue d'édition
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		if ($cswResults <> false)
			$xpathResults = new DOMXPath($cswResults);
		else
			$xpathResults = new DOMXPath($doc);
		$xpathResults->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
        $xpathResults->registerNamespace('srv','http://www.isotc211.org/2005/srv');
        $xpathResults->registerNamespace('xlink','http://www.w3.org/1999/xlink');
        $xpathResults->registerNamespace('gts','http://www.isotc211.org/2005/gts');
        
        // Récupération des namespaces à inclure
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
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
        
        HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $option);
		//HTML_metadata::editMetadata($root, $id, $xpathResults, $option);
		//HTML_metadata::editMetadata($rowMetadata, $metadatastates, $option);
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
		
		$mainframe->redirect("index.php?option=$option&task=listMetadata" );
	}
	
	function historyAssignMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		if (JRequest::getVar('object_id'))
			$id = JRequest::getVar('object_id');
			
		//$limit = JRequest::getVar('limit', 20 );
		//$limitstart = JRequest::getVar('limitstart', 0 );
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$rowObject = new object($database);
		$rowObject->load($id);
		
		$query = "SELECT COUNT(*) FROM #__sdi_history_assign h
				  WHERE h.object_id=".$rowObject->id;					
		$database->setQuery($query);
		$total = $database->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		$query = "SELECT h.assigned as date, aa.username as assignedby, bb.username as assignedto, o.name as object_name, h.information as information 
                  FROM #__sdi_history_assign h
					INNER JOIN #__sdi_object o ON h.object_id=o.id
					INNER JOIN #__sdi_account a ON a.id=h.assignedby
					INNER JOIN #__users aa ON a.user_id=aa.id
					INNER JOIN #__sdi_account b ON b.id=h.account_id
					INNER JOIN #__users bb ON b.user_id=bb.id
				  WHERE h.object_id=".$rowObject->id." ORDER BY date DESC";
		
		$database->setQuery( $query, $pagination->limitstart, $pagination->limit);
		$rowHistory = $database->loadObjectList();
		//print_r($rowHistory);
		HTML_metadata::historyAssignMetadata($rowHistory, $pagination, $id, $option);
	}
}
?>