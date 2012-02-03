<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâééééArche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
	
	/**
	 * 
	 */
	function getCSWresult ()
	{
		$database =& JFactory::getDBO();
		$id = JRequest::getVar('id');
		$metadata_guid = $id;
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=COMPLETE&id=".$metadata_guid;
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		return $cswResults;
	}
	
	/**
	 * 
	 */
	function getMetadata(&$xml)
	{	
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		$xml = "";
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
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
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		if ($type == "abstract")
		{
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract.xsl');
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
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			$title = "";
			
			$titleQuery = "  SELECT o.name 
						 FROM #__sdi_metadata m
						 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						 WHERE m.guid = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			
			//select selected properties
			$selected = array();
			$database->setQuery("SELECT id FROM #__sdi_metadata WHERE guid=".$id);
			$prod_id = $database->loadResult();
			$query = "SELECT propertyvalue_id as value FROM #__sdi_product_property WHERE product_id=".$prod_id;	
			$database->setQuery( $query );
			$selected = $database->loadResultArray();
			
			if ($database->getErrorNum()) {						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
			}
			
			$product = new product( $database );
			$product->load( $prod_id );
			
			//Version
			$version_id = JRequest::getVar('objectversion_id', 0 );
			if($version_id == 0)
			{
				$version_id = $product->objectversion_id;
			}
			$version = new objectversion($database);
			$version->load($version_id);
			
			$supplier = new account($database);
			$object = new object ($database);
			$object_id = JRequest::getVar('object_id', 0 );
			if($object_id==0)
			{
				$object_id=$version->object_id;
			}
			else
			{
				if($object_id <> $version->object_id)
				{
					$version_id=0;
					$version->load($version_id);
				}
			}
			if($object_id<>0)
			{
				
				$object->load($object_id);
				$supplier->load($object->account_id);
			}
			
			$objecttype_id = JRequest::getVar('objecttype_id', 0 );
			if($objecttype_id==0)
			{
				if($object_id<>0)
				{
					$objecttype_id=$object->objecttype_id;
				}
			}
			else
			{
				if($objecttype_id <>$object->objecttype_id)
				{
					$object_id=0;
					$object->load($object_id);
					$supplier->load(0);
					$version_id=0;
					$version->load($version_id);
				}
			}
			
			$lang =& JFactory::getLanguage();
			$condition="";
			if($supplier->id)
			{
				$condition = " OR p.account_id = $supplier->id ";
			}
			
			$queryProperties = "SELECT p.id as property_id, 
									   t.label as text,
									   p.type as type,
									   p.mandatory as mandatory 
								FROM #__sdi_language l, 
									#__sdi_list_codelang cl,
									#__sdi_property p 
									LEFT OUTER JOIN #__sdi_translation t ON p.guid=t.element_guid 
								WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$lang->_lang."' 
								AND p.published =1 
								AND (p.account_id = 0 $condition)
								order by p.ordering";
			$database->setQuery( $queryProperties);
			$propertiesList = $database->loadObjectList();
			if ($database->getErrorNum()) 
			{						
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
			}
			
			$doc .= '<Properties isProductPublished="'.$product->published.'" count="'.count($propertiesList).'">';
			foreach ($propertiesList as $curProperty)
			{
				$propertiesValueList = array();
				$query = "SELECT a.id as value, t.label as text 
							FROM 
								#__sdi_list_codelang cl,
								#__sdi_language l,
								#__sdi_propertyvalue a 
							LEFT OUTER JOIN #__sdi_translation t ON a.guid=t.element_guid 
						WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$lang->_lang."' 
							AND a.property_id =".$curProperty->property_id." 
							order by a.ordering";				 
				$database->setQuery( $query );
				$propertiesValueList = $database->loadObjectList();
				if ($database->getErrorNum()) 
				{						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}
				
				$count = 0;
				foreach($propertiesValueList as $propertyValue){
				   	if (in_array($propertyValue->value, $selected)){
				   		$count ++;
				   	}
				}
				
				if($count > 0){
				   $doc .= '<Property>';
				   $doc .= '<PropertyName>'.$curProperty->text.'</PropertyName>';
				   foreach($propertiesValueList as $propertyValue){
				   	if (in_array($propertyValue->value, $selected)){
				   		$doc .= '<PropertyValue>';
				   		$doc .= '<value>'.$propertyValue->text.'</value>';
				   		$doc .= '</PropertyValue>';
				   	}
				   }
				   $doc .= '</Property>';
				}
			}
			$doc .= '</Properties>';
			$doc .= '</Diffusion></Metadata>';
						
			$xml = new DomDocument();
			$xml->loadXML($doc);
		}	
		
	}
	
	/**
	 * 
	 */
	function showMetadata()
	{	
		JHTML::_('behavior.modal'); 
		
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		$xml = "";
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
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
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		if ($type == "abstract")
		{
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl');
			}
			else
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract.xsl');
			}
			
			$xml = displayManager::getCSWresult();
			displayManager::DisplayMetadata($style,$xml);
		}
		else if ($type == "complete")
		{
			$style = new DomDocument();
			
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete.xsl');
			}
			
			$xml = displayManager::getCSWresult();
			
			displayManager::DisplayMetadata($style,$xml);
		}
		else if ($type == "diffusion")
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'object.easysdi.class.php');
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'core'.DS.'model'.DS.'objectversion.easysdi.class.php');
			
			$title;
			
			$database->setQuery("SELECT id FROM #__sdi_metadata WHERE guid=".$id);
			$prod_id = $database->loadResult();
			
			$titleQuery = "  SELECT o.name 
						 FROM #__sdi_metadata m
						 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						 WHERE m.guid = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			
			//select selected properties
			$selected = array();
			$query = "SELECT propertyvalue_id as value FROM #__sdi_product_property WHERE product_id=".$prod_id;	
			$database->setQuery( $query );
			$selected = $database->loadResultArray();
			
			if ($database->getErrorNum()) {						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
			}
			
			$product = new product( $database );
			$product->load( $prod_id );
			
			//Version
			$version_id = JRequest::getVar('objectversion_id', 0 );
			if($version_id == 0)
			{
				$version_id = $product->objectversion_id;
			}
			$version = new objectversion($database);
			$version->load($version_id);
			
			$supplier = new account($database);
			$object = new object ($database);
			$object_id = JRequest::getVar('object_id', 0 );
			if($object_id==0)
			{
				$object_id=$version->object_id;
			}
			else
			{
				if($object_id <> $version->object_id)
				{
					$version_id=0;
					$version->load($version_id);
				}
			}
			if($object_id<>0)
			{
				
				$object->load($object_id);
				$supplier->load($object->account_id);
			}
			
			$objecttype_id = JRequest::getVar('objecttype_id', 0 );
			if($objecttype_id==0)
			{
				if($object_id<>0)
				{
					$objecttype_id=$object->objecttype_id;
				}
			}
			else
			{
				if($objecttype_id <>$object->objecttype_id)
				{
					$object_id=0;
					$object->load($object_id);
					$supplier->load(0);
					$version_id=0;
					$version->load($version_id);
				}
			}
			
			$lang =& JFactory::getLanguage();
			$condition="";
			if($supplier->id)
			{
				$condition = " OR p.account_id = $supplier->id ";
			}
			
			$queryProperties = "SELECT p.id as property_id, 
									   t.label as text,
									   p.type as type,
									   p.mandatory as mandatory 
								FROM #__sdi_language l, 
									#__sdi_list_codelang cl,
									#__sdi_property p 
									LEFT OUTER JOIN #__sdi_translation t ON p.guid=t.element_guid 
								WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$lang->_lang."' 
								AND p.published =1 
								AND (p.account_id = 0 $condition)
								order by p.ordering";
			$database->setQuery( $queryProperties);
			$propertiesList = $database->loadObjectList();
			if ($database->getErrorNum()) 
			{						
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
			}
			
			$doc .= '<Properties isProductPublished="'.$product->published.'" count="'.count($propertiesList).'">';
			foreach ($propertiesList as $curProperty)
			{
				$propertiesValueList = array();
				$query = "SELECT a.id as value, t.label as text 
							FROM 
								#__sdi_list_codelang cl,
								#__sdi_language l,
								#__sdi_propertyvalue a 
							LEFT OUTER JOIN #__sdi_translation t ON a.guid=t.element_guid 
						WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$lang->_lang."' 
							AND a.property_id =".$curProperty->property_id." 
							order by a.ordering";				 
				$database->setQuery( $query );
				$propertiesValueList = $database->loadObjectList();
				if ($database->getErrorNum()) 
				{						
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");					 			
				}
				
				$count = 0;
				foreach($propertiesValueList as $propertyValue){
				   	if (in_array($propertyValue->value, $selected)){
				   		$count ++;
				   	}
				}
				
				if($count > 0){
				   $doc .= '<Property>';
				   $doc .= '<PropertyName>'.$curProperty->text.'</PropertyName>';
				   foreach($propertiesValueList as $propertyValue){
				   	if (in_array($propertyValue->value, $selected)){
				   		$doc .= '<PropertyValue>';
				   		$doc .= '<value>'.$propertyValue->text.'</value>';
				   		$doc .= '</PropertyValue>';
				   	}
				   }
				   $doc .= '</Property>';
				}
			}
			$doc .= '</Properties>';
			$doc .= '</Diffusion></Metadata>';
			
			$document = new DomDocument();
			$document->loadXML($doc);
			
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl');
			}
			else
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion.xsl');
			}
			displayManager::DisplayMetadata($style,$document);
		}	
		
	}
	
	/**
	 * 
	 */
	function showAbstractMetadata()
	{	
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
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
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$style = new DomDocument();
		// Test des différentes combinaisons possibles pour le nom de fichier, en allant
		// de la plus restrictive é la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract.xsl');
		}
			
		$xml = displayManager::getCSWresult();
		displayManager::DisplayMetadata($style,$xml);
	}
	
	/**
	 * 
	 */
	function showCompleteMetadata ()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
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
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$style = new DomDocument();
		// Test des différentes combinaisons possibles pour le nom de fichier, en allant
		// de la plus restrictive é la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete.xsl');
		}
		
		$xml = displayManager::getCSWresult();
		
		displayManager::DisplayMetadata($style,$xml);
	}
	
	/**
	 * 
	 */
	function showDiffusionMetadata ()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
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
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		$title;
		
		$titleQuery = "  SELECT o.name 
						 FROM #__sdi_metadata m
						 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						 WHERE m.guid = '".$id."'";
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
		// de la plus restrictive é la plus basique
		if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl'))
		{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl');
		}
		else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl')){
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl');
		}
		else{
			$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion.xsl');
		}
		
		displayManager::DisplayMetadata($style,$document);
	}
	
	/**
	 * 
	 */
	function DisplayMetadata ($xslStyle, $xml)
	{
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$type = JRequest::getVar('type', 'abstract');
		$id = JRequest::getVar('id');
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		$toolbar =JRequest::getVar('toolbar',1);
		$print =JRequest::getVar('print',0);
		$buttonsHtml="";
		$menuLinkHtml="";
		$queryAccountLogo;
		$supplier;
		$product_creation_date;
		$product_update_date;
		$shopExist=0;

		// Si la page est appelée depuis un autre environnement que Joomla
		$notJoomlaCall = 'true';
		if (array_key_exists('HTTP_REFERER', $_SERVER))
		{
			// Emplacement depuis lequel l'adresse a été appelée
			$httpReferer = parse_url($_SERVER['HTTP_REFERER']);
			$caller = $httpReferer['scheme']."://".$httpReferer['host'].$httpReferer['path'];
			//echo $caller."<br>";
			
			// Adresse appelée
			$scheme = "http";
			if ($_SERVER['HTTPS'] and $_SERVER['HTTPS'] <> "off")
				$scheme .= "s";
			$current = $scheme."://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
			
			// Si l'adresse courante ne fait pas partie du même site que l'adresse appelante, 
			// on considére que c'est un appel direct
			if ($caller == $current)
				$notJoomlaCall = 'false';
		}
			
		$user = JFactory::getUser();
		
		$current_account = new accountByUserId($db);
		if (!$user->guest){
			$current_account->load($user->id);
		}else{
			$current_account->id = 0;
		}
        		
		// Construction  of supplier,creation date and update date [from EasySDIV1]
		$queryAccountID = "	SELECT a.id 
							FROM #__sdi_metadata m
							INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							INNER JOIN #__sdi_account a ON a.id=o.account_id 
							WHERE m.guid = '".$id."'";
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
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$id."'";
		$db->setQuery($query);
		$product_creation_date = $db->loadResult();
		
		$query = "	SELECT o.updated 
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$id."'";
		$db->setQuery($query);
		$product_update_date = $db->loadResult();
		if ($product_update_date == '0000-00-00 00:00:00')
			$product_update_date = '-';
				
		$query = "	SELECT count(*) 
					FROM #__sdi_list_module 
					WHERE code='SHOP'";
		$db->setQuery($query);
		$shopExist = $db->loadResult();
		if($shopExist)
		{
			$query = " SELECT count(*) FROM #__sdi_product p
										INNER JOIN #__sdi_objectversion ov on ov.id = p.objectversion_id
										INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
										WHERE m.guid = '$id'";
			$db->setQuery($query);
			$shopExist = $db->loadResult();
		}
		

		//Defines if the corresponding product is orderable.
		$hasOrderableProduct = false;
		
		$processor = new xsltProcessor();

		$context = JRequest::getVar('context');
		
		if ($type <> 'diffusion')
			$xml = displayManager::constructXML($xml, $db, $language, $id, $notJoomlaCall, $type, $context);
			
		$processor->importStylesheet($xslStyle);
		$xmlToHtml = $processor->transformToXml($xml);
		

		$myHtml = "<script type=\"text/javascript\" src=\"/administrator/components/com_easysdi_core/common/date.js\"></script>";
		// Toolbar build from EasySDIV1
		if ($toolbar==1){
			$buttonsHtml .= "<table align=\"right\"><tr align='right'>";
			$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTPDF")."\" id=\"exportPdf\" onclick=\"window.open('./index.php?tmpl=component&option=com_easysdi_core&task=exportPdf&id=$id&type=$type', '_self');\"> </div></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTXML")."\" id=\"exportXml\" onclick=\"window.open('./index.php?tmpl=component&format=raw&option=com_easysdi_core&task=exportXml&id=$id&type=$type', '_self');\"> </div></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_PRINTMD")."\" id=\"printMetadata\" onclick=\"window.open('./index.php?tmpl=component&option=$option&task=$task&id=$id&type=$type&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> </div></td>";
			if ($shopExist)
				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_ORDERPRODUCT")."\" id=\"orderProduct\" onclick=\"window.open('./index.php?option=com_easysdi_shop&task=shop', '_parent');\"> </div></td>";
			
			$buttonsHtml .= "</tr></table>";		
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
							if(document.getElementById('catalogPanel1')!= undefined){
								document.getElementById('catalogPanel1').addEvent( 'click' , function() { 
									window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=abstract', '_self');
								});
							}
							if(document.getElementById('catalogPanel2')!= undefined){
								document.getElementById('catalogPanel2').addEvent( 'click' , function() { 
									window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=complete', '_self');
								});
							}
						task = '$task';
						type = '$type';
						
						";
			/* Onglet diffusion, si et seulement si le shop est installé et que l'objet est diffusable*/
			if ($shopExist)
			{
				$myHtml .= "
				if(document.getElementById('catalogPanel3')!= undefined){
					document.getElementById('catalogPanel3').addEvent( 'click' , function() { 
						window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=diffusion', '_self');
					});
					document.getElementById('catalogPanel3').className = 'closed';
					
					if(task == 'showMetadata' & type == 'diffusion'){
		        		document.getElementById('catalogPanel3').className = 'open';
					}
				}
				";
			}
			
			/* Boutons */
			$myHtml .= "
			if(document.getElementById('catalogPanel1')!= undefined){
				document.getElementById('catalogPanel1').className = 'closed';
				if(task == 'showMetadata' & type == 'abstract'){
		        	document.getElementById('catalogPanel1').className = 'open';
				}
			}
			if(document.getElementById('catalogPanel2')!= undefined){
				document.getElementById('catalogPanel2').className = 'closed';
				if(task == 'showMetadata' & type == 'complete'){
		        	document.getElementById('catalogPanel2').className = 'open';
				}
			}
			
			
			
			});\n"; 
		
			$myHtml .= "</script>";

		}
		
		//Workaround to avoid printf problem with text with a "%", must
		//be changed to "%%".
		$xmlToHtml = str_replace("%", "%%", $xmlToHtml);
		$xmlToHtml = str_replace("__ref_", "%", $xmlToHtml);
				
		$myHtml .= $xmlToHtml;
		
		// Construction  of creation date, update date and account logo [from EasySDIV1]
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
			
		$img='<img width="$'.$logoWidth.'" height="'.$logoHeight.'" src="'.$account_logo.'">';
		printf($myHtml, $img, $supplier, $product_creation_date, $product_update_date, $buttonsHtml, $menuLinkHtml, $notJoomlaCall);
		
	}
	
	/**
	 * 
	 */
	function buildXHTML ($xslStyle, $xml)
	{
//		$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$type = JRequest::getVar('type', 'abstract');
		$id = JRequest::getVar('id');
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		$toolbar =JRequest::getVar('toolbar',1);
		$print =JRequest::getVar('print',0);
		$buttonsHtml="";
		$menuLinkHtml="";
		$queryAccountLogo;
		$supplier;
		$product_creation_date;
		$product_update_date;
		$shopExist=0;
		
		// Si la page est appelée depuis un autre environnement que Joomla
		//print_r($_SERVER);echo "<br>";
		$notJoomlaCall = 'true';
		if (array_key_exists('HTTP_REFERER', $_SERVER))
		{
			// Emplacement depuis lequel l'adresse a été appelée
			$httpReferer = parse_url($_SERVER['HTTP_REFERER']);
			$caller = $httpReferer['scheme']."://".$httpReferer['host'].$httpReferer['path'];
			//echo $caller."<br>";
			
			// Adresse appelée
			$scheme = "http";
			if ($_SERVER['HTTPS'] and $_SERVER['HTTPS'] <> "off")
				$scheme .= "s";
			$current = $scheme."://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
			//echo $current;
			
			// Si l'adresse courante ne fait pas partie du méme site que l'adresse appelante, 
			// on considére que c'est un appel direct
			if ($caller == $current)
				$notJoomlaCall = 'false';
		}
			
		$db =& JFactory::getDBO();
		
		$user = JFactory::getUser();
		$current_account = new accountByUserId($db);
		if (!$user->guest){
			$current_account->load($user->id);
		}else{
			$current_account->id = 0;
		}
        		
		// Construction  of supplier,creation date and update date [from EasySDIV1]
		$queryAccountID = "	SELECT a.id 
							FROM #__sdi_metadata m
							INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							INNER JOIN #__sdi_account a ON a.id=o.account_id 
							WHERE m.guid = '".$id."'";
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
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$id."'";
		$db->setQuery($query);
		$product_creation_date = $db->loadResult();
		//$product_creation_date = date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		$query = "	SELECT o.updated 
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$id."'";
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
		if($shopExist)
		{
			$query = " SELECT count(*) FROM #__sdi_product p
										INNER JOIN #__sdi_objectversion ov on ov.id = p.objectversion_id
										INNER JOIN #__sdi_metadata m ON m.id = ov.metadata_id
										WHERE m.guid = '$id'";
			$db->setQuery($query);
			$shopExist = $db->loadResult();
		}
		//Defines if the corresponding product is orderable.
		$hasOrderableProduct = false;
		
		//load favorites
//		$optionFavorite;
//		$metadataListArray = array();
//		if($current_account->id == 0)
//			$optionFavorite = false;
//		else if ($enableFavorites == 1){
//			$query = "SELECT m.guid FROM #__sdi_metadata m WHERE m.id IN (SELECT metadata_id FROM #__sdi_favorite WHERE account_id = $current_account->id)";
//			$db->setQuery($query);
//			$metadataListArray = $db->loadResultArray();
//			if ($db->getErrorNum()) {						
//						echo "<div class='alert'>";			
//						echo 			$db->getErrorMsg();
//						echo "</div>";
//			}
//		}
//		$isFavorite = 1;
//		if(!in_array($id, $metadataListArray) && $enableFavorites == 1 && !$user->guest)
//			$isFavorite = 0;
		
		$context = JRequest::getVar('context');
		if ($type <> 'diffusion')
			$xml = displayManager::constructXML($xml, $db, $language, $id, $notJoomlaCall, $type, $context);
		
//		echo htmlspecialchars($xml->saveXML())."<br>";break;
		
		$processor = new xsltProcessor();
		$processor->importStylesheet($xslStyle);
		$xmlToHtml = $processor->transformToXml($xml);
		
		$myHtml = "<script type=\"text/javascript\" src=\"/media/system/js/mootools.js\"></script>";
		// Toolbar build from EasySDIV1
		if ($toolbar==1){
			$buttonsHtml .= "<table align=\"right\"><tr align='right'>";
//			if(!in_array($id, $metadataListArray) && $enableFavorites == 1 && !$user->guest)
//				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ADD_TO_FAVORITE")."\" id=\"toggleFavorite\" class=\"addFavorite\" onclick=\"favoriteManagment();\"> </div></td>";
//			if(in_array($id, $metadataListArray) && $enableFavorites == 1 && !$user->guest)
//				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_REMOVE_FAVORITE")."\" id=\"toggleFavorite\" class=\"removeFavorite\" onclick=\"favoriteManagment();\"> </div></td>";
			$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTPDF")."\" id=\"exportPdf\" onclick=\"window.open('./index.php?tmpl=component&option=com_easysdi_core&task=exportPdf&id=$id&type=$type', '_self');\"> </div></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTXML")."\" id=\"exportXml\" onclick=\"window.open('./index.php?tmpl=component&format=raw&option=com_easysdi_core&task=exportXml&id=$id&type=$type', '_self');\"> </div></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_PRINTMD")."\" id=\"printMetadata\" onclick=\"window.open('./index.php?tmpl=component&option=$option&task=$task&id=$id&type=$type&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"> </div></td>";
			if ($shopExist)
				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_ORDERPRODUCT")."\" id=\"orderProduct\" onclick=\"window.open('./index.php?option=com_easysdi_shop&task=shop', '_parent');\"> </div></td>";
			
			$buttonsHtml .= "</tr></table>";		
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
			
