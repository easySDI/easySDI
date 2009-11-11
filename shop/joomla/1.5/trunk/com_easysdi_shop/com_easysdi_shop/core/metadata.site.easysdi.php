<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

//function editMetadata($prof, $id, $option)
	function editMetadata($id, $option)
	{
		//$prof->startTimer("phpPart");
		//echo "Appel editMetadata: ".date('H:m:s')."<br>";
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_EDITMETADATA"); 
		/*
		 $metadatastates = array();
		 $metadatastates[] = JHTML::_('select.option','0', JText::_("EASYSDI_METADATASTATE_LIST") );
		 $database->setQuery( "SELECT id AS value, name as text FROM #__sdi_list_metadatastate ORDER BY name" );
		 $metadatastates = array_merge( $metadatastates, $database->loadObjectList() );
		 */
		// Récupérer l'objet lié à cette métadonnée
		$rowProduct = new product( $database );
		$rowProduct->load( $id );

		/*
		 $rowObject = new objectByMetadataId( $database );
		 $rowObject->load( $id );

		 // Récupérer la classe racine du profile du type d'objet
		 $query = "SELECT c.name as name, c.isocode as isocode, c.label as label, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		 $database->setQuery( $query );
		 $root = $database->loadObjectList();
		 */

		// Récupérer la métadonnée en CSW
		//$metadata_id = "0f62e111-831d-4547-aee7-03ad10a3a141";
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		//$catalogUrlBase = "http://demo.easysdi.org:8084/geonetwork/srv/en/csw";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$rowProduct->metadata_id;
		//$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&id=".$id;

		$xmlBody= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n
			<csw:GetRecordById xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\"
			    outputSchema=\"csw:IsoRecord\"> 
			    <csw:Id>".$rowProduct->metadata_id."</csw:Id>
			</csw:GetRecordById>			
		";


		//echo "<hr>".$catalogUrlBase."<br>".htmlspecialchars($xmlBody)."<hr>";
		//echo "Avant post request: ".date('H:m:s')."<br>";
		//echo "Envoi à ".$catalogUrlBase." de ".htmlspecialchars($xmlBody)."<br>";
		$xmlResponse = SITE_metadata::PostXMLRequest($catalogUrlBase, $xmlBody);
		//echo "Reponse post request: ".date('H:m:s')."<br>";
		//echo "<hr>".$xmlResponse."<br>";
		// En POST
		$cswResults = DOMDocument::loadXML($xmlResponse);

		// En GET
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		//echo "Fichier à traiter: ".$cswResults->saveXML()."<br>";

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
		$xpathResults->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.1');
		$xpathResults->registerNamespace('dc','http://purl.org/dc/elements/1.1/');
		$xpathResults->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$xpathResults->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		$xpathResults->registerNamespace('srv','http://www.isotc211.org/2005/srv');
		$xpathResults->registerNamespace('ext','http://www.depth.ch/2008/ext');
		$xpathResults->registerNamespace('xlink','http://www.w3.org/1999/xlink');		

		$query = "SELECT s.classes_id as root_id FROM #__easysdi_metadata_standard s, #__easysdi_product p WHERE p.metadata_standard_id=s.id and p.id=".$id;
		$database->setQuery( $query );
		$root_id = $database->loadResult();
		
		$query = "SELECT * FROM #__easysdi_metadata_classes WHERE id=".$root_id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		//echo "Apres construction DomDocument: ".date('H:m:s')."<br>";
		//$prof->stopTimer("phpPart");
		
		//HTML_metadata::editMetadata($prof, $id, $root, $rowProduct->metadata_id, $xpathResults, $option);
		HTML_metadata::editMetadata($id, $root, $rowProduct->metadata_id, $xpathResults, $option);
	}


	function buildXMLTree($parent, $parentFieldset, $parentName, $XMLDoc, $xmlParent, $queryPath, $currentIsocode, $scope, $keyVals, $option)
	{
		//echo "Name: ".$parentName." \r\n ";
		//echo "Isocode courant: ".$currentIsocode."\\r\\n";
		$database =& JFactory::getDBO();
		$rowChilds = array();
		$xmlClassParent = $xmlParent;
		$xmlAttributeParent = $xmlParent;
		
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		//echo "QueryPath: ".$queryPath." \r\n ";
		
		// Traitement des enfants de type list
		$rowListClass = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent;
		$database->setQuery( $query );
		$rowListClass = array_merge( $rowListClass, $database->loadObjectList() );

		foreach($rowListClass as $child)
		{
			// Nombre d'occurence de cet élément
			//$index = $_POST[$parentName."/".$child->iso_key."__1_index"];

			
			// Récupération des valeurs postées correspondantes
			$keys = array_keys($_POST);
			$usefullVals=array();
			//$usefullKeys=array();
			$count=0;
			foreach($keys as $key)
			{
				$partToCompare = substr($key, 0, strlen($parentName."-".str_replace(":", "_", $child->iso_key)));
				if ($partToCompare == $parentName."-".str_replace(":", "_", $child->iso_key))
				{
					if (substr($key, -6) <> "_index")
					{
						$count = $count+1;
						//$usefullKeys[] = $key;
						$usefullVals[] = $_POST[$key];
					}
				}
			}
			//print_r($usefullVals); echo " \r\n ";
			
			// Ajouter chacune des copies du champ dans le XML résultat
			for ($pos=1; $pos<=$count; $pos++)
			{
				// Traitement de la multiplicité
				// Récupération du path du bloc de champs qui va être créé pour construire le nom
				$listName = $parentName."-".str_replace(":", "_", $child->iso_key)."__".$pos;

				// La liste
				$content = array();
				$query = "SELECT cont.id as cont_id, cont.code_key, cont.translation as cont_translation, c.lowerbound as lowerbound, c.upperbound as upperbound, c.translation as c_translation, c.iso_key as c_isokey, l.multiple as multiple, l.name as label, l.translation as l_translation, l.iso_key as l_iso_key, l.codeValue as l_codeValue, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_list rel, #__easysdi_metadata_list l, #__easysdi_metadata_list_content cont WHERE rel.classes_id = c.id and rel.list_id=l.id and cont.list_id = l.id and c.type = 'list' and rel.classes_id=".$child->classes_to_id;
				$database->setQuery( $query );
				$content = $database->loadObjectList();
					
				// Deux traitement pour deux types de listes
				$queryPath = $queryPath."/".$child->iso_key."/".$content[0]->l_iso_key;
				/*
				if ($_POST[$listName] <>"")
						$nodeValue = $_POST[$listName];
					else
						$nodeValue="";
				*/
				$nodeValue = $usefullVals[$pos-1];
						
				$nodeValues=split(",",$nodeValue);
	   			
	   			// Le contenu de la liste
				if ($content[0]->l_codeValue)
				{
					foreach($nodeValues as $val)
					{
						$XMLNode = $XMLDoc->createElement($child->iso_key);
						$xmlAttributeParent->appendChild($XMLNode);
						
						$XMLListNode = $XMLDoc->createElement($content[0]->l_iso_key);
						$XMLNode->appendChild($XMLListNode);
						$XMLListNode->setAttribute('codeListValue', $val);
						$xmlParent = $XMLListNode;
					}
				}
				else
				{
					foreach($nodeValues as $val)
					{
						$XMLNode = $XMLDoc->createElement($child->iso_key);
						$xmlAttributeParent->appendChild($XMLNode);
						
						$XMLListNode = $XMLDoc->createElement($content[0]->l_iso_key, $val);
						$XMLNode->appendChild($XMLListNode);
						$xmlParent = $XMLListNode;
					}
				}
				/*
				$XMLListNode = $XMLDoc->createElement($content[0]->l_iso_key, $nodeValue);
				$XMLNode->appendChild($XMLListNode);
				*/
				
			}
		}

		// Traitement des enfants de type local freetext
		$rowLocText = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'locfreetext' and rel.classes_from_id=".$parent;
		$database->setQuery( $query );
		$rowLocText = array_merge( $rowLocText, $database->loadObjectList() );

		foreach($rowLocText as $child)
		{
			// Stockage du path pour atteindre ce noeud du XML
			$queryPath = $queryPath."/".$child->iso_key."/gmd:LocalisedCharacterString";
			$searchName = $parentName."-".str_replace(":", "_", $child->iso_key);
			
			// Création des enfants langue
			$langages = array();
			$query = "SELECT loc.* FROM #__easysdi_metadata_classes_locfreetext rel, #__easysdi_metadata_loc_freetext loc WHERE rel.loc_freetext_id = loc.id and rel.classes_id=".$child->classes_to_id;
			$database->setQuery( $query );
			$langages = array_merge( $langages, $database->loadObjectList() );

			
			// Nombre d'occurence de cet élément
			//$index = $_POST[$searchName."__1_index"];
			//echo $searchName." - ".$index."\r\n";
			
			// Récupération des valeurs postées correspondantes
			$keys = array_keys($_POST);
			$usefullVals=array();
			//$usefullKeys=array();
			$count=0;
			foreach($keys as $key)
			{
				$partToCompare = substr($key, 0, strlen($searchName));
				if ($partToCompare == $searchName)
				{
					if (substr($key, -6) <> "_index")
					{
						$count = $count+1;
						//$usefullKeys[] = $key;
						$usefullVals[] = array(substr($key, -8, 5) => $_POST[$key]);
					}
				}
			}
			$count = $count/count($langages);
			
			// Ajouter chacune des copies du champ dans le XML résultat
			$langIndex = 0;
			for ($pos=1; $pos<=$count; $pos++)
			{
				// Traitement de la multiplicité
				// Récupération du path du bloc de champs qui va être créé pour construire le nom
				$LocName = $parentName."-".str_replace(":", "_", $child->iso_key)."__".$pos;
				//echo "LocName: ".$LocName." - ".$pos."\r\n";

				$XMLNode = $XMLDoc->createElement($child->iso_key);
				$xmlAttributeParent->appendChild($XMLNode);
				$xmlLocParent = $XMLNode;
				
				foreach($langages as $lang)
				{	
					// Nombre d'occurence de cet élément
					//$langIndex = $_POST[$LocName."/gmd:LocalisedCharacterString/".$lang->lang."__1_index"];
					//$langIndex = 1;
					//echo "LangName: ".$LocName."/gmd:LocalisedCharacterString/".$lang->lang." - ".$langIndex."\r\n";
					 
					// Ajouter chacune des copies du champ dans le XML résultat
					//for ($langPos=1; $langPos<=$langIndex; $langPos++)
					//{
						//$LangName = $LocName."/gmd:LocalisedCharacterString/".$lang->lang."__".$langPos;
						$LangName = $LocName."-gmd_LocalisedCharacterString-".$lang->lang."__1";
						//echo $LangName." - ".$_POST[$LangName]."\r\n";
						/*if ($_POST[$LangName] <>"")
							$nodeValue = $_POST[$LangName];
						else
							$nodeValue="";
						*/
						$nodeValue=$usefullVals[$langIndex][$lang->lang];
						
						$XMLNode = $XMLDoc->createElement("gmd:LocalisedCharacterString", $nodeValue);
						$xmlLocParent->appendChild($XMLNode);
						$XMLNode->setAttribute('locale', $lang->lang);
						$xmlParent = $XMLNode;
					//}
					$langIndex = $langIndex+1;
				}
			}
		}
		
		// Traitement des enfants de type freetext
		$rowAttributeChilds = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type = 'freetext' and rel.classes_from_id=".$parent;
		$database->setQuery( $query );
		$rowAttributeChilds = array_merge( $rowAttributeChilds, $database->loadObjectList() );

		foreach($rowAttributeChilds as $child)
		{
			// Stockage du path pour atteindre ce noeud du XML
			$path = $child->iso_key;
				
			// Traitement de la multiplicité
			// Récupération du path du bloc de champs qui va être créé pour construire le nom
			$name = $parentName."-".str_replace(":", "_", $child->iso_key);
				
			// Selon le type de noeud, on lit un type de balise
			$query = "SELECT f.* FROM #__easysdi_metadata_freetext f, #__easysdi_metadata_classes_freetext rel WHERE rel.freetext_id = f.id and rel.classes_id=".$child->classes_to_id;
			$database->setQuery( $query );
			$type = $database->loadObject();
			
			// Traitement de chaque attribut
			if ($type->is_system)
			{
				if ($type->is_datetime)
				{
					$path = $path."/gco:DateTime";
					$name = $name."-gco_DateTime";
					$childType = "gco:DateTime";
				}
				else
				{
					$path = $path."/gco:CharacterString";
					$name = $name."-gco_CharacterString";
					$childType = "gco:CharacterString";
				}
			}
			else if ($type->is_date)
			{		
				$path = $path."/gco:Date";
				$name = $name."-gco_Date";
				$childType = "gco:Date";
			}
			else if ($type->is_datetime)
			{		
				$path = $path."/gco:DateTime";
				$name = $name."-gco_DateTime";
				$childType = "gco:DateTime";
			}
			else if ($type->is_number)
			{
				$path = $path."/gco:Decimal";
				$name = $name."-gco_Decimal";
				$childType = "gco:Decimal";
			}
			else if ($type->is_integer)
			{
				$path = $path."/gco:Integer";
				$name = $name."-gco_Integer";
				$childType = "gco:Integer";
			}
			else if ($type->is_constant)
			{
				$path = $path."/gco:CharacterString";
				$name = $name."-gco_CharacterString";
				$childType = "gco:CharacterString";
			}
			else if ($type->is_shorttext)
			{
				$path = $path."/gco:CharacterString";
				$name = $name."-gco_CharacterString";
				$childType = "gco:CharacterString";
			}
			else
			{
				$path = $path."/gco:CharacterString";
				$name = $name."-gco_CharacterString";
				$childType = "gco:CharacterString";
			}
				
			// Nombre d'occurence de cet élément
			// Nombre d'occurence de cet élément
			//$index = $_POST[$name."__1_index"];
			
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
			//echo $name." - ".$count." \r\n ";
			//print_r($usefullKeys); echo " \r\n ";	
			
			// Ajouter chacune des copies du champ dans le XML résultat
			for ($pos=1; $pos<=$count; $pos++)
			{
				/*
				if ($_POST[$name."__".$pos] <> "")
					$nodeValue = $_POST[$name."__".$pos];
				else
					$nodeValue = "";
				*/
				$nodeValue = $usefullVals[$pos-1];
				
				// Traitement de chaque attribut
				if ($type->default_value <> "" and $nodeValue == "")
					$nodeValue = $type->default_value;
				
				if ($type->is_datetime)
				{
					//$nodeValue = date_format(date_create($nodeValue), 'Y-m-d');
					if ($nodeValue <> "")
						$nodeValue = date('Y-m-d', strtotime($nodeValue));
					else
						$nodeValue = date('Y-m-d');
					$nodeValue = $nodeValue."T00:00:00";
				}
				
				if ($type->is_system)
				{
					if ($type->is_datetime)
					{
						$nodeValue = date('Y-m-d')."T".date('H:m:s');
						//echo $nodeValue."\r\n";
					}
					else
					{
						//echo $name."__".$pos."_hiddenVal\r\n";
						//$nodeValue = $_POST[$name."__".$pos."_hiddenVal"];
						//echo $nodeValue."\r\n";
					}
				}
					
				//echo "Value of ".$name.": ".$nodeValue." \r\n ";
				$XMLNode = $XMLDoc->createElement($child->iso_key);
				$xmlAttributeParent->appendChild($XMLNode);
				
				$XMLValueNode = $XMLDoc->createElement($childType, $nodeValue);
				$XMLNode->appendChild($XMLValueNode);
				$xmlParent = $XMLValueNode;
			}
		}
		
		// Récupération des enfants du noeud
		$rowClassChilds = array();
		$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='class' and rel.classes_from_id=".$parent;
		$database->setQuery( $query );
		$rowClassChilds = array_merge( $rowClassChilds, $database->loadObjectList() );
		
		foreach($rowClassChilds as $child)
		{
			// Nombre d'occurence de cet élément
			//$index=0;
			$count=0;
			if ($child->is_relation)
			{
				$name = $parentName."-".str_replace(":", "_", $child->iso_key);
				//$index = $_POST[$parentName."/".$child->iso_key."__1_index"];
				//$index = $index-1;
			
				foreach($keyVals as $key => $val)
				{
					if ($key == $parentName."-".str_replace(":", "_", $child->iso_key)."__1")
					{
						$count = $val;
						break;
					}
				}
				$count = $count - 1;
			}
			else
			{
				$name = $parentName;
				//$index = 1;
				$count = 1;
			}
			
			for ($pos=0; $pos<$count; $pos++)
			{
				// Flag d'index dans le nom
				if (!$child->is_relation)
					$name = $parentName;
				else
					$name = $parentName."-".str_replace(":", "_", $child->iso_key)."__".($pos+2);
				
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
					$xlinkTitleValue = 	"";
					// S'il y a un xlink:title défini, alors le mettre comme attribut du noeud
					if ($child->has_xlinkTitle and $child->is_relation)
						$xlinkTitleValue = $_POST[$name.'_xlinktitle'];

					// Parcours récursif des classes
					$XMLNode = $XMLDoc->createElement($child->iso_key);
					$xmlClassParent->appendChild($XMLNode);
					if ($xlinkTitleValue <> "")
						$XMLNode->setAttribute('xlink:title', $xlinkTitleValue);
					$xmlParent = $XMLNode;
							
					// Récupération des codes ISO et appel récursif de la fonction
					$nextIsocode = $child->iso_key;
						
					if (!$child->is_relation)
						SITE_metadata::buildXMLTree($child->classes_to_id, $child->classes_to_id, $name, &$XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $option);
					else
						SITE_metadata::buildXMLTree($child->classes_to_id, $child->classes_to_id, $name, &$XMLDoc, $XMLNode, $queryPath, $nextIsocode, $scope, $keyVals, $option);
				}
			}
		}
		
	}

	function isXPathResultCount($result, $xpath){
		$nodes = $result->query($xpath);
		if ($nodes === false) return 0;
		if ($nodes->length==0) return 0;
		$i=0;
		foreach ($nodes as $node){
			$i++;
		}
		return $i;

	}

	function saveMetadata($option)
	{
		global  $mainframe;
		$option = $_POST['option'];
		$metadata_id = $_POST['metadata_id'];
		$product_id = $_POST['product_id'];
		
		// Récupération des index des fieldsets
		$fieldsets = array();
		$fieldsets = explode(" | ", $_POST['fieldsets']);
		$keyVals = array();
		foreach($fieldsets as $fieldset)
		{
			$keys = explode(',', $fieldset);
			$keyVals[$keys[0]] = $keys[1];
		}
		//print_r($keyVals); echo " \r\n ";
		
		
		// Sauver dans un fichier les valeurs du POST
		/*$myFile = "C:\\RecorderWebGIS\\myFile.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		foreach ($keyVals as $key => $val)
			fwrite($fh, $key." - ".$val);
		fclose($fh);
		*/
		// Parcourir les classes et les attributs
		$XMLDoc = new DOMDocument('1.0', 'UTF-8');
		$XMLDoc->formatOutput = true;
		// Récupérer l'objet lié à cette métadonnée
		$database =& JFactory::getDBO();
		/*
		 $rowObject = new objectByMetadataId( $database );
		 $rowObject->load($metadata_id);

		 // Récupérer la classe racine du profile du type d'objet
		 $query = "SELECT c.name as name, c.isocode as isocode, prof.class_id as id FROM #__sdi_profile prof, #__sdi_objecttype ot, #__sdi_object o, #__sdi_class c WHERE prof.id=ot.profile_id AND ot.id=o.objecttype_id AND c.id=prof.class_id AND o.id=".$rowObject->id;
		 $database->setQuery( $query );
		 $root = $database->loadObjectList();
		 */
		//Pour chaque élément rencontré, l'insérer dans le xml
		$XMLNode = $XMLDoc->createElement("gmd:MD_Metadata");
		$XMLDoc->appendChild($XMLNode);
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gco', 'http://www.isotc211.org/2005/gco');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ns3', 'http://www.isotc211.org/2005/gmx');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xlink', 'http://www.w3.org/1999/xlink');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gml', 'http://www.opengis.net/gml');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:gts', 'http://www.isotc211.org/2005/gts');
		//$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:srv', 'http://www.isotc211.org/2005/srv');
		$XMLNode->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:ext', 'http://www.depth.ch/2008/ext');

		/*$doc = "<gmd:MD_Metadata
					xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
					xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
					xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
					xmlns:gml=\"http://www.opengis.net/gml\" 
					xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
					xmlns:srv=\"http://www.isotc211.org/2005/srv\"
					xmlns:ext=\"http://www.depth.ch/2008/ext\">";
		*/
		$path="/";
		//SITE_metadata::buildXML($root[0]->id, $path, "", $root[0]->isocode, $doc);

		$query = "SELECT s.classes_id as root_id FROM #__easysdi_metadata_standard s, #__easysdi_product p WHERE p.metadata_standard_id=s.id and p.id=".$product_id;
		$database->setQuery( $query );
		$root_id = $database->loadResult();
		
		$query = "SELECT * FROM #__easysdi_metadata_classes WHERE id=".$root_id;
		$database->setQuery( $query );
		$root = $database->loadObjectList();
		
		//SITE_metadata::buildXML('4001', "//gmd:MD_Metadata", $path, 'gmd:MD_Metadata', $doc, $XMLDoc, $XMLNode);
		SITE_metadata::buildXMLTree($root_id, $root_id, str_replace(":", "_", $root[0]->iso_key), $XMLDoc, $XMLNode, $path, $root[0]->iso_key, $_POST, $keyVals, $option);
		//$doc=$doc."</gmd:MD_Metadata>";
		
		//echo 'Ecrit : ' . $XMLDoc->save("C:\\RecorderWebGIS\\xml.xml") . ' octets';
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'product.admin.easysdi.php');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		//echo $catalogUrlBase."\\r\\n"; 
		// Supprimer de Geonetwork l'ancienne version de la métadonnée
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
		
		$result = SITE_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
		
		$deleteResults = DOMDocument::loadXML($result);
		$xpathDelete = new DOMXPath($deleteResults);
		$xpathDelete->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		
		$deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;
		$errorMsg = "";
		if ($deleted <> 1)
		{
			$errorMsg = "Metadata has not been deleted. ";
			/* $response = '{
				    		success: false,
						    errors: {
						        xml: "'.$errorMsg.'"
						    }
						}';
			print_r($response);
			die();*/ 
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
			
		$result = SITE_metadata::PostXMLRequest($catalogUrlBase, $xmlstr);
		//echo $result;
		$insertResults = DOMDocument::loadXML($result);
		
		$xpathInsert = new DOMXPath($insertResults);
		$xpathInsert->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		
		$inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;
		
		if ($inserted <> 1)
		{
			$errorMsg .= "Metadata has not been inserted.";
			$response = '{
				    		success: false,
						    errors: {
						        xml: "'.$errorMsg.'"
						    }
						}';
			print_r($response);
			die();
		}
		else
		{
			//$result="";
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			//SITE_metadata::cswTest($xmlstr);
			$response = '{
				    		success: true,
						    errors: {
						        xml: "OK"
						    }
						}';
			print_r($response);
			die();
		}
	}

	function PostXMLRequest($url,$xmlBody){
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
	
		function deleteMetadataList($cid,$option){
			
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$mdList = new MDList( $database );
			$mdList->load( $id );
					
			if (!$mdList->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			}
			
			$query ="delete from #__easysdi_metadata_list_content where list_id = $id";
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );	
				exit();		
				}
			
		
		}
			
			
		}
		
		
