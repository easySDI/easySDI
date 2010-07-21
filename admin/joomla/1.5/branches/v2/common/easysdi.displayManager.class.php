<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dÃ¢â‚¬â„¢Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class displayManager{
	
	function getCSWresult ()
	{
		$database =& JFactory::getDBO();
		
		$id = JRequest::getVar('id');
		/*$metadata_guid = "select guid from #__sdi_metadata WHERE id=".$id;
		$database->setQuery($metadata_guid);
		$metadata_guid = $database->loadResult();*/
		$metadata_guid = $id;
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=".$metadata_guid;
		
		/*
		$id=158;
		$catalogUrlBase = "https://geoproxy.asitvd.ch/ogc/geonetwork";
		$catalogUrlCapabilities = "https://geoproxy.asitvd.ch/ogc/geonetwork?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = "https://geoproxy.asitvd.ch/ogc/geonetwork?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=158";	
		*/
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		return $cswResults;
	}
	
	function getMetadata(&$xml)
	{	
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		$xml = "";
		
		$id = JRequest::getVar('id');
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		if ($type == "abstract")
		{
			
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_abstract.xsl');
			}
			
			
			$fullxml = displayManager::getCSWresult();
			$xml = new DomDocument();
			
			$processor = new xsltProcessor();
			$processor->importStylesheet($style);
			$xml = $processor->transformToDoc($fullxml);
		}
		else if ($type == "complete")
		{
			$xml = new DomDocument();
			$xml = displayManager::getCSWresult();
		}
		else if ($type == "diffusion")
		{
			$title = "";
			
			$titleQuery = "select o.name from #__sdi_object o, #__sdi_metadata m where o.metadata_id=m.id AND m.guid = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			/*$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef 
						from #__easysdi_product_properties_definition 
						INNER JOIN 
						(select property_value_id, #__easysdi_product_properties_values_definition.text,properties_id 
								from #__easysdi_product_property 
								INNER JOIN #__easysdi_product_properties_values_definition 
								ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
	 							where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."')) T 
	 					ON #__easysdi_product_properties_definition.id=T.properties_id";
			
			$database->setQuery($query);
			$rows = $database->loadObjectList();		
			foreach ($rows as $row)
			{
				
				$doc .= "<Property><PropertyName>$row->PropDef</PropertyName>";
				
				
				$subQuery = "SELECT  #__easysdi_product_properties_definition.text as PropDef, T.text as ValueDef 
						  from #__easysdi_product_properties_definition 
						  INNER JOIN 
						  (select property_value_id, #__easysdi_product_properties_values_definition.text,properties_id 
						 	from #__easysdi_product_property 
							INNER JOIN #__easysdi_product_properties_values_definition 
						 	ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
	 						where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."') ) T 
	 					 ON #__easysdi_product_properties_definition.id=T.properties_id 
	 					 Where #__easysdi_product_properties_definition.text = '".addslashes($row->PropDef)."'";
				
				$database->setQuery($subQuery);
				$results = $database->loadObjectList();
				foreach ($results as $result)
				{
					$doc.="<PropertyValue><value>$result->ValueDef</value></PropertyValue>";
				}
				
				$doc.= "</Property>";
			}
			*/
			$doc .= '</Diffusion></Metadata>';
			
			$xml = new DomDocument();
			$xml->loadXML($doc);
		}	
		
	}
	
	function showMetadata()
	{	
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		//print_r($lg); 
		//echo $language;
		
		$type =  JRequest::getVar('type', 'abstract');
		$xml = "";
		
		$id = JRequest::getVar('id');
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		if ($type == "abstract")
		{
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl');
			}
			else
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract.xsl');
			}
			
			$xml = displayManager::getCSWresult();
			displayManager::DisplayMetadata($style,$xml);
		}
		else if ($type == "complete")
		{
			$style = new DomDocument();
			
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete.xsl');
			}
			
			$xml = displayManager::getCSWresult();
			
			displayManager::DisplayMetadata($style,$xml);
		}
		else if ($type == "diffusion")
		{
			$title;
			
			$titleQuery = "select o.name from #__sdi_object o, #__sdi_metadata m where o.metadata_id=m.id AND m.guid = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			
			/*$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef ,
						#__easysdi_product_properties_definition.translation as PropTranslation
						from #__easysdi_product_properties_definition 
						INNER JOIN 
						(select property_value_id, #__easysdi_product_properties_values_definition.text,
								properties_id 
								from #__easysdi_product_property 
								INNER JOIN #__easysdi_product_properties_values_definition 
								ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
	 							where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."')) T 
	 					ON #__easysdi_product_properties_definition.id=T.properties_id
	 					WHERE #__easysdi_product_properties_definition.published = 1 ";
			
			$database->setQuery($query);
			$rows = $database->loadObjectList();		
			foreach ($rows as $row)
			{
				$valueProp = JText::_($row->PropTranslation);
				$doc .= "<Property><PropertyName>$valueProp</PropertyName>";
				
				
				$subQuery = "SELECT  #__easysdi_product_properties_definition.text as PropDef, T.translation as ValueDef 
						  from #__easysdi_product_properties_definition 
						  INNER JOIN 
						  (select property_value_id, #__easysdi_product_properties_values_definition.text,
						  #__easysdi_product_properties_values_definition.translation,properties_id 
						 	from #__easysdi_product_property 
							INNER JOIN #__easysdi_product_properties_values_definition 
						 	ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
	 						where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."') ) T 
	 					 ON #__easysdi_product_properties_definition.id=T.properties_id 
	 					 Where #__easysdi_product_properties_definition.text = '".addslashes($row->PropDef)."'";
				
				$database->setQuery($subQuery);
				$results = $database->loadObjectList();
				foreach ($results as $result)
				{
					$value = JText::_($result->ValueDef);
					$doc.="<PropertyValue><value>$value</value></PropertyValue>";
				}
				
				$doc.= "</Property>";
			}
		*/
			$doc .= '</Diffusion></Metadata>';
			
			$document = new DomDocument();
			$document->loadXML($doc);
			
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl');
			}
			else
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion.xsl');
			}
			
			displayManager::DisplayMetadata($style,$document);
		}	
		
	}
	
	function showAbstractMetadata()
	{	
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$style = new DomDocument();
		// Test des différentes combinaisons possibles pour le nom de fichier, en allant
		// de la plus restrictive à la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract.xsl');
		}
			
		$xml = displayManager::getCSWresult();
		displayManager::DisplayMetadata($style,$xml);
	}
	function showCompleteMetadata ()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$style = new DomDocument();
		// Test des différentes combinaisons possibles pour le nom de fichier, en allant
		// de la plus restrictive à la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete.xsl');
		}
		
		$xml = displayManager::getCSWresult();
		
		displayManager::DisplayMetadata($style,$xml);
	}
	function showDiffusionMetadata ()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		$title;
		
		$titleQuery = "select o.name from #__sdi_object o, #__sdi_metadata m where o.metadata_id=m.id AND m.guid = '".$id."'";
		$database->setQuery($titleQuery);
		$title = $database->loadResult();
		
		$doc = '';
		$doc .= '<?xml version="1.0"?>';
		$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
		$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
		/*$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef 
					from #__easysdi_product_properties_definition 
					INNER JOIN 
					(select property_value_id, #__easysdi_product_properties_values_definition.text,properties_id 
							from #__easysdi_product_property 
							INNER JOIN #__easysdi_product_properties_values_definition 
							ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
 							where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."')) T 
 					ON #__easysdi_product_properties_definition.id=T.properties_id";
		
		$database->setQuery($query);
		$rows = $database->loadObjectList();		
		foreach ($rows as $row)
		{
			
			$doc .= "<Property><PropertyName>$row->PropDef</PropertyName>";
			
			
			$subQuery = "SELECT  #__easysdi_product_properties_definition.text as PropDef, T.translation as ValueDef 
					  from #__easysdi_product_properties_definition 
					  INNER JOIN 
					  (select property_value_id, #__easysdi_product_properties_values_definition.text,properties_id 
					 	from #__easysdi_product_property 
						INNER JOIN #__easysdi_product_properties_values_definition 
					 	ON #__easysdi_product_property.property_value_id=#__easysdi_product_properties_values_definition.id
 						where product_id IN (select id from #__easysdi_product where metadata_id = '".$id."') ) T 
 					 ON #__easysdi_product_properties_definition.id=T.properties_id 
 					 Where #__easysdi_product_properties_definition.text = '".addslashes($row->PropDef)."'";
			
			$database->setQuery($subQuery);
			$results = $database->loadObjectList();
			foreach ($results as $result)
			{
				$doc.="<PropertyValue><value>$result->ValueDef</value></PropertyValue>";
			}
			
			$doc.= "</Property>";
		}
	*/
		$doc .= '</Diffusion></Metadata>';
		
		$document = new DomDocument();
		$document->loadXML($doc);
		
		$style = new DomDocument();
		// Test des différentes combinaisons possibles pour le nom de fichier, en allant
		// de la plus restrictive à la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion.xsl');
		}
		
		displayManager::DisplayMetadata($style,$document);
	}
	
	function DisplayMetadata ($xslStyle, $xml)
	{
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$type = JRequest::getVar('type', 'abstract');
		$id = JRequest::getVar('id');
		$toolbar =JRequest::getVar('toolbar',1);
		$print =JRequest::getVar('print',0);
		$buttonsHtml="";
		$menuLinkHtml="";
		$queryAccountLogo;
		$supplier;
		$product_creation_date;
		$product_update_date;
		$shopExist=0;
		
		$db =& JFactory::getDBO();
		$queryAccountID = "	SELECT a.id 
							FROM #__sdi_account a, #__sdi_object o, #__sdi_metadata m 
							WHERE 	a.id = o.account_id
									AND o.metadata_id=m.id
									AND m.guid = '".$id."'";
		$db->setQuery($queryAccountID);
		$account_id = $db->loadResult();
			
		$queryAccountLogo = "	SELECT logo 
								FROM #__sdi_account 
								WHERE id = ".$account_id;
		$db->setQuery($queryAccountLogo);
		$account_logo = $db->loadResult();
		
		$query="SELECT u.name 
				FROM #__sdi_account a 
				INNER JOIN #__users u on a.user_id = u.id 
				WHERE a.id = ".$account_id;
   		$db->setQuery($query);
   		$supplier= $db->loadResult();
		
		$query = "	SELECT o.created 
					FROM #__sdi_object o, #__sdi_metadata m
					WHERE o.metadata_id=m.id
						  AND m.guid = '".$id."'";
		$db->setQuery($query);
		$product_creation_date = $db->loadResult();
		//$product_creation_date = date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		$query = "	SELECT o.updated 
					FROM #__sdi_object o, #__sdi_metadata m
					WHERE o.metadata_id=m.id
						  AND m.guid = '".$id."'";
		$db->setQuery($query);
		$product_update_date = $db->loadResult();
		//$product_update_date = $temp == '0000-00-00 00:00:00' ? '-' : date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		if ($product_update_date == '0000-00-00 00:00:00')
			$product_update_date = '-';
				
		$query = "	SELECT count(*) 
					FROM #__sdi_list_module 
					WHERE code='SHOP'";
		$db->setQuery($query);
		$shopExist = $db->loadResult();
		
		/*
		//define an array of orderable associated product for the current user
		$orderableProductsMd = null;
		$filter = "";
		$user = JFactory::getUser();
		$partner = new partnerByUserId($db);
		if (!$user->guest){
			$partner->load($user->id);
		}else{
			$partner->partner_id = 0;
		}
        	
		if($partner->partner_id == 0)
		{
			//No user logged, display only external products
			$filter .= " AND (EXTERNAL=1) ";
		}
		else
		{
			//User logged, display products according to users's rights
			if(userManager::hasRight($partner->partner_id,"REQUEST_EXTERNAL"))
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.EXTERNAL=1
					OR
					(p.INTERNAL =1 AND
					(p.partner_id =  $partner->partner_id
					OR
					p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					OR 
					p.partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
					OR
					p.partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
					
					))) ";
				}
				else
				{
					$filter .= " AND (p.EXTERNAL=1) ";
				}
			}
			else
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.INTERNAL =1 AND
					(p.partner_id =  $partner->partner_id
					OR
					p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					OR 
					p.partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id ))
					OR
					p.partner_id  IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id = $partner->partner_id ) 
					)) ";
									
				}
				else
				{
					//no command right
					$filter .= " AND (EXTERNAL = 10 AND INTERNAL = 10) ";
				}
			}
		}
		$query  = "SELECT metadata_id FROM #__easysdi_product p where published=1 and orderable = 1 ".$filter;
		$db->setQuery($query);
		$orderableProductsMd = $db->loadResultArray();
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}*/
		//Defines if the corresponding product is orderable.
		$hasOrderableProduct = false;
		/*if (in_array($id, $orderableProductsMd))
			$hasOrderableProduct = true;*/
			
		/*$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);*/
			

		$processor = new xsltProcessor();
		/*$style = new DomDocument();*/

		/*$user =& JFactory::getUser();
		$language = $user->getParam('language', '');

		if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_iso19115_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_iso19115_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_iso19115.xsl');
		}*/
		
		//$doc .= '<esdi:ID><title>Test titre</title></esdi:ID>';
		/*$doc = '<esdi:ID><title>Test titre</title></esdi:ID>';
		
		$document = new DomDocument();
		@$document->loadXML($doc);*/
		$processor->importStylesheet($xslStyle);
		$xmlToHtml = $processor->transformToXml($xml);
		
		$myHtml="";
		if ($toolbar==1){
			$buttonsHtml .= "<table align=\"right\"><tr align='right'>
				<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTPDF")."\" id=\"exportPdf\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTXML")."\" id=\"exportXml\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_PRINTMD")."\" id=\"printMetadata\"/></td>";
			if ($shopExist)
				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_ORDERPRODUCT")."\" id=\"orderProduct\"/></a></td>";
			
			$buttonsHtml .= "</td></tr></table>";		
		}
		if ($print ==1 ){
			$myHtml .= "<script>window.print();</script>";
			
		}
		
		//Affichage des onglets
		if ($print !=1 )
		{
			$index = JRequest::getVar('tabIndex', 0);
			$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));
			
			$menuLinkHtml .= $tabs->startPane("catalogPane");
			$menuLinkHtml .= $tabs->startPanel(JText::_("CORE_ABSTRACT_TAB"),"catalogPanel1");
			$menuLinkHtml .= $tabs->endPanel();
			$menuLinkHtml .= $tabs->startPanel(JText::_("CORE_COMPLETE_TAB"),"catalogPanel2");
			$menuLinkHtml .= $tabs->endPanel();
			if ($shopExist)
			{
				$menuLinkHtml .= $tabs->startPanel(JText::_("CORE_DIFFUSION_TAB"),"catalogPanel3");
				$menuLinkHtml .= $tabs->endPanel();
			}
			$menuLinkHtml .= $tabs->endPane();
			
			//Define links for onclick event
			$myHtml .= "<script>\n";
			
			//Manage display class
			/* Onglets abstract et complete*/
			$myHtml .= "window.addEvent('domready', function() {
			
			document.getElementById('catalogPanel1').addEvent( 'click' , function() { 
				window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=abstract', 'w');
			});
			document.getElementById('catalogPanel2').addEvent( 'click' , function() { 
				window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=complete', '_self');
			});
			
			task = '$task';
			type = '$type';
			
			";
			/* Onglet diffusion, si et seulement si le shop est installé et que l'objet est diffusable*/
			if ($shopExist)
			{
				$myHtml .= "
				document.getElementById('catalogPanel3').addEvent( 'click' , function() { 
					window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=diffusion', '_self');
				});
				document.getElementById('orderProduct').addEvent( 'click' , function() { 
					window.open('./index.php?option=com_easysdi_shop&view=shop', '_main');
				});
			
				document.getElementById('catalogPanel3').className = 'closed';
				
				if(task == 'showMetadata' & type == 'diffusion'){
	        		document.getElementById('catalogPanel3').className = 'open';
				}
				";
			}
			
			/* Boutons */
			$myHtml .= "
			document.getElementById('exportPdf').addEvent( 'click' , function() { 
				window.open('./index.php?tmpl=component&option=com_easysdi_core&task=exportPdf&id=$id&type=$type', '_self');
			});
			document.getElementById('exportXml').addEvent( 'click' , function() { 
				window.open('./index.php?tmpl=component&format=raw&option=com_easysdi_core&task=exportXml&id=$id&type=$type', '_self');
			});
			document.getElementById('printMetadata').addEvent( 'click' , function() { 
				window.open('./index.php?tmpl=component&option=$option&task=$task&id=$id&type=$type&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			});
			
	
			document.getElementById('catalogPanel1').className = 'closed';
			document.getElementById('catalogPanel2').className = 'closed';
			
			if(task == 'showMetadata' & type == 'abstract'){
	        	document.getElementById('catalogPanel1').className = 'open';
			}
			if(task == 'showMetadata' & type == 'complete'){
	        	document.getElementById('catalogPanel2').className = 'open';
			}
			});\n"; 
	
			/*if($message != ""){
				$myHtml .= "window.addEvent('domready', function() {
					var divMsg = document.getElementById('message');
					divMsg.style.display='block';
					divMsg.innerHTML='".addslashes($message)."';
				});\n";
			}*/
		
			$myHtml .= "</script>";

		}
		
		//Workaround to avoid printf problem with text with a "%", must
		//be changed to "%%".
		$xmlToHtml = str_replace("%", "%%", $xmlToHtml);
		$xmlToHtml = str_replace("__ref_", "%", $xmlToHtml);
		
		$myHtml .= $xmlToHtml;
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
		
		$temp = explode(" ", $product_creation_date);
		$temp = explode("-", $temp[0]);
		//$product_creation_date = $temp[2].".".$temp[1].".".$temp[0];
		$product_creation_date="";
		$temp = explode(" ", $product_update_date);
		$temp = explode("-", $temp[0]);
		if ($product_update_date <> "-")
			//$product_update_date = $temp[2].".".$temp[1].".".$temp[0];
			$product_update_date="";
		$img='<img width="$'.$logoWidth.'" height="'.$logoHeight.'" src="'.$account_logo.'">';
		printf($myHtml, $img, $supplier, $product_creation_date, $product_update_date, $buttonsHtml, $menuLinkHtml);
		
			
		/***Add consultation informations*/
		/*$db =& JFactory::getDBO();


		$query = "select max(weight)+1 from #__easysdi_product  where metadata_id='$id'";
		$db->setQuery( $query);
		$maxHit = $db->loadResult();
		if ($maxHit){
			$query = "update #__easysdi_product set weight = $maxHit where metadata_id='$id' ";
			$db->setQuery( $query);
			if (!$db->query()) {
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
			}
		}*/
	}
	
	function exportXml()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		if ($type == 'abstract')
		{
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XML_abstract.xsl');
			}
			
			$processor = new xsltProcessor();
			$processor->importStylesheet($style);
			$cswResults = $processor->transformToDoc($cswResults);
		}
		
		$xpath = new DomXPath($cswResults);
		
		if ($type == 'diffusion')
		{
			$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
			$nodes = $xpath->query("//Metadata");
		}
		else
		{
			$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
			$xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
			$nodes = $xpath->query("//gmd:MD_Metadata");
		}

		$dom = new DOMDocument();

		if($nodes->item(0) != "")
		{
			$xmlContent = $dom ->importNode($nodes->item(0),true);
			$dom->appendChild($xmlContent);
		}

		$file = $dom->saveXML();
		error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Content-type: application/xml');
		header('Content-Disposition: attachement; filename="metadata.xml"');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Pragma: public');
		header("Expires: 0"); 
		header("Content-Length: ".filesize($file));
		
		echo $file;
		//Very important, if you don't call this, the content-type will have no effect
		die();
	}
	
	function exportPdf(){
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$cswResults = new DomDocument();
		
		displayManager::getMetadata($cswResults);
		
		$processor = new xsltProcessor();
		$style = new DomDocument();
		
		if ($type == 'abstract')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_abstract.xsl');
			}
		}
		else if ($type == 'complete')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_complete.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_complete.xsl');
			}
		}
		else if ($type == 'diffusion')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive à la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_'.$objecttype.'_diffusion.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/XML2XHTML_diffusion.xsl');
			}
		}
		
		$processor->importStylesheet($style);
		$myHtml = $processor->transformToXml($cswResults);
	
		displayManager::exportPDFfile($myHtml);
	}
	
	function exportPDFfile( $myHtml) 
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("select ot.code from #__sdi_objecttype ot, #__sdi_object o, #__sdi_metadata m WHERE ot.id=o.objecttype_id AND m.id=o.metadata_id AND m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$supplier;
		$product_creation_date;
		$product_update_date;
		
		$db =& JFactory::getDBO();
		$queryAccountID = "select account_id from #__sdi_object o, #__sdi_metadata m where o.metadata_id=m.id AND m.guid = '".$id."'";
		$db->setQuery($queryAccountID);
		$account_id = $db->loadResult();
		
		$queryAccountLogo = "select logo from #__sdi_account where id = ".$account_id;
		$db->setQuery($queryAccountLogo);
		$account_logo = $db->loadResult();
		
		//$query="select CONCAT( CONCAT( ad.agentfirstname, ' ' ) , ad.agentlastname ) AS name from #__sdi_account a inner join #__sdi_address ad on a.id = ad.account_id WHERE ad.account_id = ".$account_id ." and ad.type_id=1" ;
		$query="select u.name from #__sdi_account a inner join #__users u on a.user_id = u.id WHERE a.id = ".$account_id;
   		$db->setQuery($query);
		$supplier= $db->loadResult();
			
		$query = "select created from #__sdi_object where metadata_id = '".$id."'";
		$db->setQuery($query);
		$temp = $db->loadResult();
		$object_creation_date = date("d-m-Y H:i:s", strtotime($temp));
		
		$query = "select updated from #__sdi_object where metadata_id = '".$id."'";
		$db->setQuery($query);
		$temp = $db->loadResult();
		$object_update_date = $temp == '0000-00-00 00:00:00' ? '-' : date("d-m-Y H:i:s", strtotime($temp));
		
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");		
		$img='<img width="'.$logoWidth.'" height="'.$logoHeight.'" src="'.JPATH_BASE.DS.$account_logo.'"/>';
		
		$myHtml = str_replace("__ref_1\$s", $img, $myHtml);
		$myHtml = str_replace("__ref_2\$s", $supplier, $myHtml);
		$myHtml = str_replace("__ref_3\$s", $object_creation_date, $myHtml);
		$myHtml = str_replace("__ref_4\$s", $object_update_date, $myHtml);
		$myHtml = str_replace("__ref_5\$s", "", $myHtml);
		$myHtml = str_replace("__ref_6\$s", "", $myHtml);

		if($myHtml == "")
			$myHtml = "<div/>";
		
		$document  = new DomDocument();	
		$document ->load(dirname(__FILE__).'/../xsl/XHTML2FO.xsl');
		$processor = new xsltProcessor();
		$processor->importStylesheet($document);
		
		//Problem with loadHTML() and encoding : work around method
		$pageDom = new DomDocument();
   		$searchPage = mb_convert_encoding($myHtml, 'HTML-ENTITIES', "UTF-8");
		@$pageDom->loadHTML($searchPage);
		$result = $processor->transformToXml($pageDom);
		//$exportpdf_url = config_easysdi::getValue("EXPORT_PDF_URL");
		$exportpdf_url = config_easysdi::getValue("JAVA_BRIDGE_URL");
		
		if ($exportpdf_url )
		{ 
			$tmp = uniqid();
			$fopcfg = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'config'.DS.'fop.xml';
			$foptmp = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'.pdf';
			$fopfotmp = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'.fo';
			//Check foptmp against the schema before processing
			//avoid JavaBrigde to fail
			
			file_put_contents($fopfotmp, $result);

			//Génération du document PDF sous forme de fichier
			$res = "";
			//Url to the export pdf servlet
			$url = $exportpdf_url."?cfg=fop.xml&fo=$tmp.fo&pdf=$tmp.pdf";
			//echo $url;
			$fp = fopen($url,"r");
			while (!feof($fp)) {
				$res .= fgets($fp, 4096);
			}
			
			//Avoid JVM class caching while testing DO NOT LET THIS FOR PRODUCTION USE!!!!
			//@java_reset();
			if(substr(strtoupper($res),0,7) == "SUCCESS"){
				$fp = fopen ($foptmp, 'r');
				$result = fread($fp, filesize($foptmp));
				fclose ($fp);
					//ob_end_clean();
				
				error_reporting(0);
				ini_set('zlib.output_compression', 0);
                        	
				header('Content-type: application/pdf');
				header('Content-Disposition: attachement; filename="metadata.pdf"');
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
				header('Pragma: public');
				header("Expires: 0"); 
				header("Content-Length: ".filesize($foptmp));
				
				echo $result;
				//Very important, if you don't call this, the content-type will have no effect
				die();
			}else
			{
				//If there was an error when generating the pdf, write it.
				$mainframe->redirect("index.php?option=com_easysdi_core&tmpl=component&task=reportPdfError&res=".urlencode($res));
			}
		}else {
			$mainframe->enqueueMessage(JText::_(  'CORE_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  ),'error'); 
		}
	}
	
	function reportPdfError() //, $timer)
	{
		$res = urldecode(JRequest::getVar('res'));
		
		echo '<div id="metadata" class="contentin">';
		echo '<h2 class="contentheading">'.JText::_('CORE_ERROR_PDF_TITLE').'</h2>';
		echo '<table class="descr">';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td>'.JText::_('CORE_ERROR_PDF_DETAIL').'</td><td>'.$res.'</td></tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td colspan="2">'.JText::_('CORE_ERROR_PDF_REPORT').'</td></tr>';
		echo '</table>';
		echo '</div>';
	}
	
	function convertXML2FO($xml, $xslt, $fo) //, $timer)
	{
	// Transform path of fo and xslt files for javax.xml.transform.stream
	try
	{
		$xml = new java("java.io.File", $xml);
		$xmlFile= $xml->getAbsolutePath();
		$fo = new java("java.io.File", $fo);
		$foFile= $fo->getAbsolutePath();
		$xslt = new java("java.io.File", $xslt);
		$xsltFile= $xslt->getAbsolutePath();
	
		// Setup output
		$out = new java("java.io.FileOutputStream", $fo);
	
 		$xmlSystemId = "http://www.w3.org/TR/2000/REC-xml-20001006.xml";		
		//Setup XSLT
		//fwrite($timer, "\tCréer factory : ".date("H:i:s")."\n");
		$factory = new java("javax.xml.transform.TransformerFactory");
		$factory = $factory->newInstance();
		//fwrite($timer, "\tFactory crée! : ".date("H:i:s")."\n");
		$xsltStream = new java("javax.xml.transform.stream.StreamSource", $xslt);
		//$xsltStream->setSystemId($xmlSystemId);
		//fwrite($timer, "\tCréer transformer : ".date("H:i:s")."\n");
		$transformer = $factory->newTransformer($xsltStream);
		//fwrite($timer, "\tTransformer créé! : ".date("H:i:s")."\n");
		//Setup input for XSLT transformation
		$src = new java("javax.xml.transform.stream.StreamSource", $xml);
		//Resulting SAX events (the generated FO) must be piped through to FOP
		$res = new java("javax.xml.transform.stream.StreamResult", $out);
		//Start XSLT transformation and FOP processing
		//fwrite($timer, "\tTransformation : ".date("H:i:s")."\n");
		$transformer->transform($src, $res);
		//fwrite($timer, "\tTransformation terminée : ".date("H:i:s")."\n");
	}
	catch (JavaException $ex) {
			echo "An exception occured: "; echo $ex; echo "<br>\n";
	}
	if($out != null)
		$out->close();		
	}
		
	function convertFO2PDF($fo, $pdf) //, $timer)
	{
		try
		{
			$fop_mime_constants = new JavaClass('org.apache.fop.apps.MimeConstants');
			// configure fopFactory as desired
			//fwrite($timer, "\tCréer FOP Factory : ".date("H:i:s")."\n");
			$fopFactory = new java("org.apache.fop.apps.FopFactory");
			$fopFactory = $fopFactory->newInstance();
			//fwrite($timer, "\tFOP Factory crée! : ".date("H:i:s")."\n");
			// configure foUserAgent as desired
			//fwrite($timer, "\tCréer FO User Agent: ".date("H:i:s")."\n");
			$foUserAgent = $fopFactory->newFOUserAgent();
			//fwrite($timer, "\tFO User Agent créé! : ".date("H:i:s")."\n");
			// Setup output
			$pdf = new java("java.io.File", $pdf);
			$pdf= $pdf->getAbsolutePath();
				
			$out = new java("java.io.FileOutputStream", $pdf);
			$out = new java("java.io.BufferedOutputStream", $out);
	
			// Construct fop with desired output format
			//fwrite($timer, "\tCréer FOP: ".date("H:i:s")."\n");
			$fop = $fopFactory->newFop($fop_mime_constants->MIME_PDF, $foUserAgent, $out);
			//fwrite($timer, "\tFOP créé! : ".date("H:i:s")."\n");
			//Setup XSLT
			//fwrite($timer, "\tCréer Transformer Factory: ".date("H:i:s")."\n");
			$factory = new java("javax.xml.transform.TransformerFactory");
			$factory = $factory->newInstance();
			$transformer = $factory->newTransformer();
			//fwrite($timer, "\tTransformer Factory créé! : ".date("H:i:s")."\n");
			
			// Set the value of a <param> in the stylesheet
			$transformer->setParameter("versionParam", "2.0");

			//Setup input for XSLT transformation
			$src = new java("javax.xml.transform.stream.StreamSource", $fo);
        
			// Resulting SAX events (the generated FO) must be piped through to FOP
			//fwrite($timer, "\tCréer SAX: ".date("H:i:s")."\n");
			$res = new java("javax.xml.transform.sax.SAXResult", $fop->getDefaultHandler());
			//fwrite($timer, "\tSAX créé! : ".date("H:i:s")."\n");
			//Start XSLT transformation and FOP processing
			//fwrite($timer, "\tTransformation : ".date("H:i:s")."\n");
			$transformer->transform($src, $res);
			//fwrite($timer, "\tTransformation terminée : ".date("H:i:s")."\n");
		}
		catch (JavaException $ex) {
			echo "An exception occured: "; echo $ex; echo "<br>\n";
		}
		if($out != null)
			$out->close();
	}
}

?>