//			$myHtml .="
//			function favoriteManagment ()
//			{
//			  var action = \"addFavorite\";
//			   var title = Array();
//			   title['CORE_REMOVE_FAVORITE']='".JText::_("CORE_REMOVE_FAVORITE")."';
//			   title['CORE_ADD_TO_FAVORITE']='".JText::_("CORE_ADD_TO_FAVORITE")."';
//				   
//			   if(document.getElementById('toggleFavorite').className == \"removeFavorite\")
//			      action = \"removeFavorite\";
//				   
//			   var req = new Ajax('./index.php?option=com_easysdi_shop&task='+action+'&view=&metadata_guid=$id', {
//		           	method: 'get',
//		           	onSuccess: function(){
//				        if(document.getElementById('toggleFavorite').className == \"removeFavorite\"){
//		           		   document.getElementById(\"toggleFavorite\").className = 'addFavorite';
//			   		   document.getElementById(\"toggleFavorite\").title = title['EASYSDI_ADD_TO_FAVORITE'];
//					}else{
//					   document.getElementById(\"toggleFavorite\").className = 'removeFavorite';
//			   		   document.getElementById(\"toggleFavorite\").title = title['EASYSDI_REMOVE_FAVORITE'];
//					}
//		           	},
//		           	onFailure: function(){
//		           		
//		           	}
//		           }).request();		
//				
//			}";
			
