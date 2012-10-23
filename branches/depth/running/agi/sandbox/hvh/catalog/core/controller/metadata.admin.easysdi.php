<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dé"Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
		
	/*
	 * Permet de choisir la version de l'objet dont il faut éditer la métadonnée
	 */
	function askForEditMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$uri =& JUri::getInstance();
		
		$objectversion = array();
		$listObjectversion = array();
		$database->setQuery( "SELECT id as value, title as text FROM #__sdi_objectversion WHERE object_id=".$id." ORDER BY name" );
		$objectversion= array_merge( $objectversion, $database->loadObjectList() );
		
		foreach($objectversion as $ov)
		{
			$listObjectversion[$ov->value] = $ov->text;
		}
		$listObjectversion = HTML_metadata::array2extjs($listObjectversion, false);
			
		if (count($objectversion) <= 1)
		{
			TOOLBAR_metadata::_EDIT();
			ADMIN_metadata::editMetadata($objectversion[0]->value,$option);
		}
		else
		{
			JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
			JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');
					
			$document =& JFactory::getDocument();
			$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
			
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
					         value: ''
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
			                    	selectEditWin.items.get(0).getForm().setValues({'cid[]': Ext.getCmp('version_id').getValue()});
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
			";
			
			print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
		}
	}
	
	/*
	 * Edition d'une métadonnée
	 */
	function editMetadata($id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		$task = JRequest::getVar('task');
		
		$language =& JFactory::getLanguage();
		if ($language->_lang == "fr-FR") 
			JHTML::script('ext-lang-fr.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else if ($language->_lang == "de-DE") 
			JHTML::script('ext-lang-de.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		
		if ($id == 0)
		{
			$msg = JText::_('CATALOG_OBJECT_SELECTMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg);
			exit;
		}
		
		// Récupérer l'objet
		$rowObjectVersion = new objectversion( $database );
		$rowObjectVersion->load( $id );
		
		$rowObject = new object( $database );
		$rowObject->load( $rowObjectVersion->object_id );
		
		// Récupérer la métadonnée choisie par l'utilisateur
		$rowMetadata = new metadata( $database );
		$rowMetadata->load( $rowObjectVersion->metadata_id );
		
		if ($rowMetadata->id == 0)
		{
			$msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
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
		$query = "SELECT c.name as name, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
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
			
		// Est-ce que cet utilisateur est un editeur?
		$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isEditor = false;
		if ($total == 1)
			$isEditor = true;
		
		// Est-ce que la métadonnée est publiée?
		$isPublished = false;
		if ($rowMetadata->metadatastate_id == 1)
			$isPublished = true;
			
		
		// Est-ce que la métadonnée est validée?
		$isValidated = false;
		if ($rowMetadata->metadatastate_id == 3)
			$isValidated = true;
			
		// Récupérer les périmétres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmétres prédéfinis 
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$rowMetadata->guid;
		
		$xmlBody= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n
			<csw:GetRecordById xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\"
			    outputSchema=\"csw:IsoRecord\">
			    <csw:Id>".$rowMetadata->guid."</csw:Id>
			</csw:GetRecordById>			
		";
		
		// Requéte de type GET pour le login (conserver le token response)
		// Stocker dans un cookie le résultat de la requéte précédente
		// Mettre le cookie dans l'en-téte de la requéte insert
		//$xmlResponse = ADMIN_metadata::PostXMLRequest($catalogUrlBase, $xmlBody);

		// En POST
		//$cswResults = DOMDocument::loadXML($xmlResponse);

		// En GET
		//$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		$cswResults = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		
		// Construction du DOMXPath à utiliser pour générer la vue d'édition
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		if ($cswResults <> false and $cswResults->childNodes->item(0)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else if ($cswResults->childNodes->item(0)->nodeName == "ows:ExceptionReport")
		{
			//$xpathResults = new DOMXPath($doc);
			$msg = $cswResults->childNodes->item(0)->nodeValue;
			switch ($task)
			{
				case "askForEditMetadata":
					$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
				case "editMetadata":
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$rowObject->id, $msg );
			}
		}
		else
		{
			$msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
			switch ($task)
			{
				case "askForEditMetadata":
					$mainframe->redirect("index.php?option=$option&task=listObject", $msg );
				case "editMetadata":
					$mainframe->redirect("index.php?option=$option&task=listObjectVersion&object_id=".$rowObject->id, $msg );
			}
		}
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
			}";
        }        
        else{
        	$defaultBBoxConfig = "";
        }
        
        
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option, $defaultBBoxConfig);
	}

	/*
	 * Construction d'un xml ISO1939, à partir du formulaire EXTJS 
	 */
	function buildXMLTree($parent, $parentFieldset, $parentName, &$XMLDoc, $xmlParent, $queryPath, $currentIsocode, $scope, $keyVals, $profile_id, $account_id, $option)
	{
		$database 			=& JFactory::getDBO();
		$language 			=& JFactory::getLanguage();
		$session 			=& JFactory::getSession();
		$rowChilds 			= array();
		$xmlClassParent 	= $xmlParent;
		$xmlAttributeParent = $xmlParent;
		$xmlObjectParent 	= $xmlParent;
		$rowChilds 			= array();
		
		$query = "SELECT rel.id as rel_id, 
						 rel.guid as rel_guid,
						 rel.name as rel_name, 
						 rel.upperbound as rel_upperbound, 
						 rel.lowerbound as rel_lowerbound, 
						 rel.attributechild_id as attribute_id, 
						 rel.rendertype_id as rendertype_id, 
						 rel.classchild_id as child_id, 
						 rel.objecttypechild_id as objecttype_id, 
						 rel.editable as editable,
						 CONCAT(relation_namespace.prefix,':',rel.isocode) as rel_isocode, 
						 rel.relationtype_id as reltype_id, 
						 rel.classassociation_id as association_id,
						 a.guid as attribute_guid,
						 a.name as attribute_name, 
						 CONCAT(attribute_namespace.prefix,':',a.isocode) as attribute_isocode, 
						 CONCAT(list_namespace.prefix,':',a.type_isocode) as list_isocode, 
						 a.attributetype_id as attribute_type,
						 tc.id as cl_stereotype_id, 
						 a.default as attribute_default, 
						 a.length as length,
						 a.codeList as codeList,
						 a.information as tip,
						 CONCAT(attributetype_namespace.prefix,':',t.isocode) as t_isocode, 
						 accountrel_attribute.account_id as attributeaccount_id,
						 c.name as child_name,
						 c.guid as class_guid, 
						 CONCAT(child_namespace.prefix,':',c.isocode) as child_isocode, 
						 accountrel_class.account_id as classaccount_id,
						 ot.fragment as fragment_isocode,
						 objecttype_namespace.prefix as fragment_namespace
				  FROM	 #__sdi_relation as rel 
						 JOIN #__sdi_relation_profile as prof
						 	 ON rel.id = prof.relation_id
						 LEFT OUTER JOIN #__sdi_attribute as a
				  		 	 ON rel.attributechild_id=a.id 
					     LEFT OUTER JOIN #__sdi_sys_stereotype as t
					  		 ON a.attributetype_id = t.id 
					     LEFT OUTER JOIN #__sdi_class as c
					  		 ON rel.classchild_id=c.id
					  	LEFT OUTER JOIN jos_sdi_sys_stereotype as tc
			 				ON c.stereotype_id = tc.id 
					     LEFT OUTER JOIN #__sdi_objecttype as ot
					  		 ON rel.objecttypechild_id=ot.id
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
					  	 LEFT OUTER JOIN #__sdi_namespace as attributetype_namespace
					  		 ON attributetype_namespace.id=t.namespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as objecttype_namespace
					  		 ON objecttype_namespace.id=ot.fragmentnamespace_id
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
				 GROUP BY rel.id ORDER BY rel.ordering, rel.id";		
		$database->setQuery( $query );
		$rowChilds = array_merge( $rowChilds, $database->loadObjectList() );
		
		foreach($rowChilds as $child)
		{
			// Traitement d'une relation vers un attribut
			if ($child->attribute_id <> null)
			{
				//Store Title into EasySDI database
				$titles = array ();
				
				if ($child->attribute_type == 6 )
					$type_isocode = $child->list_isocode;
				else
					$type_isocode = $child->t_isocode;
		
				$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
				$childType = $child->t_isocode;
				
				// Traitement de chaque attribut selon son type
				switch($child->attribute_type)
				{
					// Guid
					case 1:
						$name = $name."__1";
						if ($child->editable == 2)
							$name = $name."_hiddenVal";
						
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						$count=0;
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];

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
						$usefullVals=array();
						$count=0;
						if ($child->editable == 2)
							$name = $name."__1"."_hiddenVal";
							
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							$nodeValue = htmlspecialchars($nodeValue);
																	
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						
						break;
					// Local
					case 3:
						// Traitement spécifique aux langues 
						// On crée le nom spécifiquement pour les textes localisés
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode); 
	
						$count=0;
						foreach($keyVals as $key => $val)
						{
							if ($key == $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1")
							{
								$count = $val;
								break;
							}
						}
						$count = $count - 1;
						
						for ($pos=0; $pos<$count; $pos++)
						{
							$LocName = $name."__".($pos+2);
							
							// S'assurer que l'entrée a bien été retournée, et que ce n'est pas juste un effet de bord
							// du comptage, lié à des +/- successifs 
							$countExist=0;
							foreach($keyVals as $key => $val)
							{
								if ($key == $LocName)
								{
									$countExist++;
									break;
								}
							}
			
							if ($countExist == 1)
							{
								$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
								$xmlAttributeParent->appendChild($XMLNode);
								$xmlLocParent = $XMLNode;
							
								foreach($this->langList as $lang)
								{
									$LangName = $LocName."-gmd_LocalisedCharacterString-".$lang->code_easysdi."__1";
			
									// Récupération des valeurs postées correspondantes
									$keys = array_keys($_POST);
									$usefullVals=array();
									
									foreach($keys as $key)
									{
										$partToCompare = substr($key, 0, strlen($LangName));
										if ($partToCompare == $LangName)
										{
											if (substr($key, -6) <> "_index")
											{
												$nodeValue = $_POST[$key];
												break;
											}
										}
									}
										
									$nodeValue = htmlspecialchars($nodeValue);
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
										$xmlLocParent_temp = $XMLNode;
										$XMLNode = $XMLDoc->createElement("gmd:textGroup");
										$xmlLocParent_temp->appendChild($XMLNode);
										$xmlLocParent_temp = $XMLNode;
										// Ajout de la valeur
										$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
										$xmlLocParent_temp->appendChild($XMLNode);
										// Indication de la langue concernée
										$XMLNode->setAttribute('locale', "#".$lang->code);
									}
									
								}
							}
						}
						break;
					// Number
					case 4:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->editable == 2)
								$name = $name."__1"."_hiddenVal";
						
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							
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
						$count=0;
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							
							if ($nodeValue <> "")
								$nodeValue = date('Y-m-d', strtotime($nodeValue));
							
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
							
							$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
							$XMLNode->appendChild($XMLValueNode);
							$xmlParent = $XMLValueNode;
						}
						break;
					// List
					case 6:
						switch ($child->rendertype_id)
						{
							// Checkbox
							case 2:
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								$usefullVals=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								$nodeValues=$usefullVals;
								
								// Deux traitement pour deux types de listes
							 	if ($child->codeList <> "")
							 	{
								 	foreach($nodeValues as $val)
									{
										if ($val)
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
										if ($val)
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
							// Radiobutton
							case 3:
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								$usefullVals=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								$nodeValue = $usefullVals[0];
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
								// Traitement spécifique aux listes 
								// Récupération des valeurs postées correspondantes
								$keys = array_keys($_POST);
								$usefullVals=array();
								$count=0;
								foreach($keys as $key)
								{
									$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->attribute_isocode)));
									if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
									{
										if (substr($key, -6) <> "_index")
										{
											$count = $count+1;
											$usefullVals[] = $_POST[$key];
										}
									}
								}
								// Traitement des enfants de type list
								// Traitement de la multiplicité
							 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
							 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								if (count($usefullVals) > 0)
									$nodeValues=split(",",$usefullVals[0]);		
								else
									$nodeValues=array();
								
								// Deux traitement pour deux types de listes
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
						$count=0;
						foreach($keys as $key)
						{
							if ($child->editable == 2)
								$name = $name."__1"."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
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
						$count=0;
						foreach($keys as $key)
						{
							if ($child->editable == 2)
								$name = $name."__1"."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							
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
						$count=0;
						foreach($keys as $key)
						{
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
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
		
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
						
						$count=0;
						foreach($keys as $key)
						{
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
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
					
							// Récupérer la valeur liée à cette entrée de liste
							$locValues = array();
							$query = "SELECT cl.code, t.content FROM #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE t.language_id=l.id AND l.codelang_id=cl.id AND t.element_guid = '".$nodeValue."' ORDER BY l.ordering";
							$database->setQuery( $query );
							$locValues = $database->loadObjectList();
							
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
										$nodeValue=$loc->content;
										$codeToSave = $lang->code;
										if ($lang->defaultlang== true)
										{
											$isdefault = true;
										}
										break;
									}
								}
								
								if ($isdefault) // La langue par défaut
								{
									$XMLNode = $XMLDoc->createElement("gco:CharacterString", $nodeValue);
									$xmlLocParent->appendChild($XMLNode);
								}
								else // Les autres langues
								{
									$XMLNode = $XMLDoc->createElement("gmd:PT_FreeText");
									$xmlLocParent->appendChild($XMLNode);
									$xmlLocParent_temp = $XMLNode;
									$XMLNode = $XMLDoc->createElement("gmd:textGroup");
									$xmlLocParent_temp->appendChild($XMLNode);
									$xmlLocParent_temp = $XMLNode;
									// Ajout de la valeur
									$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
									$xmlLocParent_temp->appendChild($XMLNode);
									// Indication de la langue concernée
									$XMLNode->setAttribute('locale', "#".$codeToSave);
								}
							}
						}
						break;
					// Thesaurus GEMET
					case 11:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						$count=0;
						foreach($keys as $key)
						{
							$partToCompare_temp1 = substr($key, strlen($parentName."-"));
							if (strpos($partToCompare_temp1, "-"))
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "-"));
							else
								$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "__"));
							
							$partToCompare_temp3 = substr($key, 0, strlen($parentName."-"));
							$partToCompare  = $partToCompare_temp3.$partToCompare_temp2;
								
							if ($partToCompare == $parentName."-".str_replace(":", "_", $child->attribute_isocode))
							{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
							}
						}
						
						// Construire les blocs de mots-clés pour chaque mot-clé à sauver 
						foreach ($usefullVals as $usefullVal)
						{
							foreach ($usefullVal as $toUse)
							{
								if ($toUse <> "")
								{
									// Récupération des différentes traductions
									$vals=array();
									$langVals=array();
									$vals = explode(";", $toUse);
									
									foreach ($vals as $val)
									{
										if ($val <> "")
										{
											$keyval = explode(": ", $val);
											$langVals[$keyval[0]] = $keyval[1];
										}
									}
									
									// Balise principale du mot-clé, qui va contenir les différentes traductions
									$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
									$xmlAttributeParent->appendChild($XMLNode);
									$xmlLocParent = $XMLNode;
									
									// Insertion de chaque traduction	
									foreach($this->langList as $loc)
									{
										$nodeValue = $langVals[$loc->gemetlang];
																				
										// Ajout des balises inhérantes aux locales
										if ($loc->defaultlang) // La langue par défaut
										{
											$XMLNode = $XMLDoc->createElement("gco:CharacterString", $nodeValue);
											$xmlLocParent->appendChild($XMLNode);
										}
										else // Les autres langues
										{
											$XMLNode = $XMLDoc->createElement("gmd:PT_FreeText");
											$xmlLocParent->appendChild($XMLNode);
											$xmlLocParent_temp = $XMLNode;
											$XMLNode = $XMLDoc->createElement("gmd:textGroup");
											$xmlLocParent_temp->appendChild($XMLNode);
											$xmlLocParent_temp = $XMLNode;
											// Ajout de la valeur
											$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
											$xmlLocParent_temp->appendChild($XMLNode);
											// Indication de la langue concernée
											$XMLNode->setAttribute('locale', "#".$loc->code);
										}
									}
								}
							}
						}
						
						break;
					case 14 :					
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						$count=0;
						if ($child->editable == 2)
							$name = $name."__1"."_hiddenVal";
						
						foreach($keys as $key)
						{
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
							
							if(strlen($nodeValue) == 0){
								continue;
							}
							$XMLNode = $XMLDoc->createElement($child->attribute_isocode);
							$xmlAttributeParent->appendChild($XMLNode);
								
							$XMLNode1 = $XMLDoc->createElement("gmd:MI_Identifier");
							$XMLNode2 = $XMLDoc->createElement("gmd:code");
							$XMLNode3 = $XMLDoc->createElement("gco:CharacterString",$nodeValue);
							
							$XMLNode->appendChild($XMLNode1);
							$XMLNode1->appendChild($XMLNode2);
							$XMLNode2->appendChild($XMLNode3);
							
							$xmlParent = $XMLNode;
						}
						
						break;
					default:
						// Récupération des valeurs postées correspondantes
						$keys = array_keys($_POST);
						$usefullVals=array();
						$count=0;
						foreach($keys as $key)
						{
							if ($child->editable == 2)
								$name = $name."__1"."_hiddenVal";
							
							$partToCompare = substr($key, 0, strlen($name));
							if ($partToCompare == $name)
							{
								if (substr($key, -6) <> "_index")
								{
									$count = $count+1;
									$usefullVals[] = $_POST[$key];
								}
							}
						}
						
						// Ajouter chacune des copies du champ dans le XML résultat
						for ($pos=1; $pos<=$count; $pos++)
						{
							$nodeValue = $usefullVals[$pos-1];
									
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
				$count=0;
				
				foreach($keyVals as $key => $val){
					if ($key == $parentName."-".str_replace(":", "_", $child->child_isocode)."__1"){
						$count = $val;
						break;
					}
				}
				$count = $count - 1;
				
				for ($pos=0; $pos<$count; $pos++){
					//$name est le nom du fieldset
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+2);
					
					// Structure à créer ou pas
					$keys = array_keys($_POST);
					$existVal=false;
					foreach($keys as $key){
						$partToCompare = substr($key, 0, strlen($name));
						if ($partToCompare == $name){
							$existVal = true;
							break;
						}
					}

					if ($existVal){
						if ($child->cl_stereotype_id <> null){
							//Tester le stereotype pour sauver la bonne structure
							$database->setQuery("SELECT alias FROM #__sdi_sys_stereotype WHERE id =".$child->cl_stereotype_id);
							$stereotype = $database->loadResult();
							switch ($stereotype){
								case "geographicextent":
									//On ne créer pas les elements du XML correspondant à la relation et la classe enfant ici
									//Ils sont gérés dans le stereotype -> cas particulier dû à la gestion de la multiplicité du stereotype geographicextent
									ADMIN_classstereotype_saver::saveGeographicExtentClass($database, $child, $XMLDoc, $xmlClassParent, $name);
							}
						}else{
							// Création du noeud de La relation
							if ($child->rel_isocode <> ""){
								$XMLNode = $XMLDoc->createElement($child->rel_isocode);
								$xmlClassParent->appendChild($XMLNode);
								// On conserve dans une variable intermédiaire la classe parent
								$xmlOldClassParent = $xmlClassParent;
								$xmlClassParent = $XMLNode;
							}
							// Création du noeud de La classe enfant
							$XMLNode = $XMLDoc->createElement($child->child_isocode);
							$xmlClassParent->appendChild($XMLNode);
							$xmlParent = $XMLNode;
							// On récupére la vraie classe parent, au cas où elle aurait été changée
							$xmlClassParent = $xmlOldClassParent;
							// Récupération des codes ISO et appel récursif de la fonction
							$nextIsocode = $child->child_isocode;
							ADMIN_metadata::buildXMLTree($child->child_id, $child->child_id, $name, $XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
						}
					}
					
					// Classassociation_id contient une classe
					if ($child->association_id <>0){
						// Appel récursif de la fonction pour le traitement du prochain niveau
						ADMIN_metadata::buildXMLTree($child->association_id, $child->child_id, $name, $XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
					}
				}
			}
			else if ($child->objecttype_id <> null){
				$name = $parentName."-".str_replace(":", "_", $child->rel_isocode);
				
				// Récupération des valeurs postées correspondantes
				$keys = array_keys($_POST);
				$usefullVals=array();
				
				$count=0;
				foreach($keys as $key)
				{
					$partToCompare_temp1 = substr($key, strlen($parentName."-"));
					
					if (strpos($partToCompare_temp1, "-"))
						$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "-"));
					else
						$partToCompare_temp2 = substr($partToCompare_temp1, 0, strpos($partToCompare_temp1, "__"));
					
					$partToCompare_temp3 = substr($key, 0, strlen($parentName."-"));
					$partToCompare  = substr($partToCompare_temp3.$partToCompare_temp2, 0, strlen($partToCompare_temp3.$partToCompare_temp2)-3);

					if ($partToCompare == $parentName."-".str_replace(":", "_", $child->rel_isocode))
					{
						if (substr($key, -6) <> "_index")
						{
							$count = $count+1;
							$usefullVals[] = $_POST[$key];
						}
					}
				}
				
				for ($pos=1; $pos<=$count; $pos++)
				{
					$searchName = $name."__".($pos+1)."-SEARCH__1"; 
					// Récupération des valeurs postées correspondantes
					$keys = array_keys($_POST);
					$usefullVals=array();
					$searchCount=0;
					
					foreach($keys as $key)
					{
						$partToCompare = substr($key, 0, strlen($searchName));
						if ($partToCompare == $searchName)
						{
							if (substr($key, -6) <> "_index")
							{
								$searchCount = $searchCount+1;
								$usefullVals[] = $_POST[$key];
							}
						}
					}
					if ($searchCount > 0)	
					{
						$nodeValue = $usefullVals[0];
						
						if (strlen($nodeValue) <> 36)
							continue;
						require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
						$catalogUrlBase = config_easysdi::getValue("catalog_url");
						if ($child->fragment_isocode <> "" and $child->fragment_namespace <> "")
							$url = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$nodeValue."&fragment=".$child->fragment_namespace."%3A".$child->fragment_isocode;
						else
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
						$associationName = $name."__".($pos+1);
						// Appel récursif de la fonction pour le traitement du prochain niveau
						ADMIN_metadata::buildXMLTree($child->association_id, $child->objecttype_id, $associationName, $XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $profile_id, $account_id, $option);
					}
				}
			}
		}
	}
	
	/*
	 * 
	 */
	function getCurrentMetadataContent ($metadata_id)
	{
		$database 		=& JFactory::getDBO();
		$option 		= $_POST['option'];
		$metadata_id 	= $_POST['metadata_id'];
		$object_id 		= $_POST['object_id'];
		
		// Remise à jour des compteurs de suppression et d'ajout
		$deleted=0;
		$inserted=0;
		
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
		$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi,l.gemetlang FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		$database->setQuery( "SELECT c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
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
		$rowObject->load($object_id);
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix,':',c.isocode) as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObject();
		
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		
		// Récupération des namespaces à inclure
		$namespacelist = array();
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
		
		try
		{
			ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
			return $XMLDoc;
		}
		catch (Exception $e)
		{
			$response = '{
				success: false,
				errors: {
					xml: "Exception: '.$e->getMessage().'"
				}
			}';
			print_r($response);
			die();
		
		}
	}
	/*
	 * Sauvegarde d'une métadonnée 
	 */
	function saveMetadata($option)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$option 		= $_POST['option'];
		$metadata_id 	= $_POST['metadata_id'];
		$object_id 		= $_POST['object_id'];
		$XMLDoc 		= ADMIN_metadata::getCurrentMetadataContent();
		$updated 		= ADMIN_metadata::CURLUpdateMetadata($metadata_id, $XMLDoc );
		
		if ($updated <> 1)
		{
			$errorMsg = "erreur"; 
			$response = '{
				    		success: false,
						    errors: {
						        xml: "Metadata has not been inserted. '.$errorMsg.'"
						    }
						}';
			print_r($response);
			die();
		}
		
			
		// Mettre à jour la métadonnée liée (state et revision)
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		if (!$rowMetadata->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			exit();
		}
			
		//Update metadata titles (multilingual)
		$XMLstring 		= $XMLDoc->saveXML();
		$doc 			= DOMDocument::loadXML($XMLstring);
		$xpath 			= new DOMXpath($doc);
		// Recuperation des namespaces e inclure
		$namespacelist 	= array();
		$database->setQuery( "SELECT prefix, uri FROM #__sdi_namespace ORDER BY prefix" );
		$namespacelist 	= array_merge( $namespacelist, $database->loadObjectList() );
		$gmdURI = "";
		$gcoURI = "";
		foreach ($namespacelist as $namespace)
		{
			$xpath->registerNamespace($namespace->prefix,$namespace->uri);
			if($namespace->prefix == "gmd") $gmdURI = $namespace->uri;
			if($namespace->prefix == "gco") $gcoURI = $namespace->uri;
		}
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$queryXPath = config_easysdi::getValue("CATALOG_METADATA_TITLE_XPATH");
		$elements = $xpath->query($queryXPath);
		
		if (!is_null($elements)) {
			$user 					= JFactory::getUser();
			$element 				= $elements->item(0);
			$CharacterString_EL 	= $element->getElementsByTagNameNS($gcoURI,"CharacterString");
			$PT_FreeText_EL 		= $element->getElementsByTagNameNS($gmdURI,"PT_FreeText");
				
			if($CharacterString_EL->length == 1 && $PT_FreeText_EL->length == 0)//Not a multilingual field : the unique value must be save for all the languages
			{
				$nodeValue = $CharacterString_EL->item(0)->nodeValue;
				foreach($this->langList as $lang)
				{
					$database->setQuery ("SELECT COUNT(*) FROM #__sdi_translation WHERE element_guid ='".$rowMetadata->guid."' AND language_id=".$lang->id);
					$count = $database->loadResult();
					if($count > 0){
						$query = "UPDATE #__sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user->id."
						WHERE element_guid = '".$rowMetadata->guid."' AND language_id=".$lang->id;
					}else{
						$query = "INSERT INTO #__sdi_translation (element_guid, language_id, title, created, createdby)
						VALUES ('".$rowMetadata->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user->id." )";
					}
					$database->setQuery($query);
					if (!$database->query()){
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					}
				}
			}
				
			else if($PT_FreeText_EL->length > 0)//Multilingual field
			{
				$nodeValue = $CharacterString_EL->item(0)->nodeValue;
				foreach($this->langList as $lang)
				{
					if($lang->defaultlang)
					{
						$database->setQuery ("SELECT COUNT(*) FROM #__sdi_translation WHERE element_guid ='".$rowMetadata->guid."' AND language_id=".$lang->id);
						$count = $database->loadResult();
						if($count > 0){
							$query = "UPDATE #__sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user->id."
							WHERE element_guid = '".$rowMetadata->guid."' AND language_id=".$lang->id;
						}else{
							$query = "INSERT INTO #__sdi_translation (element_guid, language_id, title, created, createdby)
							VALUES ('".$rowMetadata->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user->id." )";
						}
						$database->setQuery($query);
						if (!$database->query()){
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						}
						break;
					}
				}
				
				for ($i = 0 ; $i < $PT_FreeText_EL->length ; $i++ )
				{
					$freeTextNode = $PT_FreeText_EL->item($i);
 					$textGroupNodes = $freeTextNode->getElementsByTagNameNS($gmdURI,"textGroup");
 					$textGroupNode = $textGroupNodes->item(0);
 					$LocalisedCharacterStringNodes = $textGroupNode->getElementsByTagNameNS($gmdURI,"LocalisedCharacterString");
 					$LocalisedCharacterStringNode = $LocalisedCharacterStringNodes->item(0);
 					$attributes = $LocalisedCharacterStringNode->attributes;
 					$locale = $attributes->getNamedItem("locale");
 					$nodeValue = $LocalisedCharacterStringNode->nodeValue;
 					foreach($this->langList as $lang)
 					{
 						if("#".$lang->code == $locale->nodeValue)
 						{
 							$database->setQuery ("SELECT COUNT(*) FROM #__sdi_translation WHERE element_guid ='".$rowMetadata->guid."' AND language_id=".$lang->id);
 							$count = $database->loadResult();
 							if($count > 0){
 								$query = "UPDATE #__sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user->id."
 											WHERE element_guid = '".$rowMetadata->guid."' AND language_id=".$lang->id;
 							}else{
 								$query = "INSERT INTO #__sdi_translation (element_guid, language_id, title, created, createdby)
 											VALUES ('".$rowMetadata->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user->id." )";
 							}
 							$database->setQuery($query);
 							if (!$database->query()){
								$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
 							}
 							break;
 						}
 					}
 					
				}
			}
		}
		else
		{
			//Auncun titre n'est défini dans la métadonnée, effacer si présent
			$query = "DELETE FROM  #__sdi_translation WHERE element_guid = '".$rowMetadata->guid."' ";
			$database->setQuery($query);
			if (!$database->query()){
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
		}
		// Checkin object
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		$rowObject->checkin();
			
		if ($_POST['task'] == 'saveMetadata')
		{
			$response =
			'{
				success: true,
				errors: {
					xml: "Metadata saved"
				}
			}';
			print_r($response);
			die();
		}
	}
	
	/*
	 * Quitter l'édition d'une métadonnée
	 */
	function cancelMetadata($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		
		// Check the attribute in if checked out
		$rowObject = new object( $database );
		if (isset($_POST['object_id'])){
			$rowObject->load( $_POST['object_id'] );
			$rowObject->checkin();
		}
	}
	
	/**
	 * 
	 * @param string $previewType : value 'XML' or 'MD'
	 */
	function preview($previewType)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$option 		= $_POST['option'];
		$metadata_id 	= $_POST['metadata_id'];
		$object_id 		= $_POST['object_id'];
		
		$XMLDoc = ADMIN_metadata::getCurrentMetadataContent();
		if ($XMLDoc)
		{
			if($previewType == 'XML'){
				ADMIN_metadata::previewXMLMetadata($XMLDoc);
			}
			else if ($previewType == 'MD'){
				ADMIN_metadata::previewMetadata ($XMLDoc, $database, $metadata_id);
			}
		}
		else
		{
			$response = '{
			success: false,
			errors: {
			xml: "A problem occurred"
		}
		}';
			print_r($response);
			die();
		}
	}
	
	/**
	 * Prévisualiser le rendu HTML de la métadonnée qui pourrait être construite à partir du formulaire EXTJS
	 */
	function previewMetadata ($XMLDoc, $database, $metadata_id)
	{
		$language 	=& JFactory::getLanguage();
		$xslFolder 	= "";
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$type = config_easysdi::getValue("CATALOG_METADATA_PREVIEW_TYPE_PUBLIC");
		$context = config_easysdi::getValue("CATALOG_METADATA_PREVIEW_CONTEXT_PUBLIC");
		
		if (isset($context)){
			$database->setQuery("SELECT xsldirectory FROM #__sdi_context WHERE code='".$context."'");
			$xslFolder = $database->loadResult();
		}
		if ($xslFolder <> "")
			$xslFolder = $xslFolder."/";
		
		// Récupérer le type d'objet
		$database->setQuery("SELECT ot.code
				FROM #__sdi_metadata m
				INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				INNER JOIN #__sdi_object o ON o.id = ov.object_id
				INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id
				WHERE m.guid='".$metadata_id."'");
		$objecttype = $database->loadResult();
		
		$XMLDoc->loadXML($XMLDoc->saveXML());
		$xmlcomplete = displayManager::constructXML($XMLDoc, $database, $language, $metadata_id, 'false', $type, $context);
		
		$style = new DomDocument();
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$objecttype.'_'.$type.'_'.$language->_lang.'.xsl'))
		{
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$objecttype.'_'.$type.'_'.$language->_lang.'.xsl');
		}
		else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$objecttype.'_'.$type.'.xsl'))
		{
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XHTML_'.$objecttype.'_'.$type.'.xsl');
		}
		else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$type.'_'.$language->_lang.'.xsl'))
		{
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$type.'_'.$language->_lang.'.xsl');
		}
		else
		{
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_'.$type.'.xsl');
		}
		$processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$xml = new DomDocument();
		$xml = $processor->transformToXml($xmlcomplete);
		$xmlToReturn = addslashes($xml);
		
		$response = '{
						success: true,
						file: {
							xml: "'.str_replace(chr(10), "<br>", $xmlToReturn).'"
						}
					}';
		print_r($response);
		die();
	}
	
	/**
	 * Prévisualiser le XML ISO19139 qui pourrait être construit à partir du formulaire EXTJS
	 */
	function previewXMLMetadata($XMLDoc)
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
	
	function validateMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$metadata_id = $_POST['metadata_id'];
		$account_id = $_POST['account_id'];
		
		ADMIN_metadata::saveMetadata($option);
		
		// Passer en statut validé
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->metadatastate_id=3;
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		
		if (!$rowMetadata->store()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
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
	
	function updateMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$metadata_id = $_POST['metadata_id'];
		$account_id = $_POST['account_id'];
		
		ADMIN_metadata::saveMetadata($option);
		
		// Mettre à jour la date
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		$rowMetadata->updated = date('Y-m-d H:i:s');
		$rowMetadata->updatedby = $account_id;
		
		if (!$rowMetadata->store()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			exit();
		}
		
		$response = '{
			    		success: true,
					    errors: {
					        xml: "Metadata updated"
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
		$rowMetadata->editor_id = null;
		$rowMetadata->published = null;
		
		print_r($rowMetadata);echo "<hr>";
		if (!$rowMetadata->store(true)) 
		{
			echo $database->getErrorMsg()."<br>";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
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
		$object_id = $_POST['object_id'];
		
		// Remise à jour des compteurs de suppression et d'ajout 
		$deleted=0;
		$inserted=0;
		//echo "Metadata: ".$metadata_id." \r\n ";
		//echo "Object: ".$object_id." \r\n ";
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
		//$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
		$database->setQuery( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi,l.gemetlang FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		$database->setQuery( "SELECT c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
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
		$rowObject->load($object_id);
		
		// Récupérer la classe racine du profile du type d'objet
		$query = "SELECT c.name as name, CONCAT(ns.prefix,':',c.isocode) as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
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
		
		try
		{
			ADMIN_metadata::buildXMLTree($root->id, $root->id, str_replace(":", "_", $root->isocode), $XMLDoc, $XMLNode, $path, $root->isocode, $_POST, $keyVals, $profile_id, $account_id, $option);
			
			if (!$XMLDoc)
			{
				$errorMsg = "XML non construit";
				$response = '{
					    		success: false,
							    errors: {
							        xml: "Metadata cannot be published. Bad construction '.$errorMsg.'"
							    }
							}';
				print_r($response);
				die();
			}
						
			/*
			 * Contréles des bornes entre les types d'objet enfants / parents
			 */ 
			$missingChild = array();
			$overloadChild = array();
			$missingParent = array();
			$overloadParent = array();
			// Récupération de la version de l'objet qu'on cherche à publier
			$rowMetadata = new metadataByGuid($database);
			$rowMetadata->load($metadata_id);
			
			$rowObjectVersion = new objectversionByMetadata_id($database);
			$rowObjectVersion->load($rowMetadata->id);
	
			// Récupérer tous les liens entre les types d'objets dont la version est le parent 
			$child_objecttypelinks=array();
			$query = 'SELECT l.*' .
					' FROM #__sdi_objecttypelink l
					  WHERE l.parent_id=' . $rowObject->objecttype_id;
			$database->setQuery($query);
			$child_objecttypelinks = $database->loadObjectList();
			// Pour chaque lien, récupérer les bornes des enfants
			foreach ($child_objecttypelinks as $child_objecttypelink)
			{
				$boundMin = $child_objecttypelink->childbound_lower;
				$boundMax = $child_objecttypelink->childbound_upper;
				  
				// Si la borne min est supérieure à 0, compter le nombre d'enfants de ce type d'objet
				// et contréler qu'il soit supérieur ou égal à la borne min
				if ($boundMin > 0)
				{
					$childs_count=0;
					$query = "SELECT count(*) as child_count
							 FROM #__sdi_objectversionlink ovl
							 INNER JOIN #__sdi_objectversion ov_child ON ov_child.id = ovl.child_id
							 INNER JOIN #__sdi_object o_child ON ov_child.object_id = o_child.id
							 WHERE ovl.parent_id=".$rowObjectVersion->id."
							 	   AND o_child.objecttype_id=".$child_objecttypelink->child_id;
					$database->setQuery($query);
					$childs_count = $database->loadResult();
					if ($childs_count < $boundMin)
					{
						// Sauvegarder le type d'objet manquant pour la construction future du message d'erreur
						$missingChild[] = $child_objecttypelink;
					}
				} 
				// Si la borne max est inférieure à 999, compter le nombre d'enfants de ce type d'objet
				// et contréler qu'il soit inférieur ou égal à la borne max
				if ($boundMax < 999)
				{
					$childs_count=0;
					$query = "SELECT count(*) as child_count
							 FROM #__sdi_objectversionlink ovl
							 INNER JOIN #__sdi_objectversion ov_child ON ov_child.id = ovl.child_id
							 INNER JOIN #__sdi_object o_child ON ov_child.object_id = o_child.id
							 WHERE ovl.parent_id=".$rowObjectVersion->id."
							 	   AND o_child.objecttype_id=".$child_objecttypelink->child_id;
					$database->setQuery($query);
					$childs_count = $database->loadResult();
					if ($childs_count > $boundMax)
					{
						// Sauvegarder le type d'objet manquant pour la construction future du message d'erreur
						$overloadChild[] = $child_objecttypelink;
					}
				} 
			}
			// Récupérer tous les liens entre les types d'objets dont la version est l'enfant 
			$parent_objecttypelinks=array();
			$query = 'SELECT l.*' .
					' FROM #__sdi_objecttypelink l
					  WHERE l.child_id=' . $rowObject->objecttype_id;
			$database->setQuery($query);
			$parent_objecttypelinks = $database->loadObjectList();
			// Pour chaque lien, récupérer les bornes des parents
			foreach ($parent_objecttypelinks as $parent_objecttypelink)
			{
				$boundMin = $parent_objecttypelink->parentbound_lower;
				$boundMax = $parent_objecttypelink->parentbound_upper;
				  
				// Si la borne min est supérieure à 0, compter le nombre de parents de ce type d'objet
				// et contréler qu'il soit supérieur ou égal à la borne min
				if ($boundMin > 0)
				{
					$parents_count=0;
					$query = "SELECT count(*) as parent_count
							 FROM #__sdi_objectversionlink ovl
							 INNER JOIN #__sdi_objectversion ov_parent ON ov_parent.id = ovl.parent_id
							 INNER JOIN #__sdi_object o_parent ON ov_parent.object_id = o_parent.id
							 WHERE ovl.child_id=".$rowObjectVersion->id."
							 	   AND o_parent.objecttype_id=".$parent_objecttypelink->parent_id;
					$database->setQuery($query);
					$parents_count = $database->loadResult();
					if ($parents_count < $boundMin)
					{
						// Sauvegarder le type d'objet manquant pour la construction future du message d'erreur
						$missingParent[] = $parent_objecttypelink;
					}
				} 
				// Si la borne max est inférieure à 999, compter le nombre de parents de ce type d'objet
				// et contréler qu'il soit inférieur ou égal à la borne max
				if ($boundMax < 999)
				{
					$parents_count=0;
					$query = "SELECT count(*) as parent_count
							 FROM #__sdi_objectversionlink ovl
							 INNER JOIN #__sdi_objectversion ov_parent ON ov_parent.id = ovl.parent_id
							 INNER JOIN #__sdi_object o_parent ON ov_parent.object_id = o_parent.id
							 WHERE ovl.child_id=".$rowObjectVersion->id."
							 	   AND o_parent.objecttype_id=".$parent_objecttypelink->parent_id;
					$database->setQuery($query);
					$parents_count = $database->loadResult();
					if ($parents_count > $boundMax)
					{
						// Sauvegarder le type d'objet manquant pour la construction future du message d'erreur
						$overloadParent[] = $parent_objecttypelink;
					}
				}
			}
			
			// Construction du message d'erreur, s'il y a lieu
			$language =& JFactory::getLanguage();
			$msg="";
			if (count($missingChild) > 0)
				$msg .= html_Metadata::cleanText(JText::_("CATALOG_METADATA_VALIDATEFORPUBLISH_MINCHILDBOUNDSREACHED"));
			foreach ($missingChild as $mc)
			{
				//$rowObjectType = new objecttype( $database );
				//$rowObjectType->load($mc->child_id);
				$rowObjectType=array();
				$query = "SELECT ot.*, t.label as ot_label
						  FROM #__sdi_objecttype ot
						  INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
						  INNER JOIN jos_sdi_language l ON t.language_id=l.id
						  INNER JOIN jos_sdi_list_codelang cl ON l.codelang_id=cl.id
						  WHERE ot.id = ".$mc->child_id."
							    AND cl.code = '".$language->_lang."'";
				$database->setQuery($query);
				$rowObjectType = $database->loadObject();
					
				$msg .= "<br>>".$rowObjectType->ot_label." [".$mc->childbound_lower."]";
				//$msg .= "<br>>".$rowObjectType->ot_label;
			}
			if (count($overloadChild) > 0)
				$msg .= html_Metadata::cleanText(JText::_("CATALOG_METADATA_VALIDATEFORPUBLISH_MAXCHILDBOUNDSREACHED"));
			foreach ($overloadChild as $oc)
			{
				//$rowObjectType = new objecttype( $database );
				//$rowObjectType->load($oc->child_id);
				$rowObjectType=array();
				$query = "SELECT ot.*, t.label as ot_label
						  FROM #__sdi_objecttype ot
						  INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
						  INNER JOIN jos_sdi_language l ON t.language_id=l.id
						  INNER JOIN jos_sdi_list_codelang cl ON l.codelang_id=cl.id
						  WHERE ot.id = ".$oc->child_id."
							    AND cl.code = '".$language->_lang."'";
				$database->setQuery($query);
				$rowObjectType = $database->loadObject();
		
				$msg .= "<br>>".$rowObjectType->ot_label." [".$oc->childbound_upper."]";
				//$msg .= "<br>>".$rowObjectType->ot_label;
			}
			if (count($missingParent) > 0)
				$msg .= html_Metadata::cleanText(JText::_("CATALOG_METADATA_VALIDATEFORPUBLISH_MINPARENTBOUNDSREACHED"));
			foreach ($missingParent as $mp)
			{
				//$rowObjectType = new objecttype( $database );
				//$rowObjectType->load($mp->parent_id);
				$rowObjectType=array();
				$query = "SELECT ot.*, t.label as ot_label
						  FROM #__sdi_objecttype ot
						  INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
						  INNER JOIN jos_sdi_language l ON t.language_id=l.id
						  INNER JOIN jos_sdi_list_codelang cl ON l.codelang_id=cl.id
						  WHERE ot.id = ".$mp->child_id."
							    AND cl.code = '".$language->_lang."'";
				$database->setQuery($query);
				$rowObjectType = $database->loadObject();
		
				$msg .= "<br>>".$rowObjectType->ot_label." [".$mp->parentbound_lower."]";
				//$msg .= "<br>>".$rowObjectType->ot_label;
			}
			if (count($overloadParent) > 0)
				$msg .= html_Metadata::cleanText(JText::_("CATALOG_METADATA_VALIDATEFORPUBLISH_MAXPARENTBOUNDSREACHED"));
			foreach ($overloadParent as $op)
			{
				//$rowObjectType = new objecttype( $database );
				//$rowObjectType->load($op->parent_id);
				$rowObjectType=array();
				$query = "SELECT ot.*, t.label as ot_label
						  FROM #__sdi_objecttype ot
						  INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
						  INNER JOIN jos_sdi_language l ON t.language_id=l.id
						  INNER JOIN jos_sdi_list_codelang cl ON l.codelang_id=cl.id
						  WHERE ot.id = ".$op->child_id."
							    AND cl.code = '".$language->_lang."'";
				$database->setQuery($query);
				$rowObjectType = $database->loadObject();
		
				$msg .= "<br>>".$rowObjectType->ot_label." [".$op->parentbound_upper."]";
				//$msg .= "<br>>".$rowObjectType->ot_label;
			}
			//echo $msg."<hr>";
			
			//break;
			
			if ($msg <> "")
			{
				$response = '{
							success: false,
						    errors: {
						        message: "'.$msg.'"
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
		catch (Exception $e) 
		{
			$response = '{
							success: false,
						    errors: {
						        message: "Exception: '.$e->getMessage().'"
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
		$archivelast = $_POST['archivelast'];
		
		$object_id= $_POST['object_id'];
		$account_id= $_POST['account_id'];
		
		// Est-ce que tous les enfants sont publiés?
		$rowObject = new object($database);
		$rowObject->load($object_id);
		
		$rowMetadata = new metadataByGuid($database);
		$rowMetadata->load($metadata_id);
		
		$rowObjectVersion = new objectversionByMetadata_id($database);
		$rowObjectVersion->load($rowMetadata->id);

		// Est-ce que tous les enfants sont publiés?
		$child_objectlinks=array();
		$query = 'SELECT child.*, child_metadatastate.code as state, child_object.name as object_name' .
				' FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
				  INNER JOIN #__sdi_object child_object ON child_object.id=child.object_id
				  INNER JOIN #__sdi_metadata child_metadata ON child_metadata.id=child.metadata_id
				  INNER JOIN #__sdi_list_metadatastate child_metadatastate ON child_metadatastate.id=child_metadata.metadatastate_id
				  WHERE l.parent_id=' . $rowObjectVersion->id .
				'       AND child_metadatastate.code <> "published"';
		$database->setQuery($query);
		$child_objectlinks = $database->loadObjectList();
		//	echo $database->getQuery()."<br>".$child_objectlinks."<br>";
		
		if (count($child_objectlinks) > 0)
		{
			$msg = html_Metadata::cleanText(JText::_("CATALOG_METADATA_PUBLISH_CHILDSNOTPUBLISHED"));
			foreach ($child_objectlinks as $child)
			{
				$msg .= "<br>".$child->object_name." ".$child->title." [".$child->state."]";
			}
			
			$response = '{
				    		success: false,
						    errors: {
						        message: "'.$msg.'"
						    }
						}';
			print_r($response);
			die();
		}
		
		// Est-ce que tous les enfants sont publiés à la date indiquée?
		$child_published=array();
		$query = 'SELECT child.*, child_metadata.published as published, child_object.name as object_name' .
				' FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
				  INNER JOIN #__sdi_object child_object ON child_object.id=child.object_id
				  INNER JOIN #__sdi_metadata child_metadata ON child_metadata.id=child.metadata_id
				  INNER JOIN #__sdi_list_metadatastate child_metadatastate ON child_metadatastate.id=child_metadata.metadatastate_id
				  WHERE l.parent_id=' . $rowObjectVersion->id .
				'       AND child_metadatastate.code = "published"
						AND child_metadata.published > "'.date('Y-m-d', strtotime($publishdate)).'"';
		$database->setQuery($query);
		$child_published = $database->loadObjectList();
		//echo $database->getQuery()."<br>".$child_published."<br>";
		
		if (count($child_published) > 0)
		{
			$msg = html_Metadata::cleanText(JText::_("CATALOG_METADATA_PUBLISH_CHILDSDATE"));
			foreach ($child_published as $child)
			{
				$msg .= "<br>".$child->object_name." ".$child->title." [".$child->published."]";
			}
			
			$response = '{
				    		success: false,
						    errors: {
						        message: "'.$msg.'"
						    }
						}';
			print_r($response);
			die();
		}
		
		// Est-ce que tous les enfants ont le méme metadatastate que le parent ?
		$child_metadatastates=array();
		$query = 'SELECT child.*, child_object.name as object_name, child_visibility.code as child_state, parent_visibility.code as parent_state' .
				' FROM #__sdi_objectversionlink l
				  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
				  INNER JOIN #__sdi_object child_object ON child_object.id=child.object_id
				  INNER JOIN #__sdi_list_visibility child_visibility ON child_visibility.id=child_object.visibility_id
				  INNER JOIN #__sdi_objectversion parent ON parent.id=l.parent_id
				  INNER JOIN #__sdi_object parent_object ON parent_object.id=parent.object_id
				  INNER JOIN #__sdi_list_visibility parent_visibility ON parent_visibility.id=parent_object.visibility_id
				  WHERE l.parent_id=' . $rowObjectVersion->id .
				'       AND child_object.visibility_id <> parent_object.visibility_id';
		$database->setQuery($query);
		$child_metadatastates = $database->loadObjectList();
		//echo $database->getQuery()."<br>";
		//print_r($child_metadatastates); 
		//echo "<br>";
		
		if (count($child_metadatastates) > 0)
		{
			$msg = html_Metadata::cleanText(JText::sprintf("CATALOG_METADATA_PUBLISH_DIFFMETADATASTATE", $child_metadatastates[0]->parent_state));
			foreach ($child_metadatastates as $child)
			{
				$msg .= "<br>".$child->object_name." ".$child->title." [".$child->child_state."]";
			}
			
			$response = '{
				    		success: false,
						    errors: {
						        message: "'.$msg.'"
						    }
						}';
			print_r($response);
			die();
		}
		
		$xml= $_POST['xml'];
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc = DOMDocument::loadXML($xml);

		// Enregistrement de la métadonnée dans geonetwork
		$updated = ADMIN_metadata::CURLUpdateMetadata($metadata_id, $XMLDoc );
			
		if ($updated <> 1)
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
			$rowMetadata->published=date('Y-m-d', strtotime($publishdate))." 00:00:00";
			$rowMetadata->metadatastate_id=1;
			$rowMetadata->updated = date('Y-m-d H:i:s');
			$rowMetadata->updatedby = $account_id;
			
			if (!$rowMetadata->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				exit();
			}
			
			// Archiver la derniére version si coche
			if ($archivelast == true)
			{
				// Récupérer toutes les versions de l'objet, ordonnées de la plus récente à la plus ancienne
				$listVersions = array();
				$database->setQuery( "SELECT * FROM #__sdi_objectversion WHERE object_id=".$object_id." AND id <>".$rowObjectVersion->id." ORDER BY created DESC" );
				$listVersions = array_merge( $listVersions, $database->loadObjectList() );
				
				// Récupérer la métadonnée de la derniére version de l'objet
				$rowLastMetadata = new metadata( $database );
				$rowLastObjectVersion = new objectversion( $database );
				if (count($listVersions) > 0)
				{
					$rowLastMetadata->load($listVersions[0]->metadata_id);
					$rowLastObjectVersion->load($listVersions[0]->id);
					
					// Archiver la derniére version
					$rowLastMetadata->archived=$rowMetadata->published;
					$rowLastMetadata->metadatastate_id=2;
					
					if (!$rowLastMetadata->store()) {
						$response = '{
							    		success: false,
									    errors: {
									        xml: "archive problem"
									    }
									}';
						print_r($response);
						die();
					}
					
					// Archiver également tous les enfants de la derniére version, 
					// pour autant que le lien entre les types d'objets aie flowdown_versioning=1
					// Récupérer tous les liens enfants de la derniére version
					$child_objectlinks=array();
					/*$query = 'SELECT l.child_id as child_id' .
							' FROM #__sdi_objectversionlink l
							  WHERE l.parent_id=' . $rowLastObjectVersion->id;*/
					$query = 'SELECT l.child_id as child_id, 
									 o_parent.id as parentobject_id, 
									 o_child.id as childobject_id, 
									 o_child.objecttype_id as child_objecttype, 
									 o_parent.objecttype_id as parent_objecttype' .
							' FROM #__sdi_objectversionlink l
							  INNER JOIN #__sdi_objectversion child ON child.id=l.child_id
							  INNER JOIN #__sdi_objectversion parent ON parent.id=l.parent_id
							  INNER JOIN #__sdi_object o_parent ON o_parent.id=parent.object_id
							  INNER JOIN #__sdi_object o_child ON o_child.id=child.object_id' .
							' WHERE l.parent_id=' . $rowLastObjectVersion->id;
					$database->setQuery($query);
					
					$child_objectlinks = array_merge($child_objectlinks, $database->loadObjectList());
					 
					if (count($child_objectlinks) >0)
					{
						foreach($child_objectlinks as $ol)
						{
							// Regarder s'il existe un lien entre ces types d'objet
							$rowObjectTypeLink =array();
							$query = 'SELECT *' .
									' FROM #__sdi_objecttypelink' .
									' WHERE parent_id=' . $ol->parent_objecttype.
									' 	AND child_id=' . $ol->child_objecttype;
							
							$database->setQuery($query);
							$rowObjectTypeLink = array_merge($rowObjectTypeLink, $database->loadObjectList());
							
							// Si le lien existe, regarder s'il demande une gestion virale des versions
							if (count($rowObjectTypeLink) >0)
							{
								foreach($rowObjectTypeLink as $otl)
								{
									// Le lien indique qu'il faut répercuter l'archivage sur la version enfant
									if ($otl->flowdown_versioning)
									{
										/*
										 * FLOWDOWN_VERSIONING
										 * Archiver la version liée
										 */
										// Récupérer la métadonnée de la derniére version de l'objet enfant
										$childLastObjectVersion = new objectversion( $database );
										$childLastObjectVersion->load($ol->child_id);
										$childLastMetadata = new metadata( $database );
										$childLastMetadata->load($childLastObjectVersion->metadata_id);
										
										$childLastMetadata->archived=$rowMetadata->published;
										$childLastMetadata->metadatastate_id=2;
										
										if (!$childLastMetadata->store()) {
											$response = '{
												    		success: false,
														    errors: {
														        xml: "archive problem"
														    }
														}';
											print_r($response);
											die();
										}
									}
								}
							}		
						}
					}
				}
			}
			
			// Checkin object
			$rowObject = new object( $database );
			$rowObject->load( $object_id );
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
	

	/*
	 * Assigner une métadonnée, et donc une version, à un utilisateur
	 */
	function assignMetadata($option)
	{
		global  $mainframe;
		$database 		=& JFactory::getDBO();
		$success		= true;
		$metadata_id 	= $_POST['metadata_id'];
		$object_id 		= $_POST['object_id'];
		$editor 		= $_POST['editor_hidden'];
		$information 	= $_POST['information'];
		$rowObject 		= new object($database);
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
		
		// Récupérer la version de l'objet liée
		$rowObjectVersion = new objectversionByMetadata_id($database);
		$rowObjectVersion->load($rowMetadata->id);
		
		// Remplir l'historique d'assignement
		$user 			= JFactory::getUser(); 
		$rowCurrentUser = new accountByUserId($database);
		$rowCurrentUser->load($user->get('id'));
		
		$rowHistory 					= new historyassign($database);
		$rowHistory->object_id			= $object_id;
		$rowHistory->objectversion_id	= $rowObjectVersion->id;
		$rowHistory->account_id			= $editor;
		$rowHistory->assigned			= date ("Y-m-d H:i:s");
		$rowHistory->assignedby			= $rowCurrentUser->id;
		$rowHistory->information		= $information;
		
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
		$database->setQuery( "SELECT *  
							  FROM #__sdi_account a
							  INNER JOIN #__users u ON a.user_id=u.id 
							  WHERE a.id=".$editor );
		$rowUser	= array_merge( $rowUser, $database->loadObjectList() );
		$body 		= JText::sprintf("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY",$user->username,$rowObject->name, $rowObjectVersion->title)."\n\n".JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_BODY_INFORMATION").":\n".$information;
		$success 	= ADMIN_metadata::sendMailByEmail($rowUser[0]->email,JText::_("CORE_REQUEST_ASSIGNED_METADATA_MAIL_SUBJECT"),$body);
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
		$pretreatmentxslfile = $_POST['pretreatmentxslfile'];
		$importtype = $_POST['importtype_id'];
		 
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		//$rowMetadata = new metadata( $database );
		//$rowMetadata->load( $rowObject->metadata_id );
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		// Récupérer la version
		$rowObjectVersion = new objectversionByMetadata_id( $database );
		$rowObjectVersion->load( $rowMetadata->id );
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		//echo $database->getQuery()." - ".$idrow;
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		if ($total == 1)
			$isManager = true;
		else
			$isManager = false;
		
		// Est-ce que cet utilisateur est un editeur?
		$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isEditor = false;
		if ($total == 1)
			$isEditor = true;
		
		
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
			
		// Récupérer les périmétres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmétres prédéfinis 
		//$rowAttributeType = new attributetype($database);
		//$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		// Télécharger le XML indiqué par l'utilisateur
		$xml = new DomDocument();
		$xml->formatOutput = true; 
		$xml = DOMDocument::loadXML($xmlfile);
		//echo ($xmlfile)."<br>";
		// Appliquer le XSL
		$style = new DomDocument();
		$style->formatOutput = true; 
		$style->load($xslfile);
		//echo ($xslfile)."<br>";
		
		// XML conforme à la norme ISO
		$processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$cswResults = $processor->transformToDoc($xml);
		//print_r($cswResults->saveXML());
		if ($cswResults == false or !$cswResults->childNodes->item(0)->hasChildNodes())
		{
			//$xpathResults = new DOMXPath($doc);
			$msg = JText::_('CATALOG_METADATA_IMPORTXML_NOMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
		}
		
		if ($importtype == 1) // Import de type remplacement
		{
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
		}
		else if ($importtype == 2) // Import de type fusion
		{
			// Récupérer en GET le xml déjà stocké pour cette métadonnée
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$metadata_id;
			//$existingXML = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
			$existingXML = simplexml_load_string(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
			// Enlever les balises CSW Response pour ne garder que les gmd, depuis gmd:MD_Metadata
			$existingXML = $existingXML->children("http://www.isotc211.org/2005/gmd");
			$existingXML = DOMDocument::loadXML($existingXML->asXML());
			//echo "<br>";print_r($existingXML->saveXML());
			// Nettoyage du XML déjà stocké, s'il y a lieu
			if ($pretreatmentxslfile)
			{
				$pretreatment = new DomDocument();
				$pretreatment->formatOutput = true; 
				$pretreatment->load($pretreatmentxslfile);
				$processor->importStylesheet($pretreatment);
				//echo "<br>";print_r($pretreatment->saveXML());
				$existingXML = $processor->transformToDoc($existingXML);
			}
			//echo "<br>";print_r($existingXML->saveXML());
			
			// Création des deux fichiers à fusionner
			$tmp = uniqid();
			//$existingXMLFile = "/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/test_import/existingXML_clean.xml";
			$existingXMLFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_1.xml';
			//$existingXMLFileRelative='C:/Inetpub/wwwroot/geodbmeta_test/administrator/components/com_easysdi_core/xml/tmp/'.$tmp.'_1.xml';
			$existingXMLFileRelative=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_1.xml';
			$existingXML->save($existingXMLFile);
			
			//$ESRIXMLFile = "/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/test_import/ESRIXML_iso.xml";
			$ESRIXMLFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_2.xml';
			//echo JPATH_ADMINISTRATOR;
			//$ESRIXMLFileRelative='C:/Inetpub/wwwroot/geodbmeta_test/administrator/components/com_easysdi_core/xml/tmp/'.$tmp.'_2.xml';
			$ESRIXMLFileRelative=JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_2.xml';
			$cswResults->save($ESRIXMLFile);
			
			if (DS <> "/" )
			{
				$existingXMLFileRelative = str_replace(DS, "/", $existingXMLFileRelative);
				$ESRIXMLFileRelative = str_replace(DS, "/", $ESRIXMLFileRelative);
			}
			
			// Récupération de la feuille de style à utiliser pour les fusionner
			$style = new DomDocument();
			$style->formatOutput = true; 
			//$style->load("/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_core/xsl/XML2XML_merge_ESRI.xsl");
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XML_merge_ESRI.xsl');
			//echo "<br>";print_r($style->saveXML());
			
			$xmlmerge= "<?xml version='1.0'?>
					<merge xmlns='http://informatik.hu-berlin.de/merge'>
					  <file1>$existingXMLFileRelative</file1>
					  <file2>$ESRIXMLFileRelative</file2>
					</merge>";
	
			$merging = new DomDocument();
			$merging->formatOutput = true; 
			$merging = DOMDocument::loadXML($xmlmerge);
			$cswResults = new DomDocument();
			$cswResults->formatOutput = true; 
			//echo "<br>";print_r($merging->saveXML());
			$MergingProcessor = new xsltProcessor();
			$MergingProcessor->importStylesheet($style);
			$cswResults = $MergingProcessor->transformToDoc($merging);
			//print_r($cswResults->saveXML());break;
			$mergedXMLFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_3.xml';
			$cswResults->save($mergedXMLFile);
			
			// Suppression des deux fichiers à fusionner
			unlink($existingXMLFile);
			unlink($ESRIXMLFile);
		}
		
		// Construction du DOMXPath à utiliser pour générer la vue d'édition
		$doc = new DOMDocument('1.0', 'UTF-8');
		//$cswResults->save("C:\\RecorderWebGIS\\cswResult.xml");
		
		if ($cswResults <> false and $cswResults->childNodes->item(0) and $cswResults->childNodes->item(0)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else
		{
			//$xpathResults = new DOMXPath($doc);
			$msg = JText::_('CATALOG_METADATA_IMPORTXML_PROBLEMTRANSFORMATION_MSG');
			$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
		}
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
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option);
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
		$pretreatmentxslfile = $_POST['pretreatmentxslfile'];
		$importtype = $_POST['importtype_id'];
		$serviceversion = $_POST['serviceversion'];
		$outputschema = $_POST['outputschema'];
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		// Récupérer la version
		$rowObjectVersion = new objectversionByMetadata_id( $database );
		$rowObjectVersion->load( $rowMetadata->id );
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, at.isocode as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a RIGHT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		if ($total == 1)
			$isManager = true;
		else
			$isManager = false;
		
		// Est-ce que cet utilisateur est un editeur?
		$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isEditor = false;
		if ($total == 1)
			$isEditor = true;
		
		
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
			
		// Récupérer les périmétres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		//$metadata_id = "0f62e111-831d-4547-aee7-03ad10a3a141";
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmétres prédéfinis 
		//$rowAttributeType = new attributetype($database);
		//$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		//$catalogUrlGetRecordById = $url."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$importid;
		$catalogUrlGetRecordById = $url."?request=GetRecordById&service=CSW&elementSetName=full&version=".$serviceversion."&outputschema=".$outputschema."&content=CORE&id=".$importid;
		
		// En GET
		//$xml = DOMDocument::load($catalogUrlGetRecordById);
		$xml = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		
		// Appliquer le XSL
		$style = new DomDocument();
		$style->load($xslfile);
		
		$processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$cswResults = $processor->transformToDoc($xml);
	
		if ($cswResults == false)
		{
			if (!$cswResults->childNodes->item($controlPos)->hasChildNodes())
			{
				//$xpathResults = new DOMXPath($doc);
				$msg = JText::_('CATALOG_METADATA_IMPORTCSW_NOMETADATA_MSG');
				$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
			}
		}
		
		if ($importtype == 1) // Import de type remplacement
		{
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
		}
		else if ($importtype == 2) // Import de type fusion
		{
			// Récupérer en GET le xml déjé stocké pour cette métadonnée
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$metadata_id;
			//$existingXML = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
			$existingXML = simplexml_load_string(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
			// Enlever les balises CSW Response pour ne garder que les gmd, depuis gmd:MD_Metadata
			$existingXML = $existingXML->children("http://www.isotc211.org/2005/gmd");
			$existingXML = DOMDocument::loadXML($existingXML->asXML());
			
			// Nettoyage du XML déjé stocké, s'il y a lieu
			if ($pretreatmentxslfile)
			{
				$pretreatment = new DomDocument();
				$pretreatment->load($pretreatmentxslfile);
				$processor->importStylesheet($pretreatment);
				$existingXML = $processor->transformToDoc($existingXML);
			}
	
			// Création des deux fichiers à fusionner
			$tmp = uniqid();
			//$existingXMLFile = "/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/test_import/existingXML_clean.xml";
			$existingXMLFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_1.xml';
			$existingXML->save($existingXMLFile);
			
			//$ESRIXMLFile = "/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_catalog/core/controller/test_import/ESRIXML_iso.xml";
			$ESRIXMLFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'_2.xml';
			$cswResults->save($ESRIXMLFile);
			
			// Récupération de la feuille de style à utiliser pour les fusionner
			$style = new DomDocument();
			//$style->load("/home/sites/demo.depth.ch/web/geodbmeta/administrator/components/com_easysdi_core/xsl/XML2XML_merge_ESRI.xsl");
			$style->load( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XML_merge_ESRI.xsl');
			
			$xmlmerge= "<?xml version=\"1.0\"?>
					<merge xmlns=\"http://informatik.hu-berlin.de/merge\">
					  <file1>".$existingXMLFile."</file1>
					  <file2>".$ESRIXMLFile."</file2>
					</merge>";
	
			$merging = DOMDocument::loadXML($xmlmerge);
			$processor->importStylesheet($style);
			$cswResults = $processor->transformToDoc($merging);
			
			// Suppression des deux fichiers à fusionner
			unlink($existingXMLFile);
			unlink($ESRIXMLFile);
		}
		
		// Construction du DOMXPath à utiliser pour générer la vue d'édition
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		// Le document a été créé correctement et la balise csw:GetRecordByIdResponse a au moins un enfant => résultat retourné
		$controlPos = 0;
		if ($cswResults->childNodes->item(0)->nodeName == "#text")
			$controlPos = 1;
		
		if ($cswResults <> false and $cswResults->childNodes->item($controlPos) and $cswResults->childNodes->item($controlPos)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else
		{
			//$xpathResults = new DOMXPath($doc);
			$msg = JText::_('CATALOG_METADATA_IMPORTCSW_PROBLEMTRANSFORMATION_MSG');
			$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
		}
				
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
        } 
        
        // Parcourir les noeuds enfants de la classe racine.
		// - Pour chaque classe rencontrée, ouvrir un niveau de hiérarchie dans la treeview
		// - Pour chaque attribut rencontré, créer un champ de saisie du type rendertype de la relation entre la classe et l'attribut
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option);
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
		// Récupérer la version
		$rowObjectVersion = new objectversionByMetadata_id( $database );
		$rowObjectVersion->load( $rowMetadata->id );
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c RIGHT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, at.isocode as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a RIGHT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		
		// Est-ce que cet utilisateur est un manager?
		$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		if ($total == 1)
			$isManager = true;
		else
			$isManager = false;
		
		// Est-ce que cet utilisateur est un editeur?
		$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$rowObject->id) ;
		$total = $database->loadResult();
		$isEditor = false;
		if ($total == 1)
			$isEditor = true;
		
		
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
			
		// Récupérer les périmétres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmétres prédéfinis 
		//$rowAttributeType = new attributetype($database);
		//$rowAttributeType->load(config_easysdi::getValue("catalog_boundary_type"));
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$metadata_guid;
		
		// En GET
		//$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		$cswResults = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		
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
		
		if ($cswResults <> false and $cswResults->childNodes->item(0)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else
		{
			//$xpathResults = new DOMXPath($doc);
			$msg = JText::_('CATALOG_METADATA_REPLICATE_NOMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
		}
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
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option);
		
	}
	
	function resetMetadata($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$metadata_id = $_POST['metadata_id'];
		$object_id = $_POST['object_id'];
		
		// Récupérer l'objet lié à cette métadonnée
		$rowObject = new object( $database );
		$rowObject->load( $object_id );
		// Récupérer la métadonnée
		$rowMetadata = new metadataByGuid( $database );
		$rowMetadata->load($metadata_id);
		
		// Regarder si une version précéde celle-ci
		$rowObjectVersion = new objectversionByMetadata_id( $database );
		$rowObjectVersion->load($rowMetadata->id);
		
		if ($rowObjectVersion->parent_id == null) // Aucune version qui précéde => Vider le formulaire
		{
			// Construire un XMl vide
			// Récupérer l'attribut qui correspond au stockage de l'id
			$idrow = "";
			//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',a.isocode) as attribute_isocode, at.isocode as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, CONCAT(atns.prefix, ':', at.isocode) as type_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
			$idrow = $database->loadObjectList();
			
			// Récupérer la classe racine
			$root = array();
			$database->setQuery("SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix,':',c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id);
			$root = array_merge( $root, $database->loadObjectList() );
			
			// Création d'un xml vide avec juste le fileIdentifier
			$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
							<".$root[0]->isocode."
								xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
								xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
								xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
								xmlns:gml=\"http://www.opengis.net/gml\" 
								xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
								xmlns:srv=\"http://www.isotc211.org/2005/srv\"
								xmlns:ext=\"http://www.depth.ch/2008/ext\">
								
								<".$idrow[0]->attribute_isocode.">
									<".$idrow[0]->type_isocode.">".$rowMetadata->guid."</".$idrow[0]->type_isocode.">
								</".$idrow[0]->attribute_isocode.">
							</".$root[0]->isocode.">";
			
			$cswResults = DOMDocument::loadXML($xmlstr);
				
			
		}
		else  // Une version qui précéde => Charger la derniére version
		{
			// Récupérer la version précédente
			$rowLastObjectVersion = new objectversion( $database );
			$rowLastObjectVersion->load($rowObjectVersion->parent_id);
			$rowLastMetadata = new metadata( $database );
			$rowLastMetadata->load($rowLastObjectVersion->metadata_id);
			
			// Récupérer le XML de la version précédente
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
			$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$rowLastMetadata->guid;
			//$cswResults = DOMDocument::load($catalogUrlGetRecordById);
			$cswResults = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		
			// Récupérer la classe racine du profile du type d'objet
			$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
			$database->setQuery( $query );
			$root = $database->loadObjectList();
			
			// Récupérer l'attribut qui correspond au stockage de l'id
			$idrow = "";
			//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
			$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
			$idrow = $database->loadObjectList();
			
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
	        
	        
		}
		
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
		$query = "SELECT c.name as name, ns.prefix as ns, CONCAT(ns.prefix, ':', c.isocode) as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c LEFT OUTER JOIN #__sdi_namespace ns ON c.namespace_id=ns.id WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		// Récupérer le profil lié à cet objet
		$query = "SELECT profile_id FROM #__sdi_objecttype WHERE id=".$rowObject->objecttype_id;
		$database->setQuery( $query );
		$profile_id = $database->loadResult();
		
		// Récupérer l'attribut qui correspond au stockage de l'id
		$idrow = "";
		//$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_list_attributetype as at, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND at.id=a.attributetype_id AND ot.id=".$rowObject->objecttype_id);
		$database->setQuery("SELECT a.name as name, ns.prefix as ns, CONCAT(atns.prefix, ':', at.isocode) as list_isocode FROM #__sdi_profile p, #__sdi_objecttype ot, #__sdi_relation rel, #__sdi_attribute a LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.attributetype_id LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id WHERE p.id=ot.profile_id AND rel.id=p.metadataid AND a.id=rel.attributechild_id AND ot.id=".$rowObject->objecttype_id);
		$idrow = $database->loadObjectList();
		//echo $database->getQuery()." - ".$idrow;
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
			
		// Récupérer les périmétres administratifs
		$boundaries = array();
		$database->setQuery( "SELECT name, guid, northbound, southbound, westbound, eastbound FROM #__sdi_boundary") ;
		$boundaries = array_merge( $boundaries, $database->loadObjectList() );
		
		// Récupérer la métadonnée en CSW
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		// Type d'attribut pour les périmétres prédéfinis 
		$query = "SELECT t.*, CONCAT(ns.prefix, ':', t.isocode) as attributetype_isocode FROM #__sdi_sys_stereotype t LEFT OUTER JOIN #__sdi_namespace ns ON t.namespace_id=ns.id WHERE t.id=".config_easysdi::getValue("catalog_boundary_type");
		$database->setQuery( $query );
		$rowAttributeType = $database->loadObject();
		$type_isocode = $rowAttributeType->attributetype_isocode;
		
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		// Construction du DOMXPath à utiliser pour générer la vue d'édition
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		if ($cswResults <> false and $cswResults->childNodes->item(0)->hasChildNodes())
			$xpathResults = new DOMXPath($cswResults);
		else
		{
			$msg = JText::_('CATALOG_METADATA_RESET_NOMETADATA_MSG');
			$mainframe->redirect("index.php?option=$option&task=editMetadata&cid[]=".$rowObjectVersion->id, $msg );
		}
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
		HTML_metadata::editMetadata($rowObject->id, $root, $rowMetadata->guid, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $rowObject->name, $rowObjectVersion->title, $option);
			
	}
	
	function getContact($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		//print_r($_POST);
		$searchPattern = $_POST['query'];
		$objecttype_id = $_POST['objecttype_id'];
		$start = $_POST['start'];
		$limit = $_POST['limit'];
		
		if (!$searchPattern)
			$searchPattern = "";
		
		// Récupérer tous les objets du type d'objet lié dont le nom comporte le searchPattern
		$total = 0;
		$database->setQuery( "	SELECT COUNT(*) 
								FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m, #__sdi_objectversion ov
								WHERE 	o.id=ov.object_id
										AND ov.metadata_id=m.id 
										AND o.objecttype_id=ot.id 
										AND ot.id=".$objecttype_id."  
										AND o.name LIKE '%".$searchPattern."%' 
										"
										//AND o.published=true
							);
		$total = $database->loadResult();
		
		$results = array();
		if ($total > 1)
		{
			$database->setQuery( "	SELECT DISTINCT o.id as id, m.guid as guid, CONCAT(o.name, ' [' , ov.title, ']') as name 
									FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m, #__sdi_objectversion ov
									WHERE 	o.id=ov.object_id
											AND ov.metadata_id=m.id 
											AND o.objecttype_id=ot.id 
											AND ot.id=".$objecttype_id."  
											AND o.name LIKE '%".$searchPattern."%'
									ORDER BY CONCAT(o.name, ' [' , ov.title, ']')
									LIMIT $start, $limit
									"
										//AND o.published=true
								);
		}
		else
		{
			$database->setQuery( "	SELECT DISTINCT o.id as id, m.guid as guid, o.name as name 
									FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m, #__sdi_objectversion ov
									WHERE 	o.id=ov.object_id
											AND ov.metadata_id=m.id 
											AND o.objecttype_id=ot.id 
											AND ot.id=".$objecttype_id."  
											AND o.name LIKE '%".$searchPattern."%'
									ORDER BY CONCAT(o.name, ' [' , ov.title, ']')
									LIMIT $start, $limit
									"
										//AND o.published=true
								);
		}
		$results= array_merge( $results, $database->loadObjectList() );
		//echo $database->getQuery();
		// Construire le tableau de résultats
		$return = array ("total"=>$total, "contacts"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
	}
	
	function getObjectVersion($option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$dir 	= $_POST['dir'];
		$sort 	= $_POST['sort'];
		
		//get start and limit if present
		$start 	= (array_key_exists('start', $_REQUEST))? $_REQUEST["start"]: 0;
		$count 	= (array_key_exists('limit', $_REQUEST))? $_REQUEST["limit"]: 10;
			
		$objecttype_id 		= null;
		$objectname 		= null;
		$objectstatus 		= null;
		$objectversion 		= null;
		if (array_key_exists('objecttype_id', $_POST))
			$objecttype_id 	= $_POST['objecttype_id'];
		if (array_key_exists('objectname', $_POST))
			$objectname 	= $_POST['objectname'];
		if (array_key_exists('objectstatus', $_POST))
			$objectstatus 	= $_POST['objectstatus'];
		if (array_key_exists('objectversion', $_POST))
			$objectversion	= $_POST['objectversion'];
		
		// Récupérer tous les objets du type d'objet lié dont le nom comporte le searchPattern
		$results = array();
		if ($objectversion == 'Last' )
		{
			$query = " SELECT 	ov.id as version_id,
								m.guid as metadata_guid,
								o.name as object_name,
								ov.title as version_title
						FROM 	#__sdi_object o,
								#__sdi_objectversion ov,
								#__sdi_objecttype ot,
								(
									SELECT ssm.id, ssm.metadatastate_id,ssm.guid,ssm.published,ssm.archived,max( ssm.published ), ssov.object_id
									FROM jos_sdi_metadata ssm
									INNER JOIN jos_sdi_list_metadatastate ssms ON ssms.id = ssm.metadatastate_id
									INNER JOIN jos_sdi_objectversion ssov ON ssov.metadata_id = ssm.id
									WHERE
									(
										(ssms.code='published' AND ssm.published <=NOW())
										OR
										(ssms.code='archived' AND ssm.archived >NOW())
									)
									GROUP BY ssov.object_id
									ORDER BY ssov.object_id
								)m
						WHERE 	ov.object_id=o.id
						AND 	ov.metadata_id=m.id
						AND 	o.objecttype_id=ot.id
						AND 	ot.predefined=false";
		}
		else 
		{
			$query = "SELECT 	ov.id as version_id,
								m.guid as metadata_guid,
								o.name as object_name,
								ov.title as version_title
						FROM 	#__sdi_object o,
								#__sdi_objectversion ov,
								#__sdi_objecttype ot,
								#__sdi_metadata m
						WHERE 	ov.object_id=o.id
						AND 	ov.metadata_id=m.id
						AND 	o.objecttype_id=ot.id
						AND 	ot.predefined=false";
		}
		if ($objecttype_id)
			$query .= " AND ot.id=".$objecttype_id;
		if ($objectname)
			$query .= " AND (o.name LIKE '%".$objectname."%' OR ov.name LIKE '%".$objectname."%')";
		if ($objectstatus)
			$query .= " AND m.metadatastate_id=".$objectstatus;
		
		//add sort direction if not empty
		if ($sort != "") 
			$queryFiltered = $query." ORDER BY ".$sort." ".$dir;

		//add start and limit
		$queryFiltered.= " LIMIT ".$start.",".$count;

		$database->setQuery($queryFiltered);
		$results= array_merge( $results, $database->loadObjectList() );
		
		
		$database->setQuery($query);
		$total = count($database->loadObjectList());
		
		// Construire le tableau de résultats
		$return = array ("total"=>$total, "objects"=>$results);
		
		print_r(HTML_metadata::array2json($return));
		die();
	}
	
	function PostXMLRequest($url,$xmlBody)
	{
		// Récupération du cookie sous la forme clé=valeur;clé=valeur
		$cookiesList=array();
		foreach($_COOKIE as $key => $val)
		{
			$cookiesList[]=$key."=".$val;
		}
		$cookies= implode(";", $cookiesList);
		
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
            
 		curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
 
        return $output;
    }
    
	function GetXMLRequest($url)
	{
		// Récupération du cookie sous la forme clé=valeur;clé=valeur
		$cookiesList=array();
		foreach($_COOKIE as $key => $val)
		{
			$cookiesList[]=$key."=".$val;
		}
		$cookies= implode(";", $cookiesList);
		
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
            
 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
 
        return $output;
    }
    
	function CURLRequest($type, $url, $xmlBody="")
	{
		$database =& JFactory::getDBO(); 
		
		// Récupération du cookie sous la forme clé=valeur;clé=valeur
		$cookiesList=array();
		foreach($_COOKIE as $key => $val)
		{
			$cookiesList[]=$key."=".$val;
		}
		$cookies= implode(";", $cookiesList);
		
		$ch = curl_init($url);
		// Configuration commune
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"'));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        
        // Spécifique POST
        if ($type=="POST")
        {
        	curl_setopt($ch, CURLOPT_POST, 1);
        	//curl_setopt($ch, CURLOPT_MUTE, 1);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
        }
        // Spécifique GET
        else if ($type=="GET")
        {
        	curl_setopt($ch, CURLOPT_POST, 0);
        }
        
        // Authentification HTTP avec code="service" dans la table sdi_systemaccount
        $serviceaccount=array();
        $database->setQuery( "SELECT u.username, u.password 
        					  FROM #__sdi_systemaccount sa
        					  INNER JOIN #__sdi_account a ON sa.account_id=a.id
        					  INNER JOIN #__users u ON a.user_id=u.id
        					  WHERE sa.code = 'service'" );
		$serviceaccount= $database->loadObject();
		
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
		curl_setopt($ch, CURLOPT_USERPWD, $serviceaccount->username.":".$serviceaccount->password); 
        
        $output = curl_exec($ch);        
        curl_close($ch);
 
        return $output;
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
	
	function CURLUpdateMetadata($metadata_id, $XMLDoc)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		$xmlstr = '<?xml version="1.0" encoding="UTF-8"?>
			<csw:Transaction service="CSW" version="2.0.2" xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" xmlns:ogc="http://www.opengis.net/ogc" 
			    xmlns:apiso="http://www.opengis.net/cat/csw/apiso/1.0">
			    <csw:Update>
			    	'.substr($XMLDoc->saveXML(), strlen('<?xml version="1.0" encoding="UTF-8"?>')).'
			        <csw:Constraint version="1.0.0">
			            <ogc:Filter>
			                <ogc:PropertyIsLike wildCard="%" singleChar="_" escape="/">
			                    <ogc:PropertyName>apiso:identifier</ogc:PropertyName>
			                    <ogc:Literal>'.$metadata_id.'</ogc:Literal>
			                </ogc:PropertyIsLike>
			            </ogc:Filter>
			        </csw:Constraint>
			    </csw:Update>
			</csw:Transaction>'; 
		
		$result = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase, $xmlstr);
		$updateResults = DOMDocument::loadXML($result);
		
		$xpathUpdate = new DOMXPath($updateResults);
		$xpathUpdate->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		
		$updated = $xpathUpdate->query("//csw:totalUpdated")->item(0)->nodeValue;
		
		return $updated;
	}
	
	function uploadFileAndGetLink ($option){
		
		//Check user's rights
		$user = JFactory::getUser();
		if (!userManager::isUserAllowed($user,"METADATA")){
			echo json_encode(array("success"=>false, "cause"=>"unauthorized"));
			die;
		}
		
		//Get configuration params
		$database =& JFactory::getDBO();
		$query = "select value as config from #__sdi_configuration where code ='CATALOG_METADATA_LINKED_FILE_REPOSITORY'";
		$database->setQuery($query);
		$repository = $database->loadResult();
		$query = "select value as config from #__sdi_configuration where code ='CATALOG_METADATA_LINKED_FILE_BASE_URI'";
		$database->setQuery($query);
		$url = $database->loadResult();
		
		//Generate random file name
		$original_file_name = utf8_decode($_FILES['uploadfilefield']['name']);
		$guid = sprintf( '%04x%04x%04x', 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0x0fff ) | 0x4000, 
						mt_rand( 0, 0x3fff ) | 0x8000, 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ), 
						mt_rand( 0, 0xffff ) 
					   );
		$file_name = $guid."_".$original_file_name;
		$file_location = $repository.DS.$file_name;
		
		if(move_uploaded_file($_FILES['uploadfilefield']['tmp_name'], $file_location)) {
			$result = array("success"=>true, "url"=>$url."/".$file_name);
			echo json_encode($result);
		} else{
			echo json_encode(array("success"=>false, "cause"=>"move_uploaded_file_failed"));
		}
		die();
	}
	
	function deleteUploadedFile ($option, $fileurl){
		if($fileurl == null){
			echo json_encode(array("success"=>false, "cause"=>"no_file_submitted"));
			die;
		}
		//Check user's rights
		$user = JFactory::getUser();
		if (!userManager::isUserAllowed($user,"METADATA")){
			echo json_encode(array("success"=>false, "cause"=>"unauthorized"));
			die;
		}
		
		//Get configuration params
		$database =& JFactory::getDBO();
		$query = "select value as config from #__sdi_configuration where code ='CATALOG_METADATA_LINKED_FILE_REPOSITORY'";
		$database->setQuery($query);
		$repository = $database->loadResult();
		$query = "select value as config from #__sdi_configuration where code ='CATALOG_METADATA_LINKED_FILE_BASE_URI'";
		$database->setQuery($query);
		$url = $database->loadResult();
		
		$i = strlen($url);
		$filename = substr($fileurl, $i);
		
		if (!file_exists($repository."/".$filename)) 
			echo json_encode(array("success"=>true));
		else if(unlink($repository."/".$filename))
			echo json_encode(array("success"=>true));
		else
			echo json_encode(array("success"=>false, "cause"=>"unlink_failed"));
		die;
	}

	/**
	 * 
	 * @param unknown_type $option
	 */
	function synchronizeMetadata ($metadata_id){
		global  $mainframe;
		$db		 		=& JFactory::getDBO();
		$user 			= JFactory::getUser();
		
		$rowMetadata = new metadataByGuid( $db );
		$rowMetadata->load( $metadata_id );
		
		$rowObjectVersion = new objectversionByMetadata_id( $db );
		$rowObjectVersion->load( $rowMetadata->id );
		
		$rowObject = new object( $db );
		$rowObject->load( $rowObjectVersion->object_id );
		
		//Checkout status
		if ( JTable::isCheckedOut($user->get('id'), $rowObject->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('CATALOG_METADATA_SYNCHRONIZE_MESSAGE_CHECKEDOUT_METADATA_PARENT'), $rowObject->name." - ".$rowObjectVersion->title);
			$mainframe->enqueueMessage($msg);
			return;
		}
		$rowObject->checkout($user->get('id'));
		
		//Load children metadata guid
		$query = "SELECT m.guid FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id
					INNER JOIN #__sdi_objectversionlink ovl ON ovl.child_id = ov.id
					WHERE ovl.parent_id = (SELECT id FROM #__sdi_objectversion WHERE metadata_id = (SELECT id FROM #__sdi_metadata WHERE guid = '$metadata_id' ))
					";
		$db->setQuery($query);
		$childguidlist = $db->loadResultArray();
		if(count($childguidlist) < 1 )
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_SYNCHRONIZE_MESSAGE_NO_CHILD"));
			$rowObject->checkin();
			return;
		}
		
		//Checkout status of children metadata
		$okgo = true;
		$childObjectList = array();
		foreach ($childguidlist as $childguid)
		{
			$rowChildMetadata = new metadataByGuid( $db );
			$rowChildMetadata->load( $childguid );
				
			$rowChildObjectVersion = new objectversionByMetadata_id( $db );
			$rowChildObjectVersion->load( $rowChildMetadata->id );
				
			$rowChildObject = new object( $db );
			$rowChildObject->load( $rowChildObjectVersion->object_id );
			
			if ( JTable::isCheckedOut($user->get('id'), $rowChildObject->checked_out))
			{
				$msg = JText::sprintf('DESCBEINGEDITTED', JText::sprintf('CATALOG_METADATA_SYNCHRONIZE_MESSAGE_CHECKEDOUT_METADATA', "\"".$rowChildObject->name." - ".$rowChildObjectVersion->title."\"","\"".$rowObject->name." - ".$rowObjectVersion->title."\""));
				$mainframe->enqueueMessage($msg);
				$mainframe->enqueueMessage(JText::_('CATALOG_METADATA_SYNCHRONIZE_ABORTED'));
				$okgo = false;
				break;
			}
			
			$childObjectList [$childguid] = $rowChildObject;
			$rowChildObject->checkout($user->get('id'));
		}
		if(!$okgo)
		{
			//Check in the parent metadata
			$rowObject->checkin();
			//Check in all the children metadata and get out of the synchronize process
			foreach ($childObjectList as $childObject)
			{
				$childObject->checkin();
			}
			return;
		}
		
		//Load XPath to synchronize
		$query = "SELECT h.xpath from #__sdi_objecttypelinkinheritance h
					INNER JOIN #__sdi_objecttypelink otl ON otl.id = h.objecttypelink_id
					INNER JOIN #__sdi_objecttype ot ON ot.id = otl.parent_id
					INNER JOIN #__sdi_object o ON o.objecttype_id = ot.id
					INNER JOIN #__sdi_objectversion ov ON ov.object_id = o.id
					INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
					WHERE m.guid = '$metadata_id'
					AND otl.inheritance = 1
				 ";
		$db->setQuery($query);
		$xpathlist = $db->loadResultArray();
		if(count($xpathlist) < 1 )
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_SYNCHRONIZE_MESSAGE_NO_XPATH"));
			return;
		}
		
		//Load parent Metadata
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$metadata_id;
		$metadatacsw = DOMDocument::loadXML(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
		$metadataxpath = new DOMXPath($metadatacsw);
		
		//Register namespace
		$query = "SELECT prefix, uri FROM #__sdi_namespace";
		$db->setQuery($query);
		$namespaces = $db->loadObjectList();
		foreach ($namespaces as $namespace)
			$metadataxpath->registerNamespace($namespace->prefix, $namespace->uri);
		
		
		//Loop on xpath to store the values
		$parentvaluelist = array();
		$multiplenodeerror = array();
		foreach ($xpathlist as $xpath)
		{
			//Check if all xpath nodes are unique in the parent metadata
			//If it is not the case, put the xpath to the node in $multiplenodeerror array
			//If it is the case, put the complete xpath in $parentvaluelist array
			$nodeList = $metadataxpath->query($xpath);
			
			if(!$nodeList){
				$multiplenodeerror[$xpath] = 0;
				continue;
			}
			
// 			if($nodeList->length > 1){
// // 				$multiplenodeerror[$xpath] = $nodeList->length;
// 			}
// 			else
			if($nodeList->length > 0)
				$parentvaluelist[$xpath] = $nodeList;
			
			$subxpath = $xpath;
			while (strlen($subxpath) > 1)
			{
				$pos = strrpos ($subxpath, '/', -1);
				$subxpath =  substr ($subxpath, 0, $pos);
				$nodeList = $metadataxpath->query($subxpath);
				if($nodeList->length > 1)
					$multiplenodeerror[$subxpath] = $nodeList->length;
			}
		}
		
		//If multiple node were detect in the parent metadata, the synchronization is aborted
		if(count($multiplenodeerror) > 0)
		{
			//Check in the parent metadata
			$rowObject->checkin();
			//Check in all the children metadata and get out of the synchronize process
			foreach ($childObjectList as $childObject)
			{
				$childObject->checkin();
			}
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_SYNCHRONIZE_ABORTED"));
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_SYNCHRONIZE_CHECK_XPATH"));
			foreach ($multiplenodeerror as $key => $value)
				$mainframe->enqueueMessage(JTEXT::sprintf("CATALOG_METADATA_SYNCHRONIZE_ABORTED_MESSAGE",$key, $value), "ERROR");
			return;
		}
		
		//Load children metadata guid
		$query = "SELECT m.guid FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON m.id = ov.metadata_id 
					INNER JOIN #__sdi_objectversionlink ovl ON ovl.child_id = ov.id
					WHERE ovl.parent_id = (SELECT id FROM #__sdi_objectversion WHERE metadata_id = (SELECT id FROM #__sdi_metadata WHERE guid = '$metadata_id' ))
				 ";
		$db->setQuery($query);
		$childguidlist = $db->loadResultArray();
		
		if(count($childguidlist) < 1 )
		{
			$mainframe->enqueueMessage(JText::_("CATALOG_METADATA_SYNCHRONIZE_MESSAGE_NO_CHILD"));
			return;
		}
		
		//Update children metadata 
		foreach ($childguidlist as $childguid)
		{
			try {
				$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$childguid;
				$childcsw = simplexml_load_string(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
				
				// Enlever les balises CSW Response pour ne garder que les gmd, depuis gmd:MD_Metadata
				$childcsw = $childcsw->children("http://www.isotc211.org/2005/gmd");
				$childcsw = DOMDocument::loadXML('<?xml version="1.0" encoding="UTF-8"?>'.$childcsw->asXML());
				$childxpath = new DOMXPath($childcsw);
				foreach ($namespaces as $namespace)
					$childxpath->registerNamespace($namespace->prefix, $namespace->uri);
				
				foreach ($xpathlist as $xpath)
				{
					$nodes = $parentvaluelist[$xpath];
					//Parent value for a Xpath can be null/empty : corresponding values in the children md have to be null as well
// 					if($node)
// 						$node = $childcsw->importNode($node, TRUE);
					
					$nodeList = $childxpath->query($xpath);
					if($nodeList->length >= 1)
 					{
 						//Plusieur occurence du noeud dans la métadonnée enfant
 						// opt 1 : on ajoute celui à synchroniser
 						// opt 2 : on remplace le premier rencontré
 						// opt 3 : on remplace tous les noeuds enfants par l'unique parent <--
 						$parentNode = $nodeList->item(0)->parentNode;
 						$first = true;
 						foreach ($nodeList as $nodeToRemove )
 						{
 							$parentNode->removeChild($nodeToRemove);
 						}
 						foreach ($nodes as $node)
 						{
 							$node = $childcsw->importNode($node, TRUE);
 							$parentNode->appendChild($node);
 						}
 						//New nodes were created, so an update of the metadata is required before loop to the next XPath
 						$updated = ADMIN_metadata::CURLUpdateMetadata($childguid, $childcsw);
 						//And reload
 						$childcsw = simplexml_load_string(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
 						$childcsw = $childcsw->children("http://www.isotc211.org/2005/gmd");
 						$childcsw = DOMDocument::loadXML('<?xml version="1.0" encoding="UTF-8"?>'.$childcsw->asXML());
 						$childxpath = new DOMXPath($childcsw);
 						foreach ($namespaces as $namespace)
 							$childxpath->registerNamespace($namespace->prefix, $namespace->uri);
 					}
 					else if ($nodeList->length == 0 && $nodes->length > 0)//Node does exist in the parent MD 
 					//if node does not exist in parent MD [if ($nodeList->length == 0 && (!$nodes  || $nodes->length == 0))], nothing has to be created in children MD
 					{
 						//Node doesn't exist in the child metadata, it has to be created, so do its missing parents.
 						$offset = 1;
 						$last = false;
 						while ($offset < strlen($xpath) )
 						{
 							$pos = strpos ($xpath, '/', $offset);
 							if(!$pos)//End of the xpath
 							{
 								$currentxPath = $xpath;
 								$last = true;
 							}
 							else
 							{
 								$currentxPath =  substr ($xpath, 0, $pos);
 							}
 							
 							$nodechildList = $childxpath->query($currentxPath);
 							if($nodechildList->length > 0 )
 							{
 								//Node exists, continue to next child node in the xPath
 								$prevNode = $nodechildList->item(0);
 							}
 							else if($nodechildList->length == 0)
 							{
 								//Node doesn't exist : get the previous node (the parent) to create this missing node
 								if(!$last)
 								{
 									//Not the leaf of the xPath so create the missing node and continue to next child node of the xPath
	 								$prevpos = strrpos ($currentxPath, '/', -1);
	 								$nodeName = substr ($currentxPath, $prevpos +1);
	 								$newNode = $childcsw->createElement($nodeName);
	 								$appendedNode = $prevNode->appendChild($newNode);
	 								$prevNode = $appendedNode;
 								}
 								else
 								{
 									//End of the xPath : append directly the node from the parent metadata
 									foreach ($nodes as $node)
 									{
 										$node = $childcsw->importNode($node, TRUE);
 										$prevNode->appendChild($node);
 									}
 								}
 							}
 							if($last)
 								break;
 							else
 								$offset = $pos + 1;
 						}
 						
 						//New nodes were created, so an update of the metadata is required before loop to the next XPath
 						$updated = ADMIN_metadata::CURLUpdateMetadata($childguid, $childcsw);
 						//And reload
 						$childcsw = simplexml_load_string(ADMIN_metadata::CURLRequest("GET", $catalogUrlGetRecordById));
 						$childcsw = $childcsw->children("http://www.isotc211.org/2005/gmd");
 						$childcsw = DOMDocument::loadXML('<?xml version="1.0" encoding="UTF-8"?>'.$childcsw->asXML());
 						$childxpath = new DOMXPath($childcsw);
 						foreach ($namespaces as $namespace)
 							$childxpath->registerNamespace($namespace->prefix, $namespace->uri);
 					}
//  					else if($nodeList->length == 1 && $nodes->lenght == 1)//Node does exist in the parent MD 
//  					{
//  						print_r("cas 3");
//  						print_r("<br>");
//  						//Node exists just one time in the child metadata : it is replaced
//  						$nodeList->item(0)->parentNode->replaceChild($nodes->item(0), $nodeList->item(0));
//  					}
//  					else if($nodeList->length == 1 ) //Node does not exist in the parent MD (!$node)
//  					{
//  						print_r("cas 4");
//  						print_r("<br>");
//  						//Node exists just one time in the child metadata : it is removed
//  						$nodeList->item(0)->parentNode->removeChild($nodeList->item(0));
//  					}
 					
				}
				$updated = ADMIN_metadata::CURLUpdateMetadata($childguid, $childcsw);
				
				$rowChildMetadata = new metadataByGuid( $db );
				$rowChildMetadata->load( $childguid );
				
				if($updated == 0)
				{
					$rowChildObjectVersion = new objectversionByMetadata_id( $db );
					$rowChildObjectVersion->load( $rowChildMetadata->id );
						
					$rowChildObject = new object( $db );
					$rowChildObject->load( $rowChildObjectVersion->object_id );
					
					$mainframe->enqueueMessage(JText::sprintf("CATALOG_METADATA_SYNCHRONIZE_MESSAGE_CHILD_ERROR","\"".$rowChildObject->name." - ".$rowChildObjectVersion->title."\""), "ERROR");
				}
				else
				{
					$rowChildMetadata->lastsynchronization 	= date('Y-m-d H:i:s', time());
					$rowChildMetadata->synchronizedby 		= $rowMetadata->id;
					$rowChildMetadata->store();
				}
				
				$childObjectList[$childguid]->checkin();
			}
			catch (Exception $e){
				$rowChildObject->checkin();
				$mainframe->enqueueMessage($e->getMessage(),"ERROR");
			}
		}
		
		$rowMetadata->lastsynchronization = date('Y-m-d H:i:s', time());
		$rowMetadata->store();
		
		$rowObject->checkin();
		$mainframe->enqueueMessage(JText::sprintf("CATALOG_METADATA_SYNCHRONIZE_MESSAGE_SUCCESS", "\"". $rowObject->name." - ".$rowObjectVersion->title."\""));
	}
}
?>