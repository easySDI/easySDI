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
		
		//breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_METADATA");
		
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
			
		//List only the products for which metadata manager is the current user
		$queryCount = "	SELECT DISTINCT o.*, ov.name as version_name, s.label as state 
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
		//$query = "select * from #__easysdi_product where (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id)) ";
		//List only the products for which metadata manager is the current user
		//$query = " SELECT * FROM #__sdi_object where account_id = $rootAccount->id " ;
		/*$query = "	SELECT o.*, s.label as state 
					FROM #__sdi_account a, #__users b, #__sdi_object o 
					LEFT OUTER JOIN #__sdi_metadata m ON o.metadata_id=m.id 
					LEFT OUTER JOIN #__sdi_list_metadatastate s ON m.metadatastate_id=s.id 
					WHERE a.user_id = b.id 
						AND a.id=o.account_id 
						AND o.account_id = ".$rootAccount->id;
		*/
		$query = "	SELECT DISTINCT o.*, ov.name as version_name, ov.created as version_created, CONCAT(o.name,' ',ov.name) as full_name, s.label as state, m.guid as metadata_guid 
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
								)";
		$query .= $filter;
		$query .= " ORDER BY o.name, ov.name ASC";
		
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
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $id );
		
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
		$query = "SELECT c.name as name, CONCAT(ns.prefix, ':', c.name) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		if ($total == 1)
			$isManager = true;
		else
			$isManager = false;
		
			
		// Est-ce que la métadonnée est publiée?
		if ($rowMetadata->metadatastate_id == 1)
			$isPublished = true;
		else
			$isPublished = false;
			
		// Est-ce que la métadonnée est validée?
		if ($rowMetadata->metadatastate_id == 3)
			$isValidated = true;
		else
			$isValidated = false;
			
		// Récupérer les périmètres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		//$metadata_id = "0f62e111-831d-4547-aee7-03ad10a3a141";
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmètres prédéfinis 
		$rowAttributeType = new attributetype($database);
		$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		$type_isocode = $rowAttributeType->isocode;
		
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

	function buildXMLTree($parent, $parentFieldset, $parentName, $XMLDoc, $xmlParent, $queryPath, $currentIsocode, $scope, $keyVals, $profile_id, $account_id, $option)
	{
		//echo "Name: ".$parentName." \r\n ";
		//echo "Isocode courant: ".$currentIsocode."\\r\\n";
		$database =& JFactory::getDBO();
		$rowChilds = array();
		$xmlClassParent = $xmlParent;
		$xmlAttributeParent = $xmlParent;
		
		$rowChilds = array();
		$query = "SELECT rel.id as rel_id, 
						 rel.guid as rel_guid,
						 rel.name as rel_name, 
						 rel.upperbound as rel_upperbound, 
						 rel.lowerbound as rel_lowerbound, 
						 rel.attributechild_id as attribute_id, 
						 rel.rendertype_id as rendertype_id, 
						 rel.classchild_id as child_id, 
						 CONCAT(relation_namespace.prefix,':',rel.isocode) as rel_isocode, 
						 rel.relationtype_id as reltype_id, 
						 rel.classassociation_id as association_id,
						 a.guid as attribute_guid,
						 a.name as attribute_name, 
						 CONCAT(attribute_namespace.prefix,':',a.name) as attribute_isocode, 
						 CONCAT(list_namespace.prefix,':',a.type_isocode) as list_isocode, 
						 a.attributetype_id as attribute_type, 
						 a.default as attribute_default, 
						 a.isSystem as attribute_system, 
						 a.length as length,
						 a.codeList as codeList,
						 a.information as tip,
						 t.isocode as t_isocode, 
						 accountrel_attribute.account_id as attributeaccount_id,
						 c.name as child_name,
						 c.guid as class_guid, 
						 CONCAT(child_namespace.prefix,':',c.name) as child_isocode, 
						 accountrel_class.account_id as classaccount_id
				  FROM	 #__sdi_relation as rel 
						 JOIN #__sdi_relation_profile as prof
						 	 ON rel.id = prof.relation_id
						 LEFT OUTER JOIN #__sdi_attribute as a
				  		 	 ON rel.attributechild_id=a.id 
						     LEFT OUTER JOIN #__sdi_list_attributetype as t
						  		 ON a.attributetype_id = t.id 
					     LEFT OUTER JOIN #__sdi_class as c
					  		 ON rel.classchild_id=c.id
					     LEFT OUTER JOIN #__sdi_list_relationtype as reltype
					  		 ON rel.relationtype_id=reltype.id	 
					     LEFT OUTER JOIN #__sdi_account_attribute as accountrel_attribute
					  		 ON accountrel_attribute.attribute_id=attribute_id
					     LEFT OUTER JOIN #__sdi_account_class as accountrel_class
					  		 ON accountrel_class.class_id=class_id
					  	 LEFT OUTER JOIN #__sdi_namespace as attribute_namespace
					  		 ON attribute_namespace.id=a.namespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as list_namespace
					  		 ON list_namespace.id=a.listnamespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as child_namespace
					  		 ON child_namespace.id=c.namespace_id
					     LEFT OUTER JOIN #__sdi_namespace as relation_namespace
					  		 ON relation_namespace.id=rel.namespace_id
				  WHERE  rel.parent_id=".$parent."
				  		 AND 
				  		 prof.profile_id=".$profile_id."
				  		 AND 
				  		 rel.published = 1
				  		 AND
				  		 (
				  		 	(accountrel_attribute.account_id is null or accountrel_attribute.account_id=".$account_id.")
				  		 	OR
				  		 	(accountrel_class.account_id is null or accountrel_class.account_id=".$account_id.")
				  		 )
				  ORDER BY rel.ordering, rel.id";		
		$database->setQuery( $query );
		$rowChilds = array_merge( $rowChilds, $database->loadObjectList() );
		
		//foreach($rowAttributeChilds as $child)
		foreach($rowChilds as $child)
		{
			// Traitement d'une relation vers un attribut
			if ($child->attribute_id <> null)
			{
				//echo "attribute: ".$child->attribute_isocode."\r\n";
				//echo "attributetype_id: ".$child->attribute_type."\r\n\r\n";
				
				if ($child->attribute_type == 6 )
					$type_isocode = $child->list_isocode;
				else
					$type_isocode = $child->t_isocode;
		
				//echo "type_isocode: ".$type_isocode."\r\n";
				//echo "parent name: ".$parentName."\r\n";
				$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
	
				//$name = $name."__1";
					
				//echo "attribute name: ".$name."\r\n";
				$childType = $child->t_isocode;
				
				// Traitement de chaque attribut selon son type
				switch($child->attribute_type)
				{
					// Guid
					case 1:
						//echo "attribute: ".$child->attribute_isocode."\r\n";
						$name = $name."__1";
						if ($child->attribute_system)
							$name = $name."_hiddenVal";
						
						//echo "name: ".$name."\r\n";
						
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						//print_r($usefullVals);
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// Text
					case 2:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						//print_r($keys);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						if ($child->attribute_system)
							$name = $name."_hiddenVal";
							
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							//echo "partToCompare: ".$partToCompare."\r\n";
							//echo "name: ".$name."\r\n";
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						//echo $name." ".$count."\r\n";
						//print_r($usefullVals);
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// Local
					case 3:
						/* Traitement spécifique aux langues */
						// On crée le nom spécifiquement pour les textes localisés
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode); //."-".str_replace(":", "_", $type_isocode);
	
						$count=0;
				
						foreach($keyVals as $key => $val)
						{
							//echo "key: ".$key."\r\n";
							//echo "equals: ".$parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1"."\r\n";
							if ($key == $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1")
							{
								$count = $val;
								break;
							}
						}
						$count = $count - 1;
						
						//echo "count: ".$count."\r\n";
						
						for ($pos=0; $pos<$count; $pos++)
						{
							$LocName = $name."__".($pos+2);
							//echo "LocName: ".$LocName."\r\n";
						
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							$xmlLocParent = $XMLNode;
							
							foreach($this->langList as $lang)
							{
								//print_r($lang); echo "\r\n";
								$LangName = $LocName."-gmd_LocalisedCharacterString-".$lang->code."__1";
								//echo "LangName: ".$LangName."\r\n";  
	
								 // Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								$usefullVals=array();
								//$usefullKeys=array();
								$langCount=0;
								
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($LangName));
									//echo "partToCompare: ".$partToCompare."\r\n";
									//echo "key: ".$key."\r\n";
									if ($partToCompare == $LangName)
									{
										if (substr($key, -6) <> "_index")
										{
											$langCount = $langCount+1;
											//$usefullKeys[] = $key;
											$usefullVals[$lang->code] = $_POST[$key];
										}
									}
								}
								//$count = $count/count($this->langList);
								
								//echo "count langue: ".$langCount."\r\n";
								
								for ($langPos=1; $langPos<=$langCount; $langPos++)
								{
									$nodeValue=$usefullVals[$lang->code];
									
									if (mb_detect_encoding($nodeValue) <> "UTF-8")
										$text = utf8_encode($nodeValue);
										
									$nodeValue = stripslashes($nodeValue);
									$nodeValue = preg_replace("/\r\n|\r|\n/","&#xD;",$nodeValue);
									
									$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
									$xmlLocParent->appendChild($XMLNode);
									$XMLNode->setAttribute('locale', $lang->code);
									$xmlParent = $XMLNode;
								}
							}
								
							/*
							// Ajouter chacune des copies du champ dans le XML résultat
							$langIndex = 0;
							for ($pos=1; $pos<=$count; $pos++)
							{
								$searchName = $parentName."-".str_replace(":", "_", $attribute->attribute_isocode)."__1";
								echo "searchName: ".$searchName."\\r\\n";
								
								$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
								$xmlAttributeParent->appendChild($XMLNode);
								$xmlLocParent = $XMLNode;
								
								foreach($this->langList as $lang)
								{
									$LangName = $LocName."-gmd_LocalisedCharacterString-".$row->language."__1";
									 echo "LangName: ".$LangName."\\r\\n";  
									 
									$nodeValue=$usefullVals[$langIndex][$lang->lang];
								
									$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
									$xmlLocParent->appendChild($XMLNode);
									$XMLNode->setAttribute('locale', $lang->lang);
									$xmlParent = $XMLNode;
									
									$langIndex = $langIndex+1;
								}
							}*/
						}
					
						
						
						break;
					// Number
					case 4:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->attribute_system)
								$name = $name."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// Date
					case 5:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->attribute_system)
								$name = $name."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						
						// DATETIME
						/*if ($nodeValue <> "")
							$nodeValue = date('Y-m-d', strtotime($nodeValue));
						else
							$nodeValue = date('Y-m-d');*/
						// $nodeValue = date('Y-m-d')."T".date('H:m:s');
						break;
					// List
					case 6:
						switch ($child->rendertype_id)
						{
							// Checkbox
							case 2:
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								//print_r($keys);echo "\r\n";
								$usefullVals=array();
								//$usefullKeys=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										//echo "partToCompare: ".$partToCompare."\r\n";
										//echo "second partToCompare: ".$parentName."-".str_replace(":", "_", $child->attribute_isocode)."\r\n";
										//echo "key: ".$key."\r\n";
										//echo "value: ".$_POST[$key]."\r\n";
										
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											//$usefullKeys[] = $key;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								//print_r($usefullVals);
								//echo "\r\n";
								//$nodeValues=split(",",$usefullVals);
								$nodeValues=$usefullVals;
								//print_r($nodeValues);
								//echo "\r\n";
								
								// Deux traitement pour deux types de listes
								//$child->rel_lowerbound < $child->rel_upperbound
							 	if ($child->codeList <> "")
							 	{
								 	foreach($nodeValues as $val)
									{
										if ($val)
										{
											$val = stripslashes($val);
											
											if ($child->rel_isocode <> "")
											{
												$XMLNode = $XMLDoc->createElement($child->rel_isocode);
												$xmlAttributeParent->appendChild($XMLNode);
											}
											else
											{
												$XMLNode = $xmlAttributeParent;
											}
											
											$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
											$XMLNode->appendChild($XMLRelNode);
											$XMLNode = $XMLRelNode;
											
											$XMLListNode = $XMLDoc->createElement($type_isocode);
											$XMLNode->appendChild($XMLListNode);
											$XMLListNode->setAttribute('codeListValue', $val);
											$XMLListNode->setAttribute('codeList', $child->codeList);
											$xmlParent = $XMLListNode;
										}
									}
							 	}
							 	else
							 	{
								 	foreach($nodeValues as $val)
									{
										if ($val)
										{
											$val = stripslashes($val);
											
											if ($child->rel_isocode <> "")
											{
												$XMLNode = $XMLDoc->createElement($child->rel_isocode);
												$xmlAttributeParent->appendChild($XMLNode);
											}
											else
											{
												$XMLNode = $xmlAttributeParent;
											}
											
											$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
											$XMLNode->appendChild($XMLRelNode);
											$XMLNode = $XMLRelNode;
											
											$XMLListNode = $XMLDoc->createElement($type_isocode, $val);
											$XMLNode->appendChild($XMLListNode);
											$xmlParent = $XMLListNode;
										}
									}
							 	}
							 	
								break;
							// Radiobutton
							case 3:
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								//print_r($keys);echo "\r\n";
								//print_r(array_values($_POST));
								//echo "\r\n";
								$usefullVals=array();
								//$usefullKeys=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										//echo "partToCompare: ".$partToCompare."\r\n";
										//echo "second partToCompare: ".$parentName."-".str_replace(":", "_", $child->attribute_isocode)."\r\n";
										//echo "key: ".$key."\r\n";
										//echo "value: ".$_POST[$key]."\r\n";
										
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											//$usefullKeys[] = $key;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								
								$nodeValue = $usefullVals[0];
								$nodeValue = stripslashes($nodeValue);
									
								//echo $nodeValue."\r\n";
								
								if ($nodeValue <> "")
								{
									// Deux traitement pour deux types de listes
									if ($child->codeList <> "")
								 	{
								 		if ($child->rel_isocode <> "")
										{
											$XMLNode = $XMLDoc->createElement($child->rel_isocode);
											$xmlAttributeParent->appendChild($XMLNode);
										}
										else
										{
											$XMLNode = $xmlAttributeParent;
										}
										
										$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
										$XMLNode->appendChild($XMLRelNode);
										$XMLNode = $XMLRelNode;
										
										$XMLListNode = $XMLDoc->createElement($type_isocode);
										$XMLNode->appendChild($XMLListNode);
										$XMLListNode->setAttribute('codeListValue', $nodeValue);
										$XMLListNode->setAttribute('codeList', $child->codeList);
										$xmlParent = $XMLListNode;
								 	}
								 	else
								 	{
								 		if ($child->rel_isocode <> "")
										{
											$XMLNode = $XMLDoc->createElement($child->rel_isocode);
											$xmlAttributeParent->appendChild($XMLNode);
										}
										else
										{
											$XMLNode = $xmlAttributeParent;
										}
										
										$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
										$XMLNode->appendChild($XMLRelNode);
										$XMLNode = $XMLRelNode;
										
										$XMLListNode = $XMLDoc->createElement($type_isocode, $nodeValue);
										$XMLNode->appendChild($XMLListNode);
										$xmlParent = $XMLListNode;
								 	}
								}
								break;
							// List
							case 4:
							default:
								/* Traitement spécifique aux listes */
						
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								$usefullVals=array();
								//$usefullKeys=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										//echo "partToCompare: ".$partToCompare."\r\n";
										//echo "second partToCompare: ".$parentName."-".str_replace(":", "_", $child->attribute_isocode)."\r\n";
										//echo "key: ".$key."\r\n";
										//echo "value: ".$_POST[$key]."\r\n";
										
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											//$usefullKeys[] = $key;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								// Traitement des enfants de type list
								// Traitement de la multiplicité
							 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
							 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								//$nodeValue = $usefullVals[0];
									
								$nodeValues=split(",",$usefullVals[0]);
								
								// Deux traitement pour deux types de listes
								//$child->rel_lowerbound < $child->rel_upperbound
							 	if ($child->codeList <> "")
							 	{
								 	foreach($nodeValues as $val)
									{
										if ($val <> "")
										{
											if ($child->rel_isocode <> "")
											{
												$XMLNode = $XMLDoc->createElement($child->rel_isocode);
												$xmlAttributeParent->appendChild($XMLNode);
											}
											else
											{
												$XMLNode = $xmlAttributeParent;
											}
											
											$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
											$XMLNode->appendChild($XMLRelNode);
											$XMLNode = $XMLRelNode;
											
											$XMLListNode = $XMLDoc->createElement($type_isocode);
											$XMLNode->appendChild($XMLListNode);
											$XMLListNode->setAttribute('codeListValue', $val);
											$XMLListNode->setAttribute('codeList', $child->codeList);
											$xmlParent = $XMLListNode;
										}
									}
							 	}
							 	else
							 	{
								 	foreach($nodeValues as $val)
									{
										if ($val <> "")
										{
											if ($child->rel_isocode <> "")
											{
												$XMLNode = $XMLDoc->createElement($child->rel_isocode);
												$xmlAttributeParent->appendChild($XMLNode);
											}
											else
											{
												$XMLNode = $xmlAttributeParent;
											}
											
											$XMLRelNode = $XMLDoc->createElement($child->attribute_isocode);
											$XMLNode->appendChild($XMLRelNode);
											$XMLNode = $XMLRelNode;
											
											$XMLListNode = $XMLDoc->createElement($type_isocode, $val);
											$XMLNode->appendChild($XMLListNode);
											$xmlParent = $XMLListNode;
										}
									}
							 	}
								break;
						}
						
						 
						break;
					// Link
					case 7:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->attribute_system)
								$name = $name."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					default:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->attribute_system)
								$name = $name."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									//$usefullKeys[] = $key;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
				}
			}
		
			// Récupération des relations de cette classe vers d'autres classes
			else 
			{
				//echo "child: ".$child->child_isocode."\\r\\n";
				//echo "relation: ".$child->rel_isocode."\\r\\n";
				$count=0;
				
				foreach($keyVals as $key => $val)
				{
					//echo "key: ".$key."\\r\\n";
					//echo "equals: ".$parentName."-".str_replace(":", "_", $child->child_isocode)."__1"."\\r\\n";
					if ($key == $parentName."-".str_replace(":", "_", $child->child_isocode)."__1")
					{
						$count = $val;
						break;
					}
				}
				$count = $count - 1;
				
				//echo "count: ".$count."\\r\\n";
				
				for ($pos=0; $pos<$count; $pos++)
				{
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+2);
					//echo "name: ".$name."\\r\\n";
				
					// Structure à créer ou pas
					$keys = array_keys($_POST);
					$existVal=false;
					foreach($keys as $key)
					{
						$partToCompare = substr($key, 0, strlen($name));
						if ($partToCompare == $name)
						{
							$existVal = true;
							break;
						}
					}
	
					if ($existVal)
					{
						// La relation
						if ($child->rel_isocode <> "")
						{
							$XMLNode = $XMLDoc->createElement($child->rel_isocode);
							$xmlClassParent->appendChild($XMLNode);
							// On conserve dans une variable intermédiaire la classe parent
							$xmlOldClassParent = $xmlClassParent;
							$xmlClassParent = $XMLNode;
						}
						
						// La classe enfant
						$XMLNode = $XMLDoc->createElement($child->child_isocode);
						$xmlClassParent->appendChild($XMLNode);
						$xmlParent = $XMLNode;
						// On récupère la vraie classe parent, au cas où elle aurait été changée					
						$xmlClassParent = $xmlOldClassParent;		
						// Récupération des codes ISO et appel récursif de la fonction
						$nextIsocode = $child->child_isocode;
							
						ADMIN_metadata::buildXMLTree($child->child_id, $child->child_id, $name, &$XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
					}
					
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						ADMIN_metadata::buildXMLTree($child->association_id, $child->child_id, $name, &$XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
					}
				}
			}
		}
	}
	
	function saveMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$option = $_POST['option'];
		$metadata_id = $_POST['metadata_id'];
		$product_id = $_POST['product_id'];
		
		// Remise à jour des compteurs de suppression et d'ajout 
		$deleted=0;
		$inserted=0;
		//echo "Metadata: ".$metadata_id." \r\n ";
		//echo "Product: ".$product_id." \r\n ";
		// Récupération des index des fieldsets
		$fieldsets = array();
		$fieldsets = explode(" | ", $_POST['fieldsets']);
		
		$keyVals = array();
		foreach($fieldsets as $fieldset)
		{
			$keys = explode(',', $fieldset);
			$keyVals[$keys[0]] = $keys[1];
		}
		
		//echo $_POST['gmd_MD_Metadata-gmd_MD_DataIdentification__2-gmd_abstract__2-gmd_LocalisedCharacterString-fr-FR__1']."\r\n";
		
		// Lister les langues que Joomla va prendre en charge
		//load folder filesystem class
		/*
		jimport('joomla.filesystem.folder');
		$path = JLanguage::getLanguagePath();
		$dirs = JFolder::folders( $path );
		$this->langList = array ();
		$rowid = 0;
		foreach ($dirs as $dir)
		{
			$files = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
			foreach ($files as $file)
			{
				$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$file);
	
				$row 			= new StdClass();
				$row->id 		= $rowid;
				$row->language 	= substr($file,0,-4);
	
				if (!is_array($data)) {
					continue;
				}
				foreach($data as $key => $value) {
					$row->$key = $value;
				}
	
				$this->langList[] = $row;
				$rowid++;
			}
		}
		*/
		
		// Langues à gérer
		$this->langList = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		
		
		// Parcourir les classes et les attributs
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc->formatOutput = true;
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new objectByMetadataId( $database );
		$rowObject->load($metadata_id);
		//echo "Product: ".$rowObject->id." \r\n ";
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix,':',c.name) as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON ns.id=c.namespace_id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObject();
		//echo $database->getQuery()." \r\n ";
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gco', 'http://www.isotc211.org/2005/gco');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gml', 'http://www.opengis.net/gml');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:bee', 'http://www.depth.ch/2008/bee');
		//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ext', 'http://www.depth.ch/2008/ext');
		//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:dc', 'http://purl.org/dc/elements/1.1');
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		$user =& JFactory::getUser();
		$user_id = $user->get('id');
		$database->setQuery( "SELECT a.root_id FROM #__sdi_account a,#__users u where a.root_id is null AND a.user_id = u.id and u.id=".$user_id." ORDER BY u.name" );
		$account_id = $database->loadResult();
		if ($account_id == null)
			$account_id = $user_id;

		
		/*
		$doc = "<gmd:MD_Metadata 
					xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
					xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
					xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
					xmlns:gml=\"http://www.opengis.net/gml\" 
					xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
					xmlns:srv=\"http://www.isotc211.org/2005/srv\"
					xmlns:ext=\"http://www.depth.ch/2008/ext\">";
		*/
		$path="/";
		//echo $root->id;
		ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
		
		//$XMLDoc->save("C:\\RecorderWebGIS\\".$metadata_id.".xml");
		//$XMLDoc->save("/home/sites/geodbmeta.depth.ch/web/administrator/components/com_easysdi_catalog/core/controller/xml.xml");
		
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$xmlstr = '<?xml version="1.0" encoding="UTF-8"?>
			<csw:Transaction service="CSW" version="2.0.2" xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" 
			    xmlns:apiso="http://www.opengis.net/cat/csw/apiso/1.0">
			    <csw:Delete>
			        <csw:Constraint version="1.0.0">
			            <ogc:Filter>
			                <ogc:PropertyIsLike wildCard="%" singleChar="_" escape="/">
			                    <ogc:PropertyName>apiso:identifier</ogc:PropertyName>
			                    <ogc:Literal>'.$metadata_id.'</ogc:Literal>
			                </ogc:PropertyIsLike>
			            </ogc:Filter>
			        </csw:Constraint>
			    </csw:Delete>
			</csw:Transaction>'; 
		
		$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
		
		$deleteResults = DOMDocument::loadXML($result);
		$xpathDelete = new DOMXPath($deleteResults);
		$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		
		$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
		
		if ($deleted <> 1)
		{
			$errorMsg = "erreur"; //$xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
			
			$response = '{
				    		success: false,
						    errors: {
						        xml: "Metadata has not been deleted. '.$errorMsg.'"
						    }
						}';
			print_r($response);
			die();
		}
	
		// Insérer dans Geonetwork la nouvelle version de la métadonnée
		$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<csw:Transaction service=\"CSW\"
		version=\"2.0.2\"
		xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" >
		<csw:Insert>
		".substr($XMLDoc->saveXML(), strlen('<?xml version="1.0" encoding="UTF-8"?>'))."
		</csw:Insert>
		</csw:Transaction>";
		//echo $XMLDoc->saveXML()." \r\n ";
			
		$result = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
		
		$insertResults = DOMDocument::loadXML($result);
		
		$xpathInsert = new DOMXPath($insertResults);
		$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		
		$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
		$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
		
		if ($inserted <> 1)
		{
			$errorMsg = "erreur"; //$xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
			$response = '{
				    		success: false,
						    errors: {
						        xml: "Metadata has not been inserted. '.$errorMsg.'"
						    }
						}';
			print_r($response);
			die();
		}
		else
		{
			//$result="";
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			//ADMIN_metadata::cswTest($xmlstr);
			$response = '{
				    		success: true,
						    errors: {
						        xml: "Id '.$metadata_id.' sur le service '.$catalogUrlBase.'. Supprimé: '.$deleted.', inséré: '.$inserted.'"
						    }
						}';
			print_r($response);
			die();
		}
		
		$rowObject = new object( $database );
		$rowObject->load( $product_id );
		$rowObject->checkin();
		
	}
	
	function cancelMetadata($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		// Check the attribute in if checked out
		$rowObject = new object( $database );
		$rowObject->load( $_GET['product_id'] );
		
		$rowObject->checkin();
		
		$mainframe->redirect("index.php?option=$option&task=listMetadata" );
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