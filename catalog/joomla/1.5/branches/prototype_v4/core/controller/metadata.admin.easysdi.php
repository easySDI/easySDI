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

class ADMIN_metadata {
	var $langList = array ();
	var $langCode = array ();
	var $defaultlang_isocode = "";
	var $defaultencoding_val = "";
	var $defaultencoding_code = "";
		
	function askForEditMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$uri =& JUri::getInstance();
		
		$objectversion = array();
		$listObjectversion = array();
		$database->setQuery( "SELECT id as value, name as text FROM #__sdi_objectversion WHERE object_id=".$id." ORDER BY name" );
		$objectversion= array_merge( $objectversion, $database->loadObjectList() );
		
		foreach($objectversion as $ov)
		{
			$listObjectversion[$ov->value] = $ov->text;
		}
		$listObjectversion = HTML_metadata::array2extjs($listObjectversion, false);
		
		if (count($objectversion) <= 1)
		{
			TOOLBAR_metadata::_EDIT();
			ADMIN_metadata::editMetadata($id,$option);
		}
		else
		{
			JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
			JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');
					
			$document =& JFactory::getDocument();
			$document->addStyleSheet($uri->base() . 'components/com_easysdi_catalog/ext/resources/css/ext-all.css');
			
			$javascript ="
			//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
	
			// Message d'attente pendant les chargements
			var myMask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});
			
			var selectEditWin;
			
			selectEditWin = new Ext.Window({
				  	title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_SELECTVERSION_ALERT'))."',
	                width:500,
	                height:130,
	                closeAction:'hide',
	                layout:'fit', 
				    border:true, 
				    closable:false,
				    resizable:false,
				    modal:true, 
				    frame:true,
				    items:[{ 
					     xtype:'form' 
					     ,id:'selectversionform' 
					     ,frame:true 
					     ,method:'POST' 
					     ,standardSubmit: true
					     ,items:[ 
					       { 
					       	 typeAhead:true,
					       	 triggerAction:'all',
					       	 mode:'local',
					         fieldLabel:'".addslashes(JText::_('CATALOG_METADATA_EDIT_ALERT_OBJECTVERSION_LABEL'))."', 
					         id:'version_id', 
					         hiddenName:'version_hidden', 
					         xtype: 'combo',
					         editable: false,
					         store: new Ext.data.ArrayStore({
								        id: 0,
								        fields: [
								            'value',
								            'text'
								        ],
								        data: ".$listObjectversion."
								    }),
							 valueField:'value',
							 displayField:'text'
					       },
					       { 
					         id:'cid[]', 
					         xtype: 'hidden',
					         value:'".$id."' 
					       },
					       { 
					         id:'task', 
					         xtype: 'hidden',
					         value:'editMetadata' 
					       },
					       { 
					         id:'option', 
					         xtype: 'hidden',
					         value:'".$option."' 
					       }
					    ] 
					     ,buttonAlign:'right' 
					     ,buttons: [{ 
			                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
			                    handler: function(){
			                    	myMask.show();
			                    	selectEditWin.items.get(0).getForm().submit();
			                    }
			                },
			                {
			                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
			                    handler: function(){
			                        selectEditWin.hide();
			                        window.open ('./index.php?option=".$option."&task=listObject','_parent');
			                    }
			                }]
					   }] 
			  });
	  		selectEditWin.show(true);
	  		/*Ext.MessageBox.prompt('Name', 'Please enter your name:', showResultText);
	  		
	  		function showResultText(text){
		        Ext.example.msg('Button Click', 'You entered the text \"{1}\".', text);
		    };*/
			";
			
			print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
		}
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
		
		// Récupérer l'objet
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
			$rowMetadata = new metadata( $database );
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
		
		// Est-ce que la métadonnée est publiée?
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
		echo "<hr>".htmlspecialchars($cswResults->saveXML())."<hr>";
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
        
        //$xpathResults->registerNamespace('ext','http://www.depth.ch/2008/ext');
        //$xpathResults->registerNamespace('dc','http://purl.org/dc/elements/1.1/');
        
        // Parcourir les noeuds enfants de la classe racine.
		// - Pour chaque classe rencontrée, ouvrir un niveau de hiérarchie dans la treeview
		// - Pour chaque attribut rencontré, créer un champ de saisie du type rendertype de la relation entre la classe et l'attribut
		//ADMIN_metadata::buildTree($root[0]->id, $xpathResults, $option);
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
		$xmlObjectParent = $xmlParent;
		
		$rowChilds = array();
		$query = "SELECT rel.id as rel_id, 
						 rel.guid as rel_guid,
						 rel.name as rel_name, 
						 rel.upperbound as rel_upperbound, 
						 rel.lowerbound as rel_lowerbound, 
						 rel.attributechild_id as attribute_id, 
						 rel.rendertype_id as rendertype_id, 
						 rel.classchild_id as child_id, 
						 rel.objecttypechild_id as objecttype_id, 
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
							//$nodeValue = stripslashes($nodeValue);
									
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
							//$nodeValue = stripslashes($nodeValue);
									
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
								$LangName = $LocName."-gmd_LocalisedCharacterString-".$lang->code_easysdi."__1";
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
											$usefullVals[$lang->code_easysdi] = $_POST[$key];
										}
									}
								}
								//$count = $count/count($this->langList);
								
								//echo "count langue: ".$langCount."\r\n";
								
								for ($langPos=1; $langPos<=$langCount; $langPos++)
								{
									$nodeValue=$usefullVals[$lang->code_easysdi];
									
									/*if (mb_detect_encoding($nodeValue) <> "UTF-8")
										$nodeValue = utf8_encode($nodeValue);
									*/
									//$nodeValue = stripslashes($nodeValue);
									$nodeValue = preg_replace("/\r\n|\r|\n/","&#xD;",$nodeValue);
									
									// Ajout des balises inhérantes aux locales
									if ($lang->defaultlang == true) // La langue par défaut
									{
										$XMLNode = $XMLDoc->createElement("gco:CharacterString", $nodeValue);
										$xmlLocParent->appendChild($XMLNode);
									}
									else // Les autres langues
									{
										$XMLNode = $XMLDoc->createElement("gmd:PT_FreeText");
										$xmlLocParent->appendChild($XMLNode);
										$xmlLocParent = $XMLNode;
										$XMLNode = $XMLDoc->createElement("gmd:textGroup");
										$xmlLocParent->appendChild($XMLNode);
										$xmlLocParent = $XMLNode;
										// Ajout de la valeur
										$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
										$xmlLocParent->appendChild($XMLNode);
										// Indication de la langue concernée
										$XMLNode->setAttribute('locale', "#".$lang->code);
										$xmlParent = $XMLNode;
									}
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
							//$nodeValue = stripslashes($nodeValue);
									
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
							//$nodeValue = stripslashes($nodeValue);
							if ($nodeValue <> "")
								$nodeValue = date('Y-m-d', strtotime($nodeValue));
							
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
											//$val = stripslashes($val);
											
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
											//$val = stripslashes($val);
											
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
								//$nodeValue = stripslashes($nodeValue);
									
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
								
								if (count($usefullVals) > 0)
									$nodeValues=split(",",$usefullVals[0]);		
								else
									$nodeValues=array();
								
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
							//$nodeValue = stripslashes($nodeValue);
									
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// DateTime
					case 8:
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
							//$nodeValue = stripslashes($nodeValue);
							if ($nodeValue <> "")
								$nodeValue = date('Y-m-d', strtotime($nodeValue))."T00:00:00";
							
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// ChoiceText
					case 9:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							//$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
							$partToCompare_temp1 = substr($key, strlen($parentName."-"));
							if (strpos($partToCompare_temp1, "-"))
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "-"));
							else
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "__"));
							
							$partToCompare_temp3 = substr($key, 0, strlen($parentName."-"));
							$partToCompare  = $partToCompare_temp3.$partToCompare_temp2;
								
							if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						//print_r($usefullVals);
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							//$nodeValue = stripslashes($nodeValue);

							// Récupérer la valeur liée à cette entrée de liste
							$query = "SELECT value FROM #__sdi_codevalue WHERE guid = '".$nodeValue."'";
							$database->setQuery( $query );
							$nodeValue = $database->loadResult();

							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						
						break;
					// ChoiceLocale
					case 10:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						//$usefullKeys=array();
						$count=0;
						foreach($keys as $key)
						{
							//$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
							$partToCompare_temp1 = substr($key, strlen($parentName."-"));
							if (strpos($partToCompare_temp1, "-"))
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "-"));
							else
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "__"));
							
							$partToCompare_temp3 = substr($key, 0, strlen($parentName."-"));
							$partToCompare  = $partToCompare_temp3.$partToCompare_temp2;
								
							if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						//print_r($usefullVals);
							
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							//$nodeValue = stripslashes($nodeValue);

							// Récupérer la valeur liée à cette entrée de liste
							$locValues = array();
							$query = "SELECT cl.code, t.content FROM #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE t.language_id=l.id AND l.codelang_id=cl.id AND t.element_guid = '".$nodeValue."'";
							$database->setQuery( $query );
							//echo $database->getQuery()."<br>";
							$locValues = $database->loadObjectList();
							//print_r($locValues);
							
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							$xmlLocParent = $XMLNode;
							
							foreach($locValues as $loc)
							{
								$isdefault = false;
								$codeToSave = "";
								foreach($this->langList as $lang)
								{
									if ($loc->code==$lang->code_easysdi)
									{
										//$nodeValue=$usefullVals[$loc->code];
										$nodeValue=$loc->content;
										$codeToSave = $lang->code;
										if ($lang->defaultlang== true)
										{
											$isdefault = true;
										}
										break;
									}
								}
								
								/*if (mb_detect_encoding($nodeValue) <> "UTF-8")
									$nodeValue = utf8_encode($nodeValue);
								*/
								//$nodeValue = stripslashes($loc->content);
								//$nodeValue = preg_replace("/\r\n|\r|\n/","&#xD;",$nodeValue);
								
								// Ajout des balises inhérantes aux locales
								if ($isdefault) // La langue par défaut
								{
									$XMLNode = $XMLDoc->createElement("gco:CharacterString", $nodeValue);
									$xmlLocParent->appendChild($XMLNode);
								}
								else // Les autres langues
								{
									$XMLNode = $XMLDoc->createElement("gmd:PT_FreeText");
									$xmlLocParent->appendChild($XMLNode);
									$xmlLocParent = $XMLNode;
									$XMLNode = $XMLDoc->createElement("gmd:textGroup");
									$xmlLocParent->appendChild($XMLNode);
									$xmlLocParent = $XMLNode;
									// Ajout de la valeur
									$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
									$xmlLocParent->appendChild($XMLNode);
									// Indication de la langue concernée
									$XMLNode->setAttribute('locale', "#".$codeToSave);
									$xmlParent = $XMLNode;
								}
							}
							/*
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;*/
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
							//$nodeValue = stripslashes($nodeValue);
									
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
			else if ($child->child_id <> null)
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
			else if ($child->objecttype_id <> null)
			{
				//$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
				$name = $parentName."-".str_replace(":", "_", $child->rel_isocode);
	
				/*$count=0;
				
				foreach($keyVals as $key => $val)
				{
					//echo "key: ".$key."\r\n";
					//echo "equals: ".$parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1"."\r\n";
					if ($key == $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1")
					{
						$count = $val;
						break;
					}
				}
				$count = $count - 1;
				
				echo "count: ".$count."\r\n";
				*/
				// Récupération des valeurs postées correspondantes
				$keys = array_keys($_POST);
				$usefullVals=array();
				//$usefullKeys=array();
				$count=0;
				foreach($keys as $key)
				{
					//$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
					$partToCompare_temp1 = substr($key, strlen($parentName."-"));
					
					if (strpos($partToCompare_temp1, "-"))
						$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "-"));
					else
						$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "__"));
					
					$partToCompare_temp3 = substr($key, 0, strlen($parentName."-"));
					$partToCompare  = substr($partToCompare_temp3.$partToCompare_temp2, 0, strlen($partToCompare_temp3.$partToCompare_temp2)-3);

					//echo $partToCompare." == ".$parentName."-".str_replace(":", "_", $child->rel_isocode)."\r\n";
					
					if ($partToCompare == $parentName."-".str_replace(":", "_", $child->rel_isocode))
					{
						if (substr($key, -6) <> "_index")
						{
							$count = $count+1;
							$usefullVals[] = $_POST[$key];
						}
					}
				}
				//echo "count: ".$count."\r\n";
				//echo count($usefullVals);
				//print_r($usefullVals); 
				
				for ($pos=0; $pos<$count; $pos++)
				{
					$searchName = $name."__".($pos+1)."-SEARCH__1"; 
					//echo $searchName;
					// Récupération des valeurs postées correspondantes
					$keys = array_keys($_POST);
					$usefullVals=array();
					//$usefullKeys=array();
					$searchCount=0;
					
					foreach($keys as $key)
					{
						$partToCompare = substr($key, 0, strlen($searchName));
						//echo "partToCompare: ".$partToCompare."\r\n";
						//echo "key: ".$key."\r\n";
						if ($partToCompare == $searchName)
						{
							if (substr($key, -6) <> "_index")
							{
								$searchCount = $searchCount+1;
								//$usefullKeys[] = $key;
								$usefullVals[] = $_POST[$key];
							}
						}
					}
					
					//echo "count: ".$searchCount."\r\n";
					//print_r($usefullVals); 
					
					if ($searchCount > 0)	
					{
						$nodeValue = $usefullVals[0];
						//$nodeValue = stripslashes($nodeValue);
						
						if (strlen($nodeValue) <> 36)
							continue;
						require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
						$catalogUrlBase = config_easysdi::getValue("catalog_url");
						$url = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$nodeValue;
			
						$XMLNode = $XMLDoc->createElement($child->rel_isocode);
						$XMLNode->setAttribute('xlink:show', "embed");
						$XMLNode->setAttribute('xlink:actuate', "onLoad");
						$XMLNode->setAttribute('xlink:type', "simple");
						$XMLNode->setAttribute('xlink:href', $url);
						$xmlObjectParent->appendChild($XMLNode);
						$xmlParent = $XMLNode;
					}
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						ADMIN_metadata::buildXMLTree($child->association_id, $parent, $name, &$XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
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
		$this->langCode=array();
		$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		$database->setQuery( "SELECT c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langCode= array_merge( $this->langCode, $database->loadResultArray() );
		
		// Langue par défaut
		$this->defaultlang = array();
		$database->setQuery( "SELECT isocode FROM #__sdi_language WHERE defaultlang=true" );
		$this->defaultlang = $database->loadObjectList();
		
		// Encodage par défaut
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$this->defaultencoding_val = config_easysdi::getValue("catalog_encoding_code");
		$this->defaultencoding_code = config_easysdi::getValue("catalog_encoding_val");
		
		// Parcourir les classes et les attributs
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc->formatOutput = true;
		// Récupérer l'objet lié à cette métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		//echo "Metadata: ".$rowMetadata->guid." \r\n ";
		$rowObject = new object( $database );
		$rowObject->load($product_id);
		//echo "Product: ".$rowObject->id." \r\n ";
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix,':',c.name) as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObject();
		//echo $database->getQuery()." \r\n ";
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		
		// Récupération des namespaces à inclure
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		 foreach ($namespacelist as $namespace)
        {
        	$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:'.$namespace->prefix, $namespace->uri);
        	//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gco', 'http://www.isotc211.org/2005/gco');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gml', 'http://www.opengis.net/gml');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:bee', 'http://www.depth.ch/2008/bee');
        } 
		
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
		
		// Construire les champs concernant la langue par défaut et l'encodage par défaut
		$XMLNodeLang = $XMLDoc->createElement("gmd:language");
		$XMLNode->appendChild($XMLNodeLang);
		$XMLNodeLang->appendChild($XMLDoc->createElement("gco:CharacterString", $this->defaultlang[0]->isocode));
		
		$XMLNodeEncoding = $XMLDoc->createElement("gmd:characterSet");
		$XMLNode->appendChild($XMLNodeEncoding);
		$XMLNodeCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode");
		$XMLNodeCode->setAttribute('codeListValue', $this->defaultencoding_code);
		$XMLNodeCode->setAttribute('codeList', "http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode");
		$XMLNodeEncoding->appendChild($XMLNodeCode);
		
		// Construire la définition des locales
		foreach($this->langList as $lang)
		{
			if (!$lang->defaultlang)
			{
				$XMLNodeLoc = $XMLDoc->createElement("gmd:locale");
				$XMLNode->appendChild($XMLNodeLoc);
				$XMLNodeLocPT = $XMLDoc->createElement("gmd:PT_Locale");
				$XMLNodeLocPT->setAttribute('id', $lang->code);
				$XMLNodeLoc->appendChild($XMLNodeLocPT);
				$XMLNodeLocCode = $XMLDoc->createElement("gmd:languageCode");
				$XMLNodeLocPT->appendChild($XMLNodeLocCode);
				$XMLNodeLocVal = $XMLDoc->createElement("gmd:LanguageCode", $lang->name);
				$XMLNodeLocVal->setAttribute('codeListValue', $lang->isocode);
				$XMLNodeLocVal->setAttribute('codeList', "#LanguageCode");
				$XMLNodeLocCode->appendChild($XMLNodeLocVal);
				$XMLNodeEnc = $XMLDoc->createElement("gmd:characterEncoding");
				$XMLNodeLocPT->appendChild($XMLNodeEnc);
				$XMLNodeEncCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode", $this->defaultencoding_val);
				$XMLNodeEncCode->setAttribute('codeListValue', $this->defaultencoding_code);
				$XMLNodeEncCode->setAttribute('codeList', "#MD_CharacterSetCode");
				$XMLNodeEnc->appendChild($XMLNodeEncCode);
			}
		}
		
		ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
		
		//$XMLDoc->save("C:\\RecorderWebGIS\\".$metadata_id.".xml");
		//$XMLDoc->save("/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/xml.xml");
		
		
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
		else if ($_POST['task'] == 'saveMetadata')
		{
			//$result="";
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			//ADMIN_metadata::cswTest($xmlstr);
			$response = '{
				    		success: true,
						    errors: {
						        xml: "Metadata saved"
						    }
						}';
			print_r($response);
			die();
		}
		
		// Mettre à jour la métadonnée liée (state et revision)
		$rowMetadata = new metadata($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		
		//$rowMetadata->metadatastate_id = 1;
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		// Checkin object
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
		$rowObject->load( $_POST['product_id'] );
			
		$rowObject->checkin();
		
		//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
	}
	
	function previewXMLMetadata($option)
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
		
		// Langues à gérer
		$this->langList = array();
		$this->langCode = array();
		$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		$database->setQuery( "SELECT c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langCode= array_merge( $this->langCode, $database->loadResultArray() );
		
		// Langue par défaut
		$this->defaultlang = array();
		$database->setQuery( "SELECT isocode FROM #__sdi_language WHERE defaultlang=true" );
		$this->defaultlang = $database->loadObjectList();
		
		// Encodage par défaut
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$this->defaultencoding_val = config_easysdi::getValue("catalog_encoding_code");
		$this->defaultencoding_code = config_easysdi::getValue("catalog_encoding_val");
		
		// Parcourir les classes et les attributs
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc->formatOutput = true;
		// Récupérer l'objet lié à cette métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		$rowObject = new object( $database );
		$rowObject->load($product_id);
		//echo "Product: ".$rowObject->id." \r\n ";
		// Récupérer la classe racine du profile du type d'objet
		//$query = "SELECT c.name as name, c.isocode as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$query = "SELECT c.name as name, CONCAT(ns.prefix, ':', c.name) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObject();
		//echo $database->getQuery()." \r\n ";
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		
		// Récupération des namespaces à inclure
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		 foreach ($namespacelist as $namespace)
        {
        	$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:'.$namespace->prefix, $namespace->uri);
        	//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gco', 'http://www.isotc211.org/2005/gco');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gml', 'http://www.opengis.net/gml');
			//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:bee', 'http://www.depth.ch/2008/bee');
        } 
		
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

		
		$path="/";
		//echo $root->id;
		
		// Construire les champs concernant la langue par défaut et l'encodage par défaut
		$XMLNodeEncoding = $XMLDoc->createElement("gmd:characterSet");
		$XMLNode->appendChild($XMLNodeEncoding);
		$XMLNodeCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode");
		$XMLNodeCode->setAttribute('codeListValue', $this->defaultencoding_code);
		$XMLNodeCode->setAttribute('codeList', 'http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode');
		$XMLNodeEncoding->appendChild($XMLNodeCode);
		
		$XMLNodeLang = $XMLDoc->createElement("gmd:language");
		$XMLNode->appendChild($XMLNodeLang);
		$XMLNodeLang->appendChild($XMLDoc->createElement("gco:CharacterString", $this->defaultlang[0]->isocode));
		
		// Construire la définition des locales
		foreach($this->langList as $lang)
		{
			if (!$lang->defaultlang)
			{
				$XMLNodeLoc = $XMLDoc->createElement("gmd:locale");
				$XMLNode->appendChild($XMLNodeLoc);
				
				$XMLNodeLocPT = $XMLDoc->createElement("gmd:PT_Locale");
				$XMLNodeLocPT->setAttribute('id', $lang->code);
				
				$XMLNodeLoc->appendChild($XMLNodeLocPT);
				$XMLNodeLocCode = $XMLDoc->createElement("gmd:languageCode");
				$XMLNodeLocPT->appendChild($XMLNodeLocCode);
				
				$XMLNodeLocVal = $XMLDoc->createElement("gmd:LanguageCode", $lang->name);
				$XMLNodeLocVal->setAttribute('codeListValue', $lang->isocode);
				$XMLNodeLocVal->setAttribute('codeList', "#LanguageCode");
				$XMLNodeLocCode->appendChild($XMLNodeLocVal);
				
				$XMLNodeEnc = $XMLDoc->createElement("gmd:characterEncoding");
				$XMLNodeLocPT->appendChild($XMLNodeEnc);
				
				$XMLNodeEncCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode", $this->defaultencoding_val);
				$XMLNodeEncCode->setAttribute('codeListValue', $this->defaultencoding_code);
				$XMLNodeEncCode->setAttribute('codeList', "#MD_CharacterSetCode");
				$XMLNodeEnc->appendChild($XMLNodeEncCode);
			}
		}
		
		// COnstruire la partie dynamique du xml
		ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
		
		//$file = "C:\\RecorderWebGIS\\_previewXML_".$metadata_id.".xml";
		// XMLDoc->save($file);
		//$XMLDoc->save("/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/xml.xml");
		
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		
		// Construction de la fenêtre popup pour l'affichage du xml
		/*?>
		<script>
			alert('<?php echo $XMLDoc->saveXML();?>');
		</script>
		<script>
			//pop-up window for preview
			function popup() {
			win = window.open('','myWin','toolbars=0');
			document.post.action= "file:".$file;
			document.post.target='myWin';
			document.post.submit();
			document.post.action='validate.php';
			document.post.target='_self';
			
			}
		</script>
				
		<a rel="{handler: 'iframe', size: {x: 800 y: 800}}" href="<?php echo "file:".$file;?>" class="modal">link name</a>
		<?php
		*/
		if ($XMLDoc)
		{
			//$xmlToReturn = htmlentities($XMLDoc->saveXML(), ENT_COMPAT, "UTF-8");
			$xmlToReturn = addslashes($XMLDoc->saveXML());
			$response = '{
							success: true,
						    file: {
						        xml: "'.str_replace(chr(10), "<br>", $xmlToReturn).'"
						    }
						}';
			print_r($response);
			die();
		}
		else
		{
			$response = '{
							success: false,
						    errors: {
						        xml: "Problème rencontré"
						    }
						}';
			print_r($response);
			die();
		}
	}
	
	function validateMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		ADMIN_metadata::saveMetadata($option);
		
		$metadata_id = $_POST['metadata_id'];
		$account_id = $_POST['account_id'];
		
		// Passer en statut validé
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->metadatastate_id=3;
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		//print_r($rowMetadata);
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		$response = '{
			    		success: true,
					    errors: {
					        xml: "Metadata validated"
					    }
					}';
		print_r($response);
		die();
	}
	
	function invalidateMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$metadata_id = $_POST['metadata_id'];
		$account_id = $_POST['account_id'];
		
		// Passer en statut en travail
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->metadatastate_id=4;
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		//print_r($rowMetadata);
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		$response = '{
			    		success: true,
					    errors: {
					        xml: "Metadata invalidated"
					    }
					}';
		print_r($response);
		die();
	}
	
	function validateForPublishMetadata($option)
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
		
		// Langues à gérer
		$this->langList = array();
		$this->langCode=array();
		$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		$database->setQuery( "SELECT c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.id" );
		$this->langCode= array_merge( $this->langCode, $database->loadResultArray() );
		
		// Langue par défaut
		$this->defaultlang = array();
		$database->setQuery( "SELECT isocode FROM #__sdi_language WHERE defaultlang=true" );
		$this->defaultlang = $database->loadObjectList();
		
		// Encodage par défaut
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$this->defaultencoding_val = config_easysdi::getValue("catalog_encoding_code");
		$this->defaultencoding_code = config_easysdi::getValue("catalog_encoding_val");
		
		// Parcourir les classes et les attributs
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc->formatOutput = true;
		// Récupérer l'objet lié à cette métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		//echo "Metadata: ".$rowMetadata->guid." \r\n ";
		$rowObject = new object( $database );
		$rowObject->load($product_id);
		//echo "Product: ".$rowObject->id." \r\n ";
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix,':',c.name) as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObject();
		//echo $database->getQuery()." \r\n ";
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		
		// Récupération des namespaces à inclure
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		 foreach ($namespacelist as $namespace)
        {
        	$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:'.$namespace->prefix, $namespace->uri);
        } 
		
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

		$path="/";
		
		// Construire les champs concernant la langue par défaut et l'encodage par défaut
		$XMLNodeLang = $XMLDoc->createElement("gmd:language");
		$XMLNode->appendChild($XMLNodeLang);
		$XMLNodeLang->appendChild($XMLDoc->createElement("gco:CharacterString", $this->defaultlang[0]->isocode));
		
		$XMLNodeEncoding = $XMLDoc->createElement("gmd:characterSet");
		$XMLNode->appendChild($XMLNodeEncoding);
		$XMLNodeCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode");
		$XMLNodeCode->setAttribute('codeListValue', $this->defaultencoding_code);
		$XMLNodeCode->setAttribute('codeList', "http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode");
		$XMLNodeEncoding->appendChild($XMLNodeCode);
		
		// Construire la définition des locales
		foreach($this->langList as $lang)
		{
			if (!$lang->defaultlang)
			{
				$XMLNodeLoc = $XMLDoc->createElement("gmd:locale");
				$XMLNode->appendChild($XMLNodeLoc);
				$XMLNodeLocPT = $XMLDoc->createElement("gmd:PT_Locale");
				$XMLNodeLocPT->setAttribute('id', $lang->code);
				$XMLNodeLoc->appendChild($XMLNodeLocPT);
				$XMLNodeLocCode = $XMLDoc->createElement("gmd:languageCode");
				$XMLNodeLocPT->appendChild($XMLNodeLocCode);
				$XMLNodeLocVal = $XMLDoc->createElement("gmd:LanguageCode", $lang->name);
				$XMLNodeLocVal->setAttribute('codeListValue', $lang->isocode);
				$XMLNodeLocVal->setAttribute('codeList', "#LanguageCode");
				$XMLNodeLocCode->appendChild($XMLNodeLocVal);
				$XMLNodeEnc = $XMLDoc->createElement("gmd:characterEncoding");
				$XMLNodeLocPT->appendChild($XMLNodeEnc);
				$XMLNodeEncCode = $XMLDoc->createElement("gmd:MD_CharacterSetCode", $this->defaultencoding_val);
				$XMLNodeEncCode->setAttribute('codeListValue', $this->defaultencoding_code);
				$XMLNodeEncCode->setAttribute('codeList', "#MD_CharacterSetCode");
				$XMLNodeEnc->appendChild($XMLNodeEncCode);
			}
		}
		
		ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
		
		//$XMLDoc->save("C:\\RecorderWebGIS\\".$metadata_id.".xml");
		//$XMLDoc->save("/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/xml.xml");
		
		
		if (!$XMLDoc)
		{
			$errorMsg = "XML non construit";
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
			$xmlToReturn = addslashes($XMLDoc->saveXML());
			$response = '{
							success: true,
						    file: {
						        xml: "'.str_replace(chr(10), "<br>", $xmlToReturn).'"
						    }
						}';
			print_r($response);
			die();
		}
	}
	
	function publishMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$metadata_id = $_POST['metadata_id'];
		$publishdate = $_POST['publishdate'];
		
		$product_id= $_POST['product_id'];
		$account_id= $_POST['account_id'];
		
		$xml= $_POST['xml'];
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc = DOMDocument::loadXML($xml);

		//print_r($_POST);
		//$XMLDoc->save("C:\\RecorderWebGIS\\publish\\".$metadata_id.".xml");
		
		// Enregistrement de la métadonnée dans geonetwork
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
			// Enregistrer la date de publication
			$rowMetadata = new metadataByGuid($database);
			$rowMetadata->load($metadata_id);
			$rowMetadata->published=date('Y-m-d h:i:s', strtotime($publishdate));
			$rowMetadata->metadatastate_id=1;
			$rowMetadata->updated = date('Y-m-d H:i:s');
			$rowMetadata->updatedby = $account_id;
			
			if (!$rowMetadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listMetadata" );
				exit();
			}
			
			// Checkin object
			$rowObject = new object( $database );
			$rowObject->load( $product_id );
			$rowObject->checkin();
			
			
			$response = '{
				    		success: true,
						    errors: {
						        xml: "published"
						    }
						}';
			print_r($response);
			die();
		}
	}
	
	function assignMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$success= true;
		
		$metadata_id = $_POST['metadata_id'];
		$object_id = $_POST['object_id'];
		$editor = $_POST['editor_hidden'];
		$information = $_POST['information'];
		
		$rowObject = new object($database);
		$rowObject->load($object_id);
		
		// Enregistrer l'éditeur auxquel la métadonnée est assignée
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->editor_id=$editor;
		
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		// Remplir l'historique d'assignement
		$user = JFactory::getUser(); 
		$rowCurrentUser = new accountByUserId($database);
		$rowCurrentUser->load($user->get('id'));
		
		$rowHistory = new historyassign($database);
		$rowHistory->object_id=$object_id;
		$rowHistory->account_id=$editor;
		$rowHistory->assigned=date ("Y-m-d H:i:s");
		$rowHistory->assignedby=$rowCurrentUser->id;
		$rowHistory->information=$information;
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		$rowHistory->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowHistory->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadata" );
			exit();
		}
		
		// Envoi d'email
		$rowUser = array();
		$database->setQuery( "SELECT * FROM #__sdi_account a, #__users u WHERE a.user_id=u.id AND a.id=".$editor );
		$rowUser= array_merge( $rowUser, $database->loadObjectList() );
		/*
		$languageOfMail = ""; 
		$languageOfMail = substr($rowUser[0]->params, strpos($rowUser[0]->params, "language="));
		$languageOfMail = substr($languageOfMail, strlen("language="), strpos($languageOfMail, "\n")-strlen("language="));
		
		if ($languageOfMail)
			$success = ADMIN_metadata::sendMailByEmail($rowUser[0]->email,JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_SUBJECT"),JText::sprintf("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY",$rowUser[0]->username,$rowObject->name));
		else
			$success = ADMIN_metadata::sendMailByEmail($rowUser[0]->email,JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_SUBJECT"),JText::sprintf("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY",$rowUser[0]->username,$rowObject->name));
		*/
		$body = JText::sprintf("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY",$user->username,$rowObject->name)."\n\n".JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY_INFORMATION").":\n".$information;
		
		//echo $body ;
		$success = ADMIN_metadata::sendMailByEmail($rowUser[0]->email,JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_SUBJECT"),$body);
		if (!$success) 
		{
			// Retour de la réponse au formulaire ExtJS
			$response = '{
				    		success: false,
						    errors: {
						        xml: "'.JText::_("CORE_SENDMAIL_ERROR").'"
						    }
						}';
			print_r($response);
			die();
		}
		else
		{
			// Retour de la réponse au formulaire ExtJS
			$response = '{
				    		success: true,
						    errors: {
						        xml: "Metadata assigned"
						    }
						}';
			print_r($response);
			die();
		}
	}
	
	function importXMLMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$metadata_id = $_POST['metadata_id'];
		$object_id = $_POST['object_id'];
		$xmlfile = file_get_contents($_FILES['xmlfile']['tmp_name']);
		$xslfile = $_POST['xslfile'];
		 
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		//$rowMetadata = new metadata( $database );
		//$rowMetadata->load( $rowObject->metadata_id );
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.name) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, at.isocode as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
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
		
		// Est-ce que la métadonnée est publiée?
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
		
		//$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$rowMetadata->guid;
		
		// En GET
		//$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		// Télécharger le XML indiqué par l'utilisateur
		//echo $xmlfile."\r\n";
		$xml = DOMDocument::loadXML($xmlfile);
		
		//$xml = DOMDocument::load($_FILES['xmlfile']['tmp_name']);
		
		/*echo "Récupération de tout le document :\n";
		echo $xml->saveXML() . "\n";
		*/
		
		// Appliquer le XSL
		$style = new DomDocument();
		$style->load($xslfile);
		/*echo "Récupération du style :\n";
		echo $style->saveXML() . "\n";
		*/
		
        $processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$cswResults = $processor->transformToDoc($xml);
		
		/*echo "Récupération du xml résultat :\n";
		echo $cswResults->saveXML();
		*/
		//$cswResults->save("C:\RecorderWebGIS\merging\FE_import_ESRI_".$metadata_id."_file2.xml");
        
		/* Remplacer la valeur du noeud fileIdentifier par la valeur courante metadata_id*/
        $nodeList = &$cswResults->getElementsByTagName($idrow[0]->name);

        foreach ($nodeList as $node)
        {
        	// Remplacer la valeur de fileIdentifier par celle de metadata_id pour que
        	// la métadonnée importée prenne son nouvel id
        	if ($node->parentNode->nodeName == $root[0]->ns.":".$root[0]->name)
        	{
        		foreach ($node->childNodes as $child)
        		{
        			if ($child->nodeName == $idrow[0]->list_isocode)
        			{
        				$child->nodeValue = $metadata_id;
        			}
        		}
        	}
        }
        
        /*echo "APRES Récupération du xml résultat :\n";
		echo $cswResults->saveXML();
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
        
        //$xpathResults->registerNamespace('ext','http://www.depth.ch/2008/ext');
        //$xpathResults->registerNamespace('dc','http://purl.org/dc/elements/1.1/');
        /*
        // Merging
        $actualXML = DOMDocument::load($catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$metadata_id);
		if ($actualXML <> false)
			$actualXpathResults = new DOMXPath($actualXML);
		else
			$actualXpathResults = new DOMXPath($doc);
		$actualXpathResults->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
        $actualXpathResults->registerNamespace('srv','http://www.isotc211.org/2005/srv');
        $actualXpathResults->registerNamespace('xlink','http://www.w3.org/1999/xlink');
        $actualXpathResults->registerNamespace('gts','http://www.isotc211.org/2005/gts');
        
        // Récupération des namespaces à inclure
		$namespacelist = array();
		//$namespacelist[] = JHTML::_('select.option','0', JText::_("CATALOG_ATTRIBUTE_NAMESPACE_LIST") );
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		foreach ($namespacelist as $namespace)
        {
        	$actualXpathResults->registerNamespace($namespace->prefix,$namespace->uri);
        } 
        
        //$actualXML->save("C:\RecorderWebGIS\merging\FE_import_ESRI_".$metadata_id."_file1.xml");
        /*
        $mergeXml = new DomDocument();
        $mergePath = " <merge xmlns=\"http://informatik.hu-berlin.de/merge\">
					      <file1>FE_import_ESRI_".$metadata_id."_file1.xml</file1>
					      <file2>FE_import_ESRI_".$metadata_id."_file2.xml</file2>
					   </merge>";
        
        $mergeXml = DOMDocument::loadXML($mergePath);
		//$mergeXml->save("C:\RecorderWebGIS\merging\FE_import_ESRI_".$metadata_id."_file3.xml");
        
        $mergeStyle = new DomDocument();
		$mergeStyle->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XML_merge_ESRI.xsl');
		
		$mergeProcessor = new xsltProcessor();
		$mergeProcessor->importStylesheet($mergeStyle);
		$mergeResults = new DomDocument();
		
        $mergeResults = $mergeProcessor->transformToDoc($mergeXml);
		$mergeResults->save("C:\RecorderWebGIS\merging\FE_import_ESRI_".$metadata_id."_merged.xml");
        
		*/
        
		/* Fusionner les xml*/
        /*
        $nodeListMD = &$actualXML->getElementsByTagName($root[0]->name);
		$nodeListESRI = &$cswResults->getElementsByTagName($root[0]->name);
        
		$currentNode = $actualXpathResults->query("//gmd:identificationInfo", $nodeListMD->item(0));
        // echo "gmd:identificationInfo: ".$currentNode->length."<br>";
        if ($currentNode->length > 0)
        {
        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification", $nodeListMD->item(0));
        	// echo "gmd:MD_DataIdentification: ".$currentNode->length."<br>";
	        if ($currentChildNode->length > 0)
	        {
	        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent", $nodeListMD->item(0));
	        	// echo "gmd:extent: ".$currentNode->length."<br>";
		        if ($currentChildNode->length > 0)
		        {
		        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent", $nodeListMD->item(0));
		        	// echo "gmd:EX_Extent: ".$currentNode->length."<br>";
			        if ($currentChildNode->length > 0)
			        {
			        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement", $nodeListMD->item(0));
			        	// echo "gmd:geographicElement: ".$currentNode->length."<br>";
				        if ($currentChildNode->length > 0)
				        {
				        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox", $nodeListMD->item(0));
				        	// echo "gmd:EX_GeographicBoundingBox: ".$currentNode->length."<br>";
					        if ($currentChildNode->length > 0)
					        {
					        	// North
					        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude", $nodeListMD->item(0));
					        	// echo "gmd:northBoundLatitude: ".$currentNode->length."<br>";
					        	// Insérer le noeud gmd:northBoundLatitude du xml ESRI normalisé dans le xml courant de la métadonnée
					        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude", $nodeListESRI->item(0));
					        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
					        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
						        // echo "nodeImported: ".$nodeImported->nodeName."<br>";
						        	
						        if ($currentChildNode->length > 0)
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$nodeReplaced = $currentChildNode->item(0);
						        	// echo "nodeToReplace: ".$nodeReplaced->nodeName."<br>";
						        	$placeToCopy->item(0)->replaceChild($nodeImported, $nodeReplaced);
						        }
						        else
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundindBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$placeToCopy->item(0)->appendChild($nodeImported);
						        }
						        
					        	// South
					        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude", $nodeListMD->item(0));
					        	// echo "gmd:southBoundLatitude: ".$currentNode->length."<br>";
					        	// Insérer le noeud gmd:southBoundLatitude du xml ESRI normalisé dans le xml courant de la métadonnée
					        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude", $nodeListESRI->item(0));
					        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
					        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
						        // echo "nodeImported: ".$nodeImported->nodeName."<br>";
						        	
						        if ($currentChildNode->length > 0)
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$nodeReplaced = $currentChildNode->item(0);
						        	// echo "nodeToReplace: ".$nodeReplaced->nodeName."<br>";
						        	$placeToCopy->item(0)->replaceChild($nodeImported, $nodeReplaced);
						        }
						        else
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundindBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$placeToCopy->item(0)->appendChild($nodeImported);
						        }
						        
					        	// East
					        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude", $nodeListMD->item(0));
					        	// echo "gmd:eastBoundLongitude: ".$currentNode->length."<br>";
					        	// Insérer le noeud gmd:eastBoundLongitude du xml ESRI normalisé dans le xml courant de la métadonnée
					        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude", $nodeListESRI->item(0));
					        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
					        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
						        // echo "nodeImported: ".$nodeImported->nodeName."<br>";
						        	
						        if ($currentChildNode->length > 0)
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$nodeReplaced = $currentChildNode->item(0);
						        	// echo "nodeToReplace: ".$nodeReplaced->nodeName."<br>";
						        	$placeToCopy->item(0)->replaceChild($nodeImported, $nodeReplaced);
						        }
						        else
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundindBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$placeToCopy->item(0)->appendChild($nodeImported);
						        }
						        
					        	// West
					        	$currentChildNode = $actualXpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude", $nodeListMD->item(0));
					        	// echo "gmd:westBoundLongitude: ".$currentNode->length."<br>";
					        	// Insérer le noeud gmd:westBoundLongitude du xml ESRI normalisé dans le xml courant de la métadonnée
					        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude", $nodeListESRI->item(0));
					        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
					        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
						        // echo "nodeImported: ".$nodeImported->nodeName."<br>";
						        	
						        if ($currentChildNode->length > 0)
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$nodeReplaced = $currentChildNode->item(0);
						        	// echo "nodeToReplace: ".$nodeReplaced->nodeName."<br>";
						        	$placeToCopy->item(0)->replaceChild($nodeImported, $nodeReplaced);
						        }
						        else
						        {
						        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundindBox", $nodeListMD->item(0));
						        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
						        	$placeToCopy->item(0)->appendChild($nodeImported);
						        }
					        }
					        else
					        {
					        	// Insérer le noeud gmd:EX_GeographicBoundindBox du xml ESRI normalisé dans le xml courant de la métadonnée
					        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement/gmd:EX_GeographicBoundindBox", $nodeListESRI->item(0));
					        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
					        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement", $nodeListMD->item(0));
					        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
					        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
					        	$placeToCopy->item(0)->appendChild($nodeImported);
					        }
				        }
				        else
				        {
				        	// Insérer le noeud gmd:geographicElement du xml ESRI normalisé dans le xml courant de la métadonnée
				        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent/gmd:geographicElement", $nodeListESRI->item(0));
				        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
				        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent", $nodeListMD->item(0));
				        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
				        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
				        	$placeToCopy->item(0)->appendChild($nodeImported);
				        }
			        }
			        else
			        {
				        // Insérer le noeud gmd:EX_Extent du xml ESRI normalisé dans le xml courant de la métadonnée
				        $nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent/gmd:EX_Extent", $nodeListESRI->item(0));
			        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
			        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent", $nodeListMD->item(0));
			        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
			        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
			        	$placeToCopy->item(0)->appendChild($nodeImported);
			        }
		        }
		        else
		        {
		        	// Insérer le noeud gmd:extent du xml ESRI normalisé dans le xml courant de la métadonnée
		        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification/gmd:extent", $nodeListESRI->item(0));
		        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
		        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification", $nodeListMD->item(0));
		        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
		        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
		        	$placeToCopy->item(0)->appendChild($nodeImported);
		        }
	        }
	        else
	        {
	        	// Insérer le noeud gmd:MD_DataIdentification du xml ESRI normalisé dans le xml courant de la métadonnée
	        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo/gmd:MD_DataIdentification", $nodeListESRI->item(0));
	        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
	        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:identificationInfo", $nodeListMD->item(0));
	        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
	        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
	        	$placeToCopy->item(0)->appendChild($nodeImported);	
	        }
        }
        else
        {
        	// Insérer le noeud gmd:identificationInfo du xml ESRI normalisé dans le xml courant de la métadonnée
        	$nodeToCopy = $xpathResults->query("//gmd:identificationInfo", $nodeListESRI->item(0));
        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata", $nodeListMD->item(0));
        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
        	$placeToCopy->item(0)->appendChild($nodeImported);
        }
        
		$currentNode = $actualXpathResults->query("//gmd:contentInfo", $nodeListMD->item(0));
        // echo "gmd:identificationInfo: ".$currentNode->length."<br>";
        if ($currentNode->length > 0)
        {
        	$currentChildNode = $actualXpathResults->query("//gmd:contentInfo/gmd:MD_FeatureCatalogueDescription", $nodeListMD->item(0));
        	// echo "gmd:MD_DataIdentification: ".$currentNode->length."<br>";
	        if ($currentChildNode->length > 0)
	        {
	        	// Insérer le noeud gmd:class du xml ESRI normalisé dans le xml courant de la métadonnée
	        	$nodeToCopy = $xpathResults->query("//gmd:contentInfo/gmd:MD_FeatureCatalogueDescription/gmd:class", $nodeListESRI->item(0));
	        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
	        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:contentInfo/gmd:MD_FeatureCatalogueDescription", $nodeListMD->item(0));
	        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
	        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
	        	$placeToCopy->item(0)->appendChild($nodeImported);
	        }
	        else
	        {
	        	// Insérer le noeud gmd:MD_FeatureCatalogueDescription du xml ESRI normalisé dans le xml courant de la métadonnée
	        	$nodeToCopy = $xpathResults->query("//gmd:contentInfo/gmd:MD_FeatureCatalogueDescription", $nodeListESRI->item(0));
	        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
	        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata/gmd:contentInfo", $nodeListMD->item(0));
	        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
	        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
	        	$placeToCopy->item(0)->appendChild($nodeImported);	
	        }
        }
        else
        {
        	// Insérer le noeud gmd:contentInfo du xml ESRI normalisé dans le xml courant de la métadonnée
        	$nodeToCopy = $xpathResults->query("//gmd:contentInfo", $nodeListESRI->item(0));
        	// echo "nodeToCopy: ".$nodeToCopy->item(0)->nodeName."<br>";
        	$placeToCopy = $actualXpathResults->query("//gmd:MD_Metadata", $nodeListMD->item(0));
        	// echo "placeToCopy: ".$placeToCopy->item(0)->nodeName."<br>";
        	$nodeImported = $actualXML->importNode($nodeToCopy->item(0), true);
        	$placeToCopy->item(0)->appendChild($nodeImported);
        }
        
        $actualXML->save("C:\RecorderWebGIS\merging\MERGED.xml");
        */
        // Parcourir les noeuds enfants de la classe racine.
		// - Pour chaque classe rencontrée, ouvrir un niveau de hiérarchie dans la treeview
		// - Pour chaque attribut rencontré, créer un champ de saisie du type rendertype de la relation entre la classe et l'attribut
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $option);
		
		/*// Retour de la réponse au formulaire ExtJS
		$xmlToReturn = addslashes($cswResults->saveXML());
		$cswResults->save("C:\RecorderWebGIS\import_".$metadata_id.".xml");
		$response = '{
			    		success: true,
					    file: {
					        xml: "'.str_replace(chr(10), "<br>", $xmlToReturn).'"
					    }
					}';
		print_r($response);
		die();*/
	}

	function importCSWMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$metadata_id = $_POST['metadata_id'];
		$object_id = $_POST['object_id'];
		$xslfile = $_POST['xslfile'];
		$importid = $_POST['id'];
		$url = $_POST['url'];
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.name) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, at.isocode as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a RIGHT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
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
		
		// Est-ce que la métadonnée est publiée?
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
		$catalogUrlGetRecordById = $url."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$importid;
		
		// En GET
		$xml = DOMDocument::load($catalogUrlGetRecordById);
		
		// Appliquer le XSL
		$style = new DomDocument();
		$style->load($xslfile);
		/*echo "Récupération du style :\n";
		echo $style->saveXML() . "\n";
		*/
		$processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$cswResults = $processor->transformToDoc($xml);
		
		/*echo "Récupération du xml résultat :\n";
		echo $cswResults->saveXML();
		*/
		
		/* Remplacer la valeur du noeud fileIdentifier par la valeur courante metadata_id*/
        $nodeList = &$cswResults->getElementsByTagName($idrow[0]->name);

        foreach ($nodeList as $node)
        {
        	// Remplacer la valeur de fileIdentifier par celle de metadata_id pour que
        	// la métadonnée importée prenne son nouvel id 
        	if ($node->parentNode->nodeName == $root[0]->ns.":".$root[0]->name)
        	{
        		foreach ($node->childNodes as $child)
        		{
        			if ($child->nodeName == $idrow[0]->list_isocode)
        			{
        				$child->nodeValue = $metadata_id;
        			}
        		}
        	}
        }
        
        /*echo "\nAPRES Récupération du xml résultat :\n";
		echo $cswResults->saveXML();
       */
        //$cswResults->save("C:\RecorderWebGIS\import_".$metadata_id.".xml");
        
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
        
        //$xpathResults->registerNamespace('ext','http://www.depth.ch/2008/ext');
        //$xpathResults->registerNamespace('dc','http://purl.org/dc/elements/1.1/');
        
        // Parcourir les noeuds enfants de la classe racine.
		// - Pour chaque classe rencontrée, ouvrir un niveau de hiérarchie dans la treeview
		// - Pour chaque attribut rencontré, créer un champ de saisie du type rendertype de la relation entre la classe et l'attribut
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $option);
		
		// Retour de la réponse au formulaire ExtJS
		/*$xmlToReturn = addslashes($cswResults->saveXML());
		$cswResults->save("C:\RecorderWebGIS\import_".$metadata_id.".xml");
		$response = '{
			    		success: true,
					    file: {
					        xml: "'.str_replace(chr(10), "<br>", $xmlToReturn).'"
					    }
					}';
		print_r($response);
		die();
		*/
	}

	function replicateMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$metadata_id = $_POST['metadata_id'];
		$object_id = $_POST['object_id'];
		$metadata_guid = $_POST['metadata_guid'];

		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.name) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, at.isocode as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a RIGHT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
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
		
		// Est-ce que la métadonnée est publiée?
		if ($rowMetadata->metadatastate_id == 3)
			$isValidated = true;
		else
			$isValidated = false;
			
		// Récupérer les périmètres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmètres prédéfinis 
		$rowAttributeType = new attributetype($database);
		$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		$type_isocode = $rowAttributeType->isocode;
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$metadata_guid;
		
		// En GET
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		/* Remplacer la valeur du noeud fileIdentifier par la valeur courante metadata_id*/
        $nodeList = &$cswResults->getElementsByTagName($idrow[0]->name);

        foreach ($nodeList as $node)
        {
        	// Remplacer la valeur de fileIdentifier par celle de metadata_id pour que
        	// la métadonnée importée prenne son nouvel id 
        	if ($node->parentNode->nodeName == $root[0]->ns.":".$root[0]->name)
        	{
        		foreach ($node->childNodes as $child)
        		{
        			if ($child->nodeName == $idrow[0]->list_isocode)
        			{
        				$child->nodeValue = $metadata_id;
        			}
        		}
        	}
        }
        
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
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist = array_merge( $namespacelist, $database->loadObjectList() );
		
		 foreach ($namespacelist as $namespace)
        {
        	$xpathResults->registerNamespace($namespace->prefix,$namespace->uri);
        } 
        
        // Parcourir les noeuds enfants de la classe racine.
		// - Pour chaque classe rencontrée, ouvrir un niveau de hiérarchie dans la treeview
		// - Pour chaque attribut rencontré, créer un champ de saisie du type rendertype de la relation entre la classe et l'attribut
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $option);
		
	}
	
	function getContact($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$searchPattern = $_POST['query'];
		$objecttype_id = $_POST['objecttype_id'];
		
		if (!$searchPattern)
			$searchPattern = "";
		// Récupérer tous les objets du type d'objet lié dont le nom comporte le searchPattern
		$results = array();
		$database->setQuery( "SELECT o.id as id, m.guid as guid, o.name as name FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m where o.metadata_id=m.id AND o.objecttype_id=ot.id AND ot.id=".$objecttype_id."  AND o.name LIKE '%".$searchPattern."%'" );
		$results= array_merge( $results, $database->loadObjectList() );
		
		// Construire le tableau de résultats
		$return = array ("total"=>count($results), "contacts"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
	}
	
	function getObject($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$dir = $_POST['dir'];
		$sort = $_POST['sort'];
		
		//get start and limit if present
		$start = (array_key_exists('start', $_REQUEST))? $_REQUEST["start"]: 0;
		$count = (array_key_exists('limit', $_REQUEST))? $_REQUEST["limit"]: 10;
			
		$objecttype_id = null;
		if (array_key_exists('objecttype_id', $_POST))
			$objecttype_id = $_POST['objecttype_id'];
		
		// Récupérer tous les objets du type d'objet lié dont le nom comporte le searchPattern
		$results = array();
		$query = "SELECT o.id as object_id, m.guid as metadata_guid, o.name as object_name FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.objecttype_id=ot.id AND ot.predefined=false";
		
		if ($objecttype_id)
			$query .= " AND ot.id=".$objecttype_id;
		
		//add sort direction if not empty
		if ($sort != "") 
			$queryFiltered = $query." ORDER BY ".$sort." ".$dir;

		//add start and limit
		$queryFiltered.= " LIMIT ".$start.",".$count;

		$database->setQuery($queryFiltered);
		//echo $database->getQuery();
		$results= array_merge( $results, $database->loadObjectList() );
		
		
		$database->setQuery($query);
		$total = count($database->loadObjectList());
		
		// Construire le tableau de résultats
		$return = array ("total"=>$total, "objects"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
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
	
	function sendMailByEmail($email,$subject,$body)
	{
		$mailer =& JFactory::getMailer();		
		$mailer->addBCC($email);																				
		$mailer->setSubject($subject);
		$user = JFactory::getUser();
		$mailer->setBody($body);

		if ($mailer->send() !==true){
			return false;
		}
		return true;		
	}
}
?>