function deleteMetadataListContent($cid,$option){
			
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$mdList = new MDListContent( $database );
			$mdList->load( $id );
					
			if (!$mdList->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			}
			
			
		
		}
			
			
		}
	
function deleteMDTabs($cid,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit;
		}
		foreach( $cid as $id )
		{
			$mdTabs = new MDTabs( $database );
			$mdTabs->load( $id );
					
			if (!$mdTabs->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			}														
		
		}

		
	}
	
	
	function saveMDTabs($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
							
		$row =&	 new MDTabs($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit();
		}	
	}
	
	
	function editMDTabs($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDTabs( $db );
		
		$row->load( $id );					
	
		
		HTML_metadata::editMetadataTabs($row,$id, $option );
		
	}
	
	function listMetadataTabs($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_tabs ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_tabs order by id";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
			
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listMetadataTabs($use_pagination,$rows,$pageNav,$option);		
	}
	
	
	function deleteMDStandardClasses($cid,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit;
		}
		foreach( $cid as $id )
		{
			$standardClasses = new MDStandardClasses( $database );
			$standardClasses->load( $id );
					
			if (!$standardClasses->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			}														
		
		}

		
	}
	
	function validateMetadata(){
		
		$xdoc = new DomDocument;
		$xmlfile = 'http://www.ecadastre.public.lu/Portail/getIso19115.do?format=XML&id=171';
		$xmlschema = 'D:/DEPTH/Projets/projets/eclipse/workspace/jaxb/bin/schemas-all/iso/19139/20070417/gmd/gmd.xsd';

		$xdoc->Load($xmlfile);
		echo "OK";
		if ($xdoc->schemaValidate($xmlschema)) {
			echo "$xmlfile is valid.\n";
			} else {
				echo "$xmlfile is invalid.\n";
		}

	}
	function saveMDStandardClasses($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
							
		$row =&	 new MDStandardClasses($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit();
		}	
	}
	
	
	function editStandardClasses($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDStandardClasses( $db );
		
		$row->load( $id );
		if ($id == 0)	{				
			$row->standard_id = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		}
		HTML_metadata::editStandardClasses($row,$id, $option );
		
	}
	
	function listStandardClasses($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',1);		
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		$user = JFactory::getUser();
		$partner = new partnerByUserId($db);
		$partner->load($user->id);		
		
		
		if ($type == ''){
			
			$query  = "SELECT id AS value FROM #__easysdi_metadata_standard WHERE is_deleted =0 AND (partner_id in (SELECT partner_id FROM #__easysdi_community_partner where  root_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id) OR  partner_id = ( SELECT root_id FROM #__easysdi_community_partner where partner_id=$partner->partner_id)  OR root_id = $partner->partner_id OR  partner_id = $partner->partner_id))";
			$db->setQuery( $query ,1,1); 
			 $type = $db->loadResult();
		}
		if ($type){
		$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select a.id as id, b.name as standard_name , c.name as class_name ,a.position from  #__easysdi_metadata_standard_classes a ,#__easysdi_metadata_standard b,#__easysdi_metadata_classes c  where b.is_deleted =0 AND b.id=a.standard_id and c.id = a.class_id and standard_id = $type order by standard_name,position";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		
		}
		HTML_metadata::listStandardClasses($use_pagination,$rows,$pageNav,$option,$type);
		
	}
	
	
	function saveMDStandard($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$row =&	 new MDStandard($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
	}
	
	
	function editStandard($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDStandard( $db );
		
		$row->load( $id );					
	
		
		
		HTML_metadata::editStandard($row,$id, $option );
		
	}
	
	function listStandard($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_standard WHERE is_deleted =0 ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_standard WHERE is_deleted =0 ";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listStandard($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	
	function saveMDLocfreetext($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDFreetext =&	 new MDLocFreetext($database);
				
		
		if (!$rowMDFreetext->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
			exit();
		}				
	if (!$rowMDFreetext->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
			exit();
		}
	}
	
	
	function editExt($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDExt = new MDExt( $db );
		
		$rowMDExt->load( $id );					
	
		
		
		HTML_metadata::editExt($rowMDExt,$id, $option );
		
	}
	
	function editLocfreetext($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDFreetext = new MDLocFreetext( $db );
		
		$rowMDFreetext->load( $id );					
	
		
		
		HTML_metadata::editLocfreetext($rowMDFreetext,$id, $option );
		
	}
	
	function listExt($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_ext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_ext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
					
		}		

		HTML_metadata::listExt($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	
	function listLocfreetext($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_loc_freetext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_loc_freetext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
					
		}		

		HTML_metadata::listLocfreetext($use_pagination,$rows,$pageNav,$option);
		
	}
	
function listClass($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_classes ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_classes ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listClass($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	function saveMDClass($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDClasses =&	 new MDClasses($database);
				
		
		if (!$rowMDClasses->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
			exit();
		}				
		if (!$rowMDClasses->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
			exit();
		}

		
		//delete the links  
		$query = "DELETE FROM  #__easysdi_metadata_classes_classes WHERE classes_from_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}
			$query = "DELETE FROM  #__easysdi_metadata_classes_freetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
	
			$query = "DELETE FROM  #__easysdi_metadata_classes_locfreetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
		$query = "DELETE FROM  #__easysdi_metadata_classes_list WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
		
						
		if ($_POST[type]=='class'){
			
			foreach( $_POST['class'] as $class_id ) {
				
				$query = "INSERT INTO #__easysdi_metadata_classes_classes VALUES (0,".$rowMDClasses->id.",".$class_id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}

		
		if ($_POST[type]=='list'){
			
			foreach( $_POST['list'] as $class_id ) {
				
				$query = "INSERT INTO #__easysdi_metadata_classes_list VALUES (0,".$rowMDClasses->id.",".$class_id.")";
			
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}
		
		
		if ($_POST[type]=='freetext'){
			foreach( $_POST['freetext'] as $id ) {
				$query = "INSERT INTO #__easysdi_metadata_classes_freetext VALUES (0,".$rowMDClasses->id.",".$id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}			
		if ($_POST[type]=='locfreetext'){
			foreach( $_POST['locfreetext'] as $id ) {
				$query = "INSERT INTO #__easysdi_metadata_classes_locfreetext VALUES (0,".$rowMDClasses->id.",".$id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}			
	
	
	
		
	}
	
	function editClass($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDClasses = new MDClasses( $db );
		$rowMDClasses->load( $id );					
	
		
		
		HTML_metadata::editClass($rowMDClasses,$id, $option );
		
	}
	
	
	
	
	
	
	
	function saveMDListContent($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDList =&	 new MDListContent($database);
				
		
		if (!$rowMDList->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataListContent" );
			exit();
		}				
	if (!$rowMDList->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataListContent" );
			exit();
		}
	}

	
	
	function saveMDList($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDList =&	 new MDList($database);
				
		
		if (!$rowMDList->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit();
		}				
	if (!$rowMDList->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit();
		}
	}

	
	
function saveMDFreetext($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDFreetext =&	 new MDFreetext($database);
				
		
		if (!$rowMDFreetext->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
			exit();
		}				
	if (!$rowMDFreetext->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
			exit();
		}
	}
	
	function editFreetext($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDFreetext = new MDFreetext( $db );
		$rowMDFreetext->load( $id );					
	
		
		
		HTML_metadata::editFreetext($rowMDFreetext,$id, $option );
		
	}
	
	
	
	//if id = 0, create a new Entry
	function editList($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDList = new MDList( $db );
		$rowMDList->load( $id );					
	
		
		
		HTML_metadata::editList($rowMDList,$id, $option );
		
	}
	
	function editListContent($id,$option,$list_id){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDList = new MDListContent( $db );
		$rowMDList->load( $id );					
	
		
		
		HTML_metadata::editListContent($rowMDList,$id, $option,$list_id );
		
	}
	
function listFreetext($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_freetext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_freetext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listFreetext($use_pagination,$rows,$pageNav,$option);
		
	}
	
	function listDate($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_date ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_date ";
	if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listDate($use_pagination,$rows,$pageNav,$option);
		
	}
	
function listList($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_list ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_list ";
if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listList($use_pagination,$rows,$pageNav,$option);
		
	}
	
	

	


function listListContent($list_id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_list_content where list_id = $list_id ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_list_content where list_id = $list_id ";
if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listListContent($use_pagination,$rows,$pageNav,$option,$list_id);
		
	}
	
	
}

?>