//			//Manage display class
//			/* Onglets abstract et complete*/
//			$myHtml .= "window.addEvent('domready', function() {
//			
//			document.getElementById('catalogPanel1').addEvent( 'click' , function() { 
//				window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=abstract', '_self');
//			});
//			document.getElementById('catalogPanel2').addEvent( 'click' , function() { 
//				window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=complete', '_self');
//			});
//			
//			task = '$task';
//			type = '$type';
//			
//			";
//			/* Onglet diffusion, si et seulement si le shop est installé et que l'objet est diffusable*/
//			if ($shopExist)
//			{
//				$myHtml .= "
//				document.getElementById('catalogPanel3').addEvent( 'click' , function() { 
//					window.open('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=diffusion', '_self');
//				});
//				
//				document.getElementById('catalogPanel3').className = 'closed';
//				
//				if(task == 'showMetadata' & type == 'diffusion'){
//	        		document.getElementById('catalogPanel3').className = 'open';
//				}
//				";
//			}
			
			/* Boutons */
//			$myHtml .= "
//
//			
//	
//			document.getElementById('catalogPanel1').className = 'closed';
//			document.getElementById('catalogPanel2').className = 'closed';
//			
//			if(task == 'showMetadata' & type == 'abstract'){
//	        	document.getElementById('catalogPanel1').className = 'open';
//			}
//			if(task == 'showMetadata' & type == 'complete'){
//	        	document.getElementById('catalogPanel2').className = 'open';
//			}
//			});\n"; 
		
			$myHtml .= "</script>";

		}
		
		//Workaround to avoid printf problem with text with a "%", must
		//be changed to "%%".
		$xmlToHtml = str_replace("%", "%%", $xmlToHtml);
		$xmlToHtml = str_replace("__ref_", "%", $xmlToHtml);
		$myHtml .= $xmlToHtml;
		
		// Construction  of creation date, update date and account logo [from EasySDIV1]
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
		
		
		//printf($myHtml, $img, $supplier, $product_creation_date, $product_update_date, $buttonsHtml, $menuLinkHtml, $notJoomlaCall);
		return $myHtml;
	}
	
	/**
	 * 
	 */
	function exportXml()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
			$database->setQuery("SELECT xsldirectory FROM #__sdi_context WHERE code='".$context."'");
			$xslFolder = $database->loadResult(); 
		}
		if ($xslFolder <> "")
			$xslFolder = $xslFolder."/";
		
		
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("SELECT ot.code 
							 FROM #__sdi_metadata m
							 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							 INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		if ($type == 'abstract')
		{
			$style = new DomDocument();
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XML_abstract.xsl');
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
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Pragma: public');
		header("Expires: 0"); 
		header("Content-Length: ".strlen($file)); // Attention, très important que la taille soit juste, sinon IE pos problème
		
		echo $file;
		//Very important, if you don't call this, the content-type will have no effect
		die();
	}
	
	/**
	 * 
	 */
	function exportPdf(){
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
			$database->setQuery("SELECT xsldirectory FROM #__sdi_context WHERE code='".$context."'");
			$xslFolder = $database->loadResult(); 
		}
		if ($xslFolder <> "")
			$xslFolder = $xslFolder."/";
		
		
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("SELECT ot.code 
							 FROM #__sdi_metadata m
							 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							 INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		//$cswResults = new DomDocument();
		//displayManager::getMetadata($cswResults);
		
		$processor = new xsltProcessor();
		$style = new DomDocument();
		
		if ($type == 'abstract')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_abstract.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_abstract.xsl');
			}
		}
		else if ($type == 'complete')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_complete.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_complete.xsl');
			}
		}
		else if ($type == 'diffusion')
		{
			// Test des différentes combinaisons possibles pour le nom de fichier, en allant
			// de la plus restrictive é la plus basique
			if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion_'.$language.'.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl'))
			{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_'.$objecttype.'_diffusion.xsl');
			}
			else if (file_exists(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl')){
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion_'.$language.'.xsl');
			}
			else{
				$style->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XML2XHTML_diffusion.xsl');
			}
		}
		
		//$processor->importStylesheet($style);
		//$myHtml = $processor->transformToXml($cswResults);
		$myHTML = displayManager::buildXHTML($style, $cswResults);
		displayManager::exportPDFfile($myHTML);
	}
	
	/**
	 * 
	 */
	function exportPDFfile($myHtml) 
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		//$language = $user->getParam('language', '');
		$lg = &JFactory::getLanguage();
		$language = $lg->_lang;
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$id = JRequest::getVar('id');
		
		// Répertoire des fichiers xsl, s'il y en a un
		$context = JRequest::getVar('context');
		$xslFolder = ""; 
		
		if (isset($context))
		{
			$database->setQuery("SELECT xsldirectory FROM #__sdi_context WHERE code='".$context."'");
			$xslFolder = $database->loadResult(); 
		}
		if ($xslFolder <> "")
			$xslFolder = $xslFolder."/";
		
		
		//$cswResults = new DomDocument();
		//displayManager::getMetadata($cswResults);
		
		// Récupérer le type d'objet
		$database->setQuery("SELECT ot.code 
							 FROM #__sdi_metadata m
							 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							 INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							 INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
							 WHERE m.guid='".$id."'");
		$objecttype = $database->loadResult();
		
		$supplier;
		$product_creation_date;
		$product_update_date;
		
		$db =& JFactory::getDBO();
		$queryAccountID = "SELECT o.account_id 
						   FROM #__sdi_metadata m
						   INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						   INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						   INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
						   WHERE m.guid = '".$id."'";
		$db->setQuery($queryAccountID);
		$account_id = $db->loadResult();
		
		$queryAccountLogo = "select logo from #__sdi_account where id = ".$account_id;
		$db->setQuery($queryAccountLogo);
		$account_logo = $db->loadResult();
		
		//$query="select CONCAT( CONCAT( ad.agentfirstname, ' ' ) , ad.agentlastname ) AS name from #__sdi_account a inner join #__sdi_address ad on a.id = ad.account_id WHERE ad.account_id = ".$account_id ." and ad.type_id=1" ;
		$query="select u.name from #__sdi_account a inner join #__users u on a.user_id = u.id WHERE a.id = ".$account_id;
   		$db->setQuery($query);
		$supplier= $db->loadResult();
			
		$query = "SELECT m.created 
				  FROM #__sdi_metadata m
				  INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				  INNER JOIN #__sdi_object o ON o.id = ov.object_id 
				  INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
			      WHERE m.guid = '".$id."'";
		$db->setQuery($query);
		$temp = $db->loadResult();
		$object_creation_date = date("d-m-Y H:i:s", strtotime($temp));
		
		$query = "SELECT m.updated 
				  FROM #__sdi_metadata m
				  INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				  INNER JOIN #__sdi_object o ON o.id = ov.object_id 
				  INNER JOIN #__sdi_objecttype ot ON ot.id=o.objecttype_id 
			      WHERE m.guid = '".$id."'";
		$db->setQuery($query);
		$temp = $db->loadResult();
		$object_update_date = $temp == '0000-00-00 00:00:00' ? '-' : date("d-m-Y H:i:s", strtotime($temp));
		
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");		
		$img='<img width="'.$logoWidth.'" height="'.$logoHeight.'" src="'.JPATH_BASE.DS.$account_logo.'"/>';
		
		$myHtml = str_replace("%1\$s", $img, $myHtml);
		$myHtml = str_replace("%2\$s", $supplier, $myHtml);
		$myHtml = str_replace("%3\$s", $object_creation_date, $myHtml);
		$myHtml = str_replace("%4\$s", $object_update_date, $myHtml);
		$myHtml = str_replace("%5\$s", "", $myHtml);
		$myHtml = str_replace("%6\$s", "", $myHtml);
		
		if($myHtml == "")
			$myHtml = "<div/>";
		
		$document  = new DomDocument();	
		$document ->load(dirname(__FILE__).'/../xsl/'.$xslFolder.'XHTML2FO.xsl');
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
				
				unlink($foptmp);
			
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
	
	/**
	 * 
	 */
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
	
	/**
	 * 
	 */
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
		
	/**
	 * 
	 */
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
	
	/**
	 * 
	 */
	function constructXML($xml, $db, $language, $fileIdentifier, $notJoomlaCall, $type, $context)
	{
		$doc = new DomDocument('1.0', 'UTF-8');
		$doc->formatOutput = true;
	
		$root = $xml->getElementsByTagName("MD_Metadata");
		$root = $root->item(0);
		$gmdRoot = $doc->importNode($root, true);
		
		$XMLNewRoot = $doc->createElement("Metadata");
		$doc->appendChild($XMLNewRoot);
		$XMLNewRoot->appendChild($gmdRoot);
	
		$XMLSdi = $doc->createElementNS('http://www.depth.ch/sdi', 'sdi:Metadata');
		$XMLSdi->setAttribute('user_lang', $language->_lang);
		$XMLSdi->setAttribute('call_from_joomla', (int)!$notJoomlaCall);
		$XMLNewRoot->appendChild($XMLSdi);
	
		$queryAccountID = "	select o.account_id 
							FROM #__sdi_metadata m
							INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							INNER JOIN #__sdi_object o ON o.id = ov.object_id 
							WHERE m.guid = '".$fileIdentifier."'";
		$db->setQuery($queryAccountID);
		$account_id = $db->loadResult();
	
		if ($account_id <> "")
		{
			$queryAccountLogo = "select logo from #__sdi_account where id = ".$account_id;
			$db->setQuery($queryAccountLogo);
			$account_logo = $db->loadResult();
		}
		else
		{
			$account_logo = "";
		}
		
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
	
		// Nom du fournisseur
		$query="SELECT u.name 
				FROM #__sdi_account a 
				INNER JOIN #__users u on a.user_id = u.id 
				WHERE a.id = ".$account_id;
   		$db->setQuery($query);
   		$supplier= $db->loadResult();
		
		// Créer une entrée pour la config
		$XMLConfig = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:config");
		$XMLDescrLength = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:DescriptionLength", config_easysdi::getValue("DESCRIPTION_LENGTH"));
		$XMLConfig->appendChild($XMLDescrLength);
		$XMLSdi->appendChild($XMLConfig);
		
		
   		// Créer une entrée pour le compte
		$XMLAccount = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:account");
		// Créer une entrée pour le logo du compte
		$XMLALogo = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:logo", $account_logo);
		$XMLALogo->setAttribute('width', $logoWidth);
		$XMLALogo->setAttribute('height', $logoHeight);
		$XMLAccount->appendChild($XMLALogo);
		// Créer une entrée pour le nom du fournisseur de l'objet
		$XMLASupplier = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:supplier", $supplier);
		$XMLAccount->appendChild($XMLASupplier);
		$XMLSdi->appendChild($XMLAccount);
		
		// Récupérer les informations de base sur l'objet, sa version et sa métadonnée
		$object=array();
		$queryObject = "	select o.id, o.name, ov.title, v.code as metadata_visibility 
							FROM #__sdi_metadata m
							INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
							INNER JOIN #__sdi_object o ON o.id = ov.object_id
							INNER JOIN #__sdi_list_visibility v ON v.id = o.visibility_id 
							WHERE m.guid = '".$fileIdentifier."'";
		$db->setQuery($queryObject);
		$object = $db->loadObject();
		
		//Récupérer les managers de l'objet
		$managerList=array();
		$queryManager = "	select u.email, u.name
							FROM #__sdi_object o
							INNER JOIN #__sdi_manager_object m ON o.id = m.object_id
							INNER JOIN #__sdi_account a ON a.id = m.account_id
							INNER JOIN #__users u ON a.user_id = u.id
							WHERE o.id = ".$object->id;
		$db->setQuery($queryManager);
		$managerList = $db->loadObjectList();
		
		// Date de création de la métadonnée
   		$query = "	SELECT m.created 
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$fileIdentifier."'";
		$db->setQuery($query);
		$creation_date = $db->loadResult();
		
		// Derniére mise é jour de la métadonnée
		$query = "	SELECT m.updated 
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
					INNER JOIN #__sdi_object o ON o.id = ov.object_id 
					WHERE m.guid = '".$fileIdentifier."'";
		$db->setQuery($query);
		$update_date = $db->loadResult();
		
		// Modify objectversion_title, creation_date and updated_date
		// to construct an XML valid date
		$explodeDate = array();
		$explodeDate = explode(" ", $object->title);
		$object->title = $explodeDate[0]."T".$explodeDate[1]; 
		if ($creation_date and $creation_date <> "")
		{
			$explodeDate = explode(" ", $creation_date);
			$creation_date = $explodeDate[0]."T".$explodeDate[1];
		} 
		if ($update_date and $update_date <> "")
		{
			$explodeDate = explode(" ", $update_date);
			$update_date = $explodeDate[0]."T".$explodeDate[1];
		} 
		
		// Créer une entrée pour l'objet
		$XMLObject = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:object");
		if ($object)
		{
			$XMLObject->setAttribute('object_name', $object->name);
			$XMLObject->setAttribute('objectversion_title', $object->title);
			$XMLObject->setAttribute('metadata_visibility', $object->metadata_visibility);
			$XMLObject->setAttribute('metadata_created', $creation_date);
			$XMLObject->setAttribute('metadata_updated', $update_date);
		}
		else
		{
			$XMLObject->setAttribute('object_name', '');
			$XMLObject->setAttribute('objectversion_title', '');
			$XMLObject->setAttribute('metadata_visibility', '');
			$XMLObject->setAttribute('metadata_created', '0000-00-00T00:00:00');
			$XMLObject->setAttribute('metadata_updated', '0000-00-00T00:00:00');
		}
		$XMLSdi->appendChild($XMLObject);
		
		// Récupérer le type d'objet
		$objecttype = array();
		// Récupérer le logo du type d'objet
		$queryObjecttype = "SELECT ot.code, t.label, ot.logo 
							FROM #__sdi_objecttype ot
							INNER JOIN #__sdi_object o ON o.objecttype_id=ot.id
							INNER JOIN #__sdi_objectversion ov ON ov.object_id=o.id
							INNER JOIN #__sdi_metadata m ON m.id=ov.metadata_id
							INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
							INNER JOIN jos_sdi_language l ON t.language_id=l.id
							INNER JOIN jos_sdi_list_codelang cl ON l.codelang_id=cl.id
							WHERE m.guid = '".$fileIdentifier."'
								  AND cl.code = '".$language->_lang."'";
		$db->setQuery($queryObjecttype);
		$objecttype = $db->loadObject();
		
		// Créer une entrée pour le type d'objet
		if ($objecttype)
		{
			$XMLObjectType = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:objecttype", $objecttype->label);
			$XMLObjectType->setAttribute('code', $objecttype->code);
			$XMLObjectType->setAttribute('logo_path', $objecttype->logo);
			$XMLObjectType->setAttribute('logo_width', $logoWidth);
			$XMLObjectType->setAttribute('logo_height', $logoHeight);
		}
		else
		{
			$XMLObjectType = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:objecttype");
			$XMLObjectType->setAttribute('code', '');
			$XMLObjectType->setAttribute('logo_path', '');
			$XMLObjectType->setAttribute('logo_width', '');
			$XMLObjectType->setAttribute('logo_height', '');
		}
		$XMLSdi->appendChild($XMLObjectType);
		
		// Entrées à ajouter si le shop est installé
		$shopExist=0;
		$query = "	SELECT count(*) 
					FROM #__sdi_list_module 
					WHERE code='SHOP'";
		$db->setQuery($query);
		$shopExist = $db->loadResult();
		
		if ($shopExist == 1)
		{
			require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'controller'.DS.'shop.easysdi.class.php');
			$product = array();
			$queryProduct = "	SELECT p.id, p.free, p.available, p.published, pf.size, pf.type 
								FROM #__sdi_metadata m
								INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
								INNER JOIN #__sdi_product p ON p.objectversion_id = ov.id 
								LEFT OUTER JOIN #__sdi_product_file pf ON pf.product_id = p.id 
								WHERE m.guid = '".$fileIdentifier."'";
			$db->setQuery($queryProduct);
			$product = $db->loadObject();
		
			// Créer une entrée pour le produit, avec comme attributs la gratuité, la disponibilité et l'état de publication
			// et si l'utilisateur à le droit de commande le produit
			if ($product)
			{
				$XMLProduct = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:product", $product->id);
				$XMLProduct->setAttribute('published', (int)$product->published);
				$XMLProduct->setAttribute('available', (int)$product->available);
				$XMLProduct->setAttribute('free', (int)$product->free);
				$XMLProduct->setAttribute('file_size', $product->size);
				$XMLProduct->setAttribute('size_unit', 'Byte');
				$XMLProduct->setAttribute('file_type', $product->type);
				
				if(SITE_shop::getProductListCount() > 0)
					$XMLProduct->setAttribute('orderable', 1);
				else
					$XMLProduct->setAttribute('orderable', 0);
				
			}
			else
			{
				$XMLProduct = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:product");
				$XMLProduct->setAttribute('published', (int)0);
				$XMLProduct->setAttribute('available', (int)0);
				$XMLProduct->setAttribute('free', (int)0);
				$XMLProduct->setAttribute('file_size', '');
				$XMLProduct->setAttribute('size_unit', '');
				$XMLProduct->setAttribute('file_type', '');
			}
			
			$XMLSdi->appendChild($XMLProduct);
		}
		
		//Ajoute les actions disponibles
		$XMLAction = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:action");
		
		//Export PDF
		$XMLActionPDF = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:exportPDF");
		$XMLActionPDF->setAttribute('id', 'exportPdf');
		$XMLActionPDFLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_core&task=exportPdf&id='.$fileIdentifier.'&type='.$type.'&context='.$context)));
		$XMLActionPDF->appendChild($XMLActionPDFLink);
		
		//Make PDF
		$XMLActionmPDF = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:makePDF");
		$XMLActionmPDF->setAttribute('id', 'makePdf');
		$XMLActionmPDFLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?option=com_easysdi_catalog&task=getReport&format=makepdf&reporttype='.$type.'&lastVersion=yes&language='.$language->_lang.'&metadata_guid='.$fileIdentifier.'&metadatatype='.$type.'&context='.$context)));
		$XMLActionmPDF->appendChild($XMLActionmPDFLink);
		
		//Export XML
		$XMLActionXML = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:exportXML");
		$XMLActionXML->setAttribute('id', 'exportXml');
		$XMLActionXMLLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?format=raw&option=com_easysdi_core&task=exportXml&id='.$fileIdentifier.'&type='.$type.'&context='.$context)));
		$XMLActionXML->appendChild($XMLActionXMLLink);
		
		//Print
		$XMLActionPrint = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:print");
		$XMLActionPrint->setAttribute('id', 'printMetadata');
		$XMLActionPrintLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_core&task=printMetadata&id='.$fileIdentifier.'&type='.$type.'&context='.$context.'&toolbar=0&print=1')));
		$XMLActionPrint->appendChild($XMLActionPrintLink);
		
		$XMLAction->appendChild($XMLActionPDF);
		$XMLAction->appendChild($XMLActionmPDF);
		$XMLAction->appendChild($XMLActionXML);
		$XMLAction->appendChild($XMLActionPrint);
		
		if ($shopExist == 1)
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'model'.DS.'product.easysdi.class.php');
			//Order
			$XMLActionOrder = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:order");
			$XMLActionOrder->setAttribute('id', 'orderProduct');
			$XMLActionOrderLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?option=com_easysdi_shop&task=shop')));
			$XMLActionOrder->appendChild($XMLActionOrderLink);
			$XMLAction->appendChild($XMLActionOrder);
			
			if ($product)
			{
				$product_object = new product ($db);
				$product_object->load($product->id);
				$productFileName = $product_object->getFileName();
				
				$user = JFactory::getUser();
				$account = new accountByUserId( $db );
				$account->load( $user->id );
				if($product_object->published && $product_object->available && strlen($productFileName) > 0 && $product_object->isUserAllowedToLoad($account->id)){
					//Link to download the product 
					$XMLActionDownloadProduct = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:downloadProduct");
					$XMLActionDownloadProduct->setAttribute('id', 'downloadProduct');
					$XMLActionDownloadProductLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_shop&task=downloadAvailableProduct&cid='.$product_object->id.'&toolbar=0&print=1')));
					$XMLActionDownloadProduct->appendChild($XMLActionDownloadProductLink);
					$XMLAction->appendChild($XMLActionDownloadProduct);
				} else if($product_object->published && $product_object->available && strlen($productFileName) > 0 && !$product_object->isUserAllowedToLoad($account->id)){
					//Contact to extend user rights  
					$XMLActionDownloadProductRight = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:downloadProductRight");
					$XMLActionDownloadProductRight->setAttribute('id', 'downloadProductRight');
					$tooltipString = "";
					foreach ($managerList as $value) {
						$tooltipString .= $value->name." (".$value->email."), ";
					}
					$tooltipString = substr($tooltipString, 0,strlen($tooltipString)-2);
					
					$XMLActionDownloadProductLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:tooltip", $tooltipString);
					$XMLActionDownloadProductRight->appendChild($XMLActionDownloadProductLink);
					$XMLAction->appendChild($XMLActionDownloadProductRight);
				}
				$query = "select count(*) from #__sdi_product p 
										where p.viewurlwms != '' AND p.id = $product->id";
				$db->setQuery( $query);
				$hasPreview = $db->loadResult();
				if ($db->getErrorNum()) {
					$hasPreview = 0;
				}
				
				if($product_object->published && $hasPreview && $product_object->isUserAllowedToView($account->id)){
					//link to preview the product
					$query = "select ov.metadata_id from #__sdi_objectversion ov 
										INNER JOIN #__sdi_product p ON p.objectversion_id=ov.id 
										where  p.id = $product->id";
					$db->setQuery( $query);
					$metadata_id = $db->loadResult();
					$XMLActionPreviewProduct = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:previewProduct");
					$XMLActionPreviewProduct->setAttribute('id', 'previewProduct'); 
					$XMLActionPreviewProductLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_shop&task=previewProduct&metadata_id='.$metadata_id.'&toolbar=0&print=1')));
					$XMLActionPreviewProduct->appendChild($XMLActionPreviewProductLink);
					$XMLAction->appendChild($XMLActionPreviewProduct);
				}
			}
		}
		$XMLSdi->appendChild($XMLAction);
		
		//Ajoute les onglets disponibles
		$XMLTabs = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:tab");
		
		$XMLTabAbstract = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:abstract");
		$XMLTabAbstract->setAttribute('id', 'catalogPanel1');
		$XMLTabAbstract->setAttribute('name', JText::_("CORE_ABSTRACT_TAB"));
		$XMLTabAbstractLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id='.$fileIdentifier.'&type=abstract'.'&context='.$context)));
		$XMLTabAbstract->appendChild($XMLTabAbstractLink);
		
		$XMLTabComplete = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:complete");
		$XMLTabComplete->setAttribute('id', 'catalogPanel2');
		$XMLTabComplete->setAttribute('name', JText::_("CORE_COMPLETE_TAB"));
		$XMLTabCompleteLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id='.$fileIdentifier.'&type=complete'.'&context='.$context)));
		$XMLTabComplete->appendChild($XMLTabCompleteLink);
		
		$XMLTabDiffusion = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:diffusion");
		$XMLTabDiffusion->setAttribute('id', 'catalogPanel3');
		$XMLTabDiffusion->setAttribute('name', JText::_("CORE_DIFFUSION_TAB"));
		$XMLTabDiffusionLink = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:link", htmlentities(JRoute::_('./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=$id&type=diffusion'.'&context='.$context)));
		$XMLTabDiffusion->appendChild($XMLTabDiffusionLink);
		
		$XMLTabs->appendChild($XMLTabAbstract);
		$XMLTabs->appendChild($XMLTabComplete);
		$XMLTabs->appendChild($XMLTabDiffusion);
		$XMLSdi->appendChild($XMLTabs);
		
		// Stockage des liens parents et enfants
		$rowMetadata = new metadataByGuid($db);
		$rowMetadata->load($fileIdentifier);
		$rowObjectVersion = new objectversionByMetadata_id($db);
		$rowObjectVersion->load($rowMetadata->id);
			
		$childs=array();
		$query = "SELECT m.guid as metadata_guid, o.name as objectname, ot.code as objecttype
				 FROM #__sdi_metadata m
				 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				 INNER JOIN #__sdi_object o ON ov.object_id = o.id
				 INNER JOIN #__sdi_objecttype ot ON o.objecttype_id = ot.id
				 INNER JOIN #__sdi_objectversionlink ovl ON ov.id = ovl.child_id
				 WHERE ovl.parent_id=".$rowObjectVersion->id;
		$db->setQuery($query);
		$childs = $db->loadObjectList();
		
		$parents=array();
		$query = "SELECT m.guid as metadata_guid, o.name as objectname, ot.code as objecttype
				 FROM #__sdi_metadata m
				 INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
				 INNER JOIN #__sdi_object o ON ov.object_id = o.id
				 INNER JOIN #__sdi_objecttype ot ON o.objecttype_id = ot.id
				 INNER JOIN #__sdi_objectversionlink ovl ON ov.id = ovl.parent_id
				 WHERE ovl.child_id=".$rowObjectVersion->id;
		$db->setQuery($query);
		$parents = $db->loadObjectList();
		
		$XMLLinks = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:links");
		foreach ($childs as $c)
		{
			$XMLChild = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:child");
			$XMLChild->setAttribute('metadata_guid', $c->metadata_guid);
			$XMLChild->setAttribute('object_name', $c->objectname);
			$XMLChild->setAttribute('objecttype', $c->objecttype);
			$XMLLinks->appendChild($XMLChild);
		}

		foreach ($parents as $p)
		{
			$XMLParent = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:parent");
			$XMLParent->setAttribute('metadata_guid', $p->metadata_guid);
			$XMLParent->setAttribute('object_name', $p->objectname);
			$XMLParent->setAttribute('objecttype', $p->objecttype);	
			$XMLLinks->appendChild($XMLParent);
		}
		$XMLSdi->appendChild($XMLLinks);
		
		// Stockage des applications externes
		$apps=array();
		$query = "SELECT *
				 FROM #__sdi_application a
				 WHERE a.object_id=".$object->id;
		$db->setQuery($query);
		$apps = $db->loadObjectList();
		
		$XMLExternalApp = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:externalapplications");
		foreach ($apps as $a)
		{
			$XMLApp = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:application");
			$XMLAppName = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:name", $a->name);
			$XMLAppWindowname = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:windowname", $a->windowname);
			$XMLAppUrl = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:url", $a->url);
			$XMLAppOptions = $doc->createElementNS('http://www.depth.ch/sdi', "sdi:options", $a->options);
			
			$XMLApp->appendChild($XMLAppName);
			$XMLApp->appendChild($XMLAppWindowname);
			$XMLApp->appendChild($XMLAppUrl);
			$XMLApp->appendChild($XMLAppOptions);
			
			$XMLExternalApp->appendChild($XMLApp);
		}
		$XMLSdi->appendChild($XMLExternalApp);
			
		//$doc->save("C:/tmp/temp1.xml");
		                     
		return $doc;
	}
	
	/**
	 *  Add Itemid and lang to the url
	 */
	function buildUrl($url)
	{
		if (JRequest::getVar('Itemid') and JRequest::getVar('Itemid') <> "")
			$url = $url."&Itemid=".JRequest::getVar('Itemid');
		if (JRequest::getVar('lang') and JRequest::getVar('lang') <> "")
			$url = $url."&lang=".JRequest::getVar('lang');
		return $url;
	}
}

?>