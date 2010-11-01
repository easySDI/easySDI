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

class displayManager{
	
	function getCSWresult ()
	{
		$id = JRequest::getVar('id');
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		
		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&outputSchema=csw:IsoRecord&elementSetName=full&id=".$id;
		
		/*
		$id=158;
		$catalogUrlBase = "https://geoproxy.asitvd.ch/ogc/geonetwork";
		$catalogUrlCapabilities = "https://geoproxy.asitvd.ch/ogc/geonetwork?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = "https://geoproxy.asitvd.ch/ogc/geonetwork?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&id=158";	
		*/
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		
		return $cswResults;
	}
	
	function getMetadata(&$xml)
	{	
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		
		$type =  JRequest::getVar('type', 'abstract');
		$xml = "";
		if ($type == "abstract")
		{
			$xml = new DomDocument();
			$xml = displayManager::getCSWresult();
		}
		else if ($type == "complete")
		{
			$xml = new DomDocument();
			$xml = displayManager::getCSWresult();
		}
		else if ($type == "diffusion")
		{
			$database =& JFactory::getDBO();
			$id = JRequest::getVar('id');
			$title;
			
			$titleQuery = "select data_title from #__easysdi_product where metadata_id = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef 
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
			
			if(count($rows) == 0){
				$doc .= "<Property><PropertyName></PropertyName>";
				if(count($results) == 0){
					$doc.="<PropertyValue><value></value></PropertyValue>";
				}
				$doc.= "</Property>";
			}
			$doc .= '</Diffusion></Metadata>';
			
			//Take care here to replace some non XHTML tags preventing the dom parser to fail
			$doc = str_replace("<br>", "<br/>", $doc);
			
			$xml = new DomDocument();
			$xml->loadXML($doc);
		}	
		
	}
	
	function showMetadata()
	{	
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		
		$type =  JRequest::getVar('type', 'abstract');
		
		$database =& JFactory::getDBO();
		
		// Récupérer le nom du compte root pour cet utilisateur
		$database->setQuery("SELECT a.root_id FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name");
		$root_id = $database->loadResult();	
		if ($root_id == null){
			$database->setQuery("SELECT a.partner_id FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name");
			$root_id = $database->loadResult();
		}
		// Récupérer la norme de ce produit
		$id = JRequest::getVar('id');
		$qry = "SELECT metadata_standard_id FROM #__easysdi_product WHERE metadata_id='".$id."'";
		$database->setQuery($qry);
		$standard_id = $database->loadResult();
		
		//echo $user->get('id')." - ".$root_id." - ".$database->getQuery()." - ".$standard_id;
		
		// Construction des noms possibles du fichier xslt à récupérer
		$baseName = dirname(__FILE__).'/../xsl/'.$type.'.xsl';
		$standardName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'.xsl';
		$rootName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'_'.$root_id.'.xsl';
		$langName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'_'.$root_id.'_'.$language.'.xsl';
		//echo $baseName."<br>".$standardName."<br>".$rootName."<br>".$langName;
		
		$style = new DomDocument();
		// Chargement du bon fichier de transformation XSL 
		if (file_exists($langName))
		{
			$style->load($langName);
			//echo $langName;
		}
		else if (file_exists($rootName))
		{
			$style->load($rootName);
			//echo $rootName;
		}
		else if (file_exists($standardName))
		{
			$style->load($standardName);
			//echo $standardName;
		}
		else
		{
			$style->load($baseName);
			//echo $baseName;
		}
		
		
		if ($type == "abstract")
		{
			/*
			$style = new DomDocument();
			if (file_exists(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl")){
				$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl");
			}else{
				$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract.xsl');
			}
			//$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract.xsl');
			*/
			$xml = displayManager::getCSWresult();
			
			displayManager::DisplayMetadata($style,$xml,"");
		}
		else if ($type == "complete")
		{
			/*
			$style = new DomDocument();
			if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl")){
				$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
			}else{
				$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
			}
			//$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
			*/
			$xml = displayManager::getCSWresult();
			displayManager::DisplayMetadata($style,$xml,"");
		}
		else if ($type == "diffusion")
		{
			//$database =& JFactory::getDBO();
			//$id = JRequest::getVar('id');
			$title;
			
			$titleQuery = "select data_title from #__easysdi_product where metadata_id = '".$id."'";
			$database->setQuery($titleQuery);
			$title = $database->loadResult();
			
			$doc = '';
			$doc .= '<?xml version="1.0"?>';
			$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
			$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
			
			$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef ,
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
			$message = "";
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
			if(count($rows) == 0){
				
				$md_orderable=0;
			        $pOrderableExt = 0;
			        $pOrderableInt = 0;
			        
			        $query = "select external from #__easysdi_product where metadata_id = '".$id."'";
			        $database->setQuery( $query);
			        $pOrderableExt = $database->loadResult();
			        
			        $query = "select internal from #__easysdi_product where metadata_id = '".$id."'";
			        $database->setQuery( $query);
			        $pOrderableInt = $database->loadResult();
			        			
			        if($pOrderableExt == 1 || $pOrderableInt == 1)
			        {
			        	$md_orderable=1;
			        }
				
				if($md_orderable == 1)
				   $message = JText::_("EASYSDI_TEXT_NO_DIFFUSION_PROPERTY");
			        else
				   $message = JText::_("EASYSDI_TEXT_PRODUCT_NOT_DIFFUSED");
			}
			
			$doc .= '</Diffusion></Metadata>';
			
			//Take care here to replace some non XHTML tags preventing the dom parser to fail
			$doc = str_replace("<br>", "<br/>", $doc);
			
			
			$document = new DomDocument();
			$document->loadXML($doc);
			
			
			/*
			$style = new DomDocument();
			if (file_exists(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl")){
				$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl");
			}else{
				$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata.xsl');
			}
			*/
			displayManager::DisplayMetadata($style,$document, $message);
			 
		}	
		
	}
	
	/*
	function showAbstractMetadata()
	{	
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		
		$style = new DomDocument();
		if (file_exists(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract.xsl');
		}
		//$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract.xsl');
		$xml = displayManager::getCSWresult();
		
		displayManager::DisplayMetadata($style,$xml,"");
	}
	function showCompleteMetadata ()
	{
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		
		$style = new DomDocument();
		if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
		}
		//$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
		$xml = displayManager::getCSWresult();
		
		displayManager::DisplayMetadata($style,$xml,"");
	}
	function showDiffusionMetadata ()
	{
		$database =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		$id = JRequest::getVar('id');
		$title;
		
		$titleQuery = "select data_title from #__easysdi_product where metadata_id = '".$id."'";
		$database->setQuery($titleQuery);
		$title = $database->loadResult();
		
		$doc = '';
		$doc .= '<?xml version="1.0"?>';
		$doc .= '<Metadata><Diffusion><fileIdentifier><CharacterString>'.$id.'</CharacterString></fileIdentifier>';
		$doc .= '<gmd:identificationInfo xmlns:gmd="http://www.isotc211.org/2005/gmd"><gmd:MD_DataIdentification><gmd:citation><gmd:CI_Citation><gmd:title><gmd:LocalisedCharacterString>'.$title.'</gmd:LocalisedCharacterString></gmd:title></gmd:CI_Citation></gmd:citation></gmd:MD_DataIdentification></gmd:identificationInfo>';
		$query = "SELECT DISTINCT #__easysdi_product_properties_definition.text as PropDef 
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
	
		$doc .= '</Diffusion></Metadata>';
		
		$document = new DomDocument();
		$document->loadXML($doc);
		
		$style = new DomDocument();
		if (file_exists(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata.xsl');
		}
		
		displayManager::DisplayMetadata($style,$document,"");
	}
	*/
	
	function DisplayMetadata ($xslStyle, $xml, $message)
	{
		$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$type = JRequest::getVar('type', 'abstract');
		$id = JRequest::getVar('id');
		$toolbar =JRequest::getVar('toolbar',1);
		$print =JRequest::getVar('print',0);
		$buttonsHtml;
		$menuLinkHtml;
		$queryPartnerLogo;
		$supplier;
		$product_creation_date;
		$product_update_date;
		
		$db =& JFactory::getDBO();
		$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
			
		$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
			$db->setQuery($queryPartnerLogo);
			$partner_logo = $db->loadResult();
		
		$query="select u.name from #__easysdi_community_partner p inner join #__users u on p.user_id = u.id WHERE p.partner_id = ".$partner_id;
   			$db->setQuery($query);
   			$supplier= $db->loadResult();
			
		$query = "select creation_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$temp = $db->loadResult();
			$product_creation_date = date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		$query = "select metadata_update_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$temp = $db->loadResult();
			$product_update_date = $temp == '0000-00-00 00:00:00' ? '-' : date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		
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
		}
	
		/*$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);*/
			

		$processor = new xsltProcessor();
		/*$style = new DomDocument();*/

		/*$user =& JFactory::getUser();
		$language = $user->getParam('language', '');

		if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
		}*/
		$doc .= '<esdi:ID><title>Test titre</title></esdi:ID>';
		
		$document = new DomDocument();
		@$document->loadXML($doc);
		$processor->importStylesheet($xslStyle);
		$xmlToHtml = $processor->transformToXml($xml);
		$myHtml;
		$myHtml = "<script type=\"text/javascript\" src=\"/media/system/js/mootools.js\"></script>";
		//Defines if the corresponding product is orderable.
		$hasOrderableProduct = false;
		$productId;
		if (in_array($id, $orderableProductsMd))
			$hasOrderableProduct = true;
		//load favorites
		$optionFavorite;
		$productListArray = array();
		if($partner->partner_id == 0)
			$optionFavorite = false;
		else if ($enableFavorites == 1){
			$query = "SELECT id FROM #__easysdi_product p where p.id IN (SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id) and p.published=1";
			//$query = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id ";
			$db->setQuery($query);
			$productListArray = $db->loadResultArray();
			if ($db->getErrorNum()) {						
						echo "<div class='alert'>";			
						echo 			$db->getErrorMsg();
						echo "</div>";
			}
		}
		
		$db->setQuery("select id from #__easysdi_product where metadata_id = '".$id."'");
		$productId = $db->loadResult();
		
		if ($toolbar==1){
			$buttonsHtml .= "<table align=\"right\"><tr align='right'>";
			if(!in_array($productId, $productListArray) && $enableFavorites == 1 && !$user->guest)
				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ADD_TO_FAVORITE")."\" id=\"toggleFavorite\" class=\"addToFavorite\"/></td>";
			if(in_array($productId, $productListArray) && $enableFavorites == 1 && !$user->guest)
				$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_REMOVE_FAVORITE")."\" id=\"toggleFavorite\" class=\"removeFavorite\"/></td>";
			$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTPDF")."\" id=\"exportPdf\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTXML")."\" id=\"exportXml\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_PRINTMD")."\" id=\"printMetadata\"/></td>";
					if($hasOrderableProduct)
						$buttonsHtml .= "<td><div title=\"".JText::_("EASYSDI_ACTION_ORDERPRODUCT")."\" id=\"orderProduct\"/></a></td>";
					$buttonsHtml .= "</td></tr></table>";		
		}
		if ($print ==1 ){
			$myHtml .= "<script>window.addEvent('domready', function() {window.print();});</script>";
		}
		
		//Affichage des onglets
		if ($print !=1 ){
		$index = JRequest::getVar('tabIndex');
		$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));
		
		$menuLinkHtml .= $tabs->startPane("catalogPane");
		$menuLinkHtml .= $tabs->startPanel(JText::_("EASYSDI_METADATA_ABSTRACT_TAB"),"catalogPanel1");
		$menuLinkHtml .= $tabs->endPanel();
		$menuLinkHtml .= $tabs->startPanel(JText::_("EASYSDI_METADATA_COMPLETE_TAB"),"catalogPanel2");
		$menuLinkHtml .= $tabs->endPanel();
		$menuLinkHtml .= $tabs->startPanel(JText::_("EASYSDI_METADATA_DIFFUSION_TAB"),"catalogPanel3");
		$menuLinkHtml .= $tabs->endPanel();
		$menuLinkHtml .= $tabs->endPane();
		
		//Define links for onclick event
		$myHtml .= "<script>\n";
		
		$db->setQuery("SELECT * FROM #__menu where name='GEOCommande'");
		$shopitemId = $db->loadResult();
		
		//Manage display class
		$myHtml .= "
		window.addEvent('domready', function() {
		
		$('catalogPanel1').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=$id&type=abstract', '_self');
		});
		$('catalogPanel2').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=$id&type=complete', '_self');
		});
		$('catalogPanel3').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=$id&type=diffusion', '_self');
		});
		$('exportPdf').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=$option&task=exportPdf&id=$id&type=$type', '_self');
		});
		$('exportXml').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&format=raw&option=$option&task=exportXml&id=$id&type=$type', '_self');
		});
		$('printMetadata').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=$option&task=$task&id=$id&type=$type&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
		});
		";
		if($hasOrderableProduct){
			$myHtml .= "
		$('orderProduct').addEvent( 'click' , function() { 
			window.open('./index.php?option=com_easysdi_shop&view=shop&Itemid=$shopitemId&firstload=1&fromStep=1&cid[]=$productId', '_main');
		});";
		}
		if($enableFavorites == 1 && !$user->guest){
		   //$action = !in_array($productId, $productListArray) ? "addFavorite" : "removeFavorite";
		   //$className = !in_array($productId, $productListArray) ? "removeFavorite" : "addToFavorite";
		   //$title = !in_array($productId, $productListArray) ? "EASYSDI_REMOVE_FAVORITE" : "EASYSDI_ADD_TO_FAVORITE";
		   $myHtml .= "
		   $('toggleFavorite').addEvent( 'click' , function() {
		   var action = \"addFavorite\";
		   var title = Array();
		   title['EASYSDI_REMOVE_FAVORITE']='".JText::_("EASYSDI_REMOVE_FAVORITE")."';
		   title['EASYSDI_ADD_TO_FAVORITE']='".JText::_("EASYSDI_ADD_TO_FAVORITE")."';
		   
		   if($('toggleFavorite').className == \"removeFavorite\")
		      action = \"removeFavorite\";
		   
		   var req = new Ajax('./index.php?option=com_easysdi_shop&task='+action+'&view=&productId=$productId', {
	           	method: 'get',
	           	onSuccess: function(){
			        if($('toggleFavorite').className == \"removeFavorite\"){
	           		   document.getElementById(\"toggleFavorite\").className = 'addToFavorite';
		   		   document.getElementById(\"toggleFavorite\").title = title['EASYSDI_ADD_TO_FAVORITE'];
				}else{
				   document.getElementById(\"toggleFavorite\").className = 'removeFavorite';
		   		   document.getElementById(\"toggleFavorite\").title = title['EASYSDI_REMOVE_FAVORITE'];
				}
	           	},
	           	onFailure: function(){
	           		
	           	}
	           }).request();		
		});";
		}
		$myHtml .= "
		task = '$task';
		type = '$type';
		
		$('catalogPanel1').className = 'closed';
		$('catalogPanel2').className = 'closed';
		$('catalogPanel3').className = 'closed';
		if(task == 'showMetadata' & type == 'abstract'){
        	$('catalogPanel1').className = 'open';
		}
		if(task == 'showMetadata' & type == 'complete'){
        	$('catalogPanel2').className = 'open';
		}
		if(task == 'showMetadata' & type == 'diffusion'){
        	$('catalogPanel3').className = 'open';
		}
		});\n"; 

		if($message != ""){
			$myHtml .= "window.addEvent('domready', function() {
				var divMsg = document.getElementById('message');
				divMsg.style.display='block';
				divMsg.innerHTML='".addslashes($message)."';
			});\n";
		}
		
		$myHtml .= "</script>";

		}

		//Workaround to avoid printf problem with text with a "%", must
		//be changed to "%%".
		$xmlToHtml = str_replace("%", "%%", $xmlToHtml);
		$xmlToHtml = str_replace("__ref_", "%", $xmlToHtml);		
		$myHtml .= $xmlToHtml;
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
		
		$img='<img width="$'.$logoWidth.'" height="'.$logoHeight.'" src="'.$partner_logo.'"/>';
		printf($myHtml, $img, $supplier, $product_creation_date, $product_update_date, $buttonsHtml, $menuLinkHtml);
		
		/***Add consultation informations*/
		$db =& JFactory::getDBO();

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
		}
	}
	
	function exportXml()
	{
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
		$id = JRequest::getVar('id');
		$type = JRequest::getVar('type', 'abstract');
		
		//$cswResults = displayManager::getCSWresult();
		$cswResults = new DomDocument();
		displayManager::getMetadata($cswResults);
		
		if ($type == 'abstract')
		{
			// Récupérer le nom du compte root pour cet utilisateur
			$database =& JFactory::getDBO();
			$database->setQuery( "SELECT a.root_id FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name" );
			$root_id = $database->loadResult();
			
			if ($root_id == null){
				$database->setQuery("SELECT a.partner_id FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name");
				$root_id = $database->loadResult();
			}
			
			// Récupérer la norme de ce produit
			$id = JRequest::getVar('id');
			$database->setQuery( "SELECT metadata_standard_id FROM #__easysdi_product WHERE metadata_id='".$id."'");
			$standard_id = $database->loadResult();
			
			//echo $user->get('id')." - ".$root_id." - ".$database->getQuery()." - ".$standard_id;
			
			// Construction des noms possibles du fichier xslt à récupérer
			$baseName = dirname(__FILE__).'/../xsl/complete-to-abstract.xsl';
			$standardName = dirname(__FILE__).'/../xsl/complete-to-abstract_'.$standard_id.'.xsl';
			$rootName = dirname(__FILE__).'/../xsl/complete-to-abstract_'.$standard_id.'_'.$root_id.'.xsl';
			$langName = dirname(__FILE__).'/../xsl/complete-to-abstract_'.$standard_id.'_'.$root_id.'_'.$language.'.xsl';
			//echo $baseName."<br>".$standardName."<br>".$rootName."<br>".$langName;
			$style = new DomDocument();
			// Chargement du bon fichier de transformation XSL 
			if (file_exists($langName))
			{
				$style->load($langName);
				//echo $langName;
			}
			else if (file_exists($rootName))
			{
				$style->load($rootName);
				//echo $rootName;
			}
			else if (file_exists($standardName))
			{
				$style->load($standardName);
				//echo $standardName;
			}
			else
			{
				$style->load($baseName);
				//echo $baseName;
			}
			/*
			$style = new DomDocument();
			if (file_exists(dirname(__FILE__).'/../xsl/xml-to-xml_iso19115_abstract_'.$language.".xsl")){
				$style->load(dirname(__FILE__).'/../xsl/xml-to-xml_iso19115_abstract_'.$language.".xsl");
			}else{
				$style->load(dirname(__FILE__).'/../xsl/xml-to-xml_iso19115_abstract.xsl');
			}
			*/
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
		$type = JRequest::getVar('type', 'abstract');
		$cswResults = new DomDocument();
		
		displayManager::getMetadata($cswResults);
		
		$processor = new xsltProcessor();
		$style = new DomDocument();

		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');

		// Récupérer le nom du compte root pour cet utilisateur
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT a.root_id FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name" );
		$root_id = $database->loadResult();
		
		if ($root_id == null){
			$database->setQuery("SELECT a.partner_id FROM #__easysdi_community_partner a,#__users b where a.user_id = b.id and b.id=".$user->get('id')." ORDER BY b.name");
			$root_id = $database->loadResult();
		}
		
		// Récupérer la norme de ce produit
		$id = JRequest::getVar('id');
		$database->setQuery( "SELECT metadata_standard_id FROM #__easysdi_product WHERE metadata_id='".$id."'");
		$standard_id = $database->loadResult();
		
		//echo $user->get('id')." - ".$root_id." - ".$database->getQuery()." - ".$standard_id;
		
		// Construction des noms possibles du fichier xslt à récupérer
		$baseName = dirname(__FILE__).'/../xsl/'.$type.'.xsl';
		$standardName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'.xsl';
		$rootName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'_'.$root_id.'.xsl';
		$langName = dirname(__FILE__).'/../xsl/'.$type.'_'.$standard_id.'_'.$root_id.'_'.$language.'.xsl';
		//echo $baseName."<br>".$standardName."<br>".$rootName."<br>".$langName;
		
		// Chargement du bon fichier de transformation XSL 
		if (file_exists($langName))
		{
			$style->load($langName);
			//echo $langName;
		}
		else if (file_exists($rootName))
		{
			$style->load($rootName);
			//echo $rootName;
		}
		else if (file_exists($standardName))
		{
			$style->load($standardName);
			//echo $standardName;
		}
		else
		{
			$style->load($baseName);
			//echo $baseName;
		}
		
		
		/*
		if ($type == 'abstract')
		{
			if (file_exists(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl"))
			{
				$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract_'.$language.".xsl");
			}else
			{
				$style->load(dirname(__FILE__).'/../xsl/iso19115_abstract.xsl');
			}
		}
		else if ($type == 'complete')
		{
			if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl"))
			{
				$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
			}else
			{
				$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
			}
		}
		else if ($type == 'diffusion')
		{
			if (file_exists(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl"))
			{
				$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl");
			}else
			{
				$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata.xsl');
			}
		}
		*/
		$processor->importStylesheet($style);
		$myHtml = $processor->transformToXml($cswResults);
		displayManager::exportPDFfile($myHtml);
	}
	
	function exportPDFfile( $myHtml) {
		global  $mainframe;
		$database =& JFactory::getDBO();
		$id = JRequest::getVar('id');
		$supplier;
		$product_creation_date;
		$product_update_date;
		
		//$timerFile = 'timer.txt';
		//$timer = fopen ($timerFile, 'w');
			
		//fwrite($timer, "Avant accès base de données : ".date("H:i:s")."\n");
		
		$db =& JFactory::getDBO();
		$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
		
		$query="select u.name from #__easysdi_community_partner p inner join #__users u on p.user_id = u.id WHERE p.partner_id = ".$partner_id;
   			$db->setQuery($query);
   			$supplier= $db->loadResult();
			
		$query = "select creation_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$temp = $db->loadResult();
			$product_creation_date = date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		$query = "select metadata_update_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$temp = $db->loadResult();
			$product_update_date = $temp == '0000-00-00 00:00:00' ? '-' : date(config_easysdi::getValue("DATETIME_FORMAT", "d-m-Y H:i:s"), strtotime($temp));
		
		$query = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
			$db->setQuery($query);
			$partner_logo = $db->loadResult();
		
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");		
		$img='<img width="'.$logoWidth.'" height="'.$logoHeight.'" src="'.JPATH_BASE.DS.$partner_logo.'"/>';
		
		$myHtml = str_replace("__ref_1\$s", $img, $myHtml);
		$myHtml = str_replace("__ref_2\$s", $supplier, $myHtml);
		$myHtml = str_replace("__ref_3\$s", $product_creation_date, $myHtml);
		$myHtml = str_replace("__ref_4\$s", $product_update_date, $myHtml);
		$myHtml = str_replace("__ref_5\$s", "", $myHtml);
		$myHtml = str_replace("__ref_6\$s", "", $myHtml);

		//fwrite($timer, "Après accès base de données : ".date("H:i:s")."\n");
		//fwrite($timer, "Avant application xhtml to xslfo : ".date("H:i:s")."\n");
		
		if($myHtml == "")
			$myHtml = "<div/>";
		
		$timer = fopen ('/home/sites/joomla.asitvd.ch/web/components/com_easysdi_core/tmp/my', 'w');
		fwrite($timer, $myHtml);
		fclose($timer);
		
		$document  = new DomDocument();	
		$document ->load(dirname(__FILE__).'/../xsl/xhtml-to-xslfo.xsl');
		$processor = new xsltProcessor();
		$processor->importStylesheet($document);
		
		//fwrite($timer, "Après application xhtml to xslfo : ".date("H:i:s")."\n");
		
		//Problem with loadHTML() and encoding : work around method
		$pageDom = new DomDocument();
   		$searchPage = mb_convert_encoding($myHtml, 'HTML-ENTITIES', "UTF-8");
		@$pageDom->loadHTML($searchPage);
		$result = $processor->transformToXml($pageDom);
		$exportpdf_url = config_easysdi::getValue("EXPORT_PDF_URL");
		//$fop_url = config_easysdi::getValue("FOP_URL");
	 
		if ($exportpdf_url ){ 
			//require_once($bridge_url);
			//$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP'  ),'error');
			// version FOP 0.93 
			//$java_library_path = 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'fop.jar;';
			//$java_library_path .= 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'FOPWrapper.jar';
			
			$tmp = uniqid();
			$fopcfg = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'config'.DS.'fop.xml';
			$foptmp = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'.pdf';
			$fopfotmp = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp.'.fo';
			//Check foptmp against the schema before processing
			//avoid JavaBrigde to fail
			
			file_put_contents($fopfotmp, $result);

			//java -jar JavaBridge.jar SERVLET:8080 3 JavaBridge.log
			
			//java_require($java_library_path);
			//$j_fw = new Java("FOPWrapper");
			//Génération du document PDF sous forme de fichier
			$res = "";
			//Url to the export pdf servlet
			$url = $exportpdf_url."?cfg=fop.xml&fo=$tmp.fo&pdf=$tmp.pdf";
			//echo $url;
			$fp = fopen($url,"r");
			while (!feof($fp)) {
				$res .= fgets($fp, 4096);
			}
			//echo $res;
			/*
			try{
				$j_fw->convert($fopcfg,$fopfotmp,$foptmp);
			}catch (JavaException $ex) {
				echo "Cause 1: ".$ex->getCause()."\n";
				echo "Message 1: ".$ex->getMessage()."\n";
				$trace = new Java("java.io.ByteArrayOutputStream");
				$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
				print $trace;
			}
			*/
			
			
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
			$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  ),'error'); 
		}
	}
	
	function reportPdfError() //, $timer)
	{
		$res = urldecode(JRequest::getVar('res'));
		
		echo '<div id="metadata" class="contentin">';
		echo '<h2 class="contentheading">'.JText::_('EASYSDI_ERROR_PDF_TITLE').'</h2>';
		echo '<table class="descr">';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td>'.JText::_('EASYSDI_ERROR_PDF_DETAIL').'</td><td>'.$res.'</td></tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td colspan="2">'.JText::_('EASYSDI_ERROR_PDF_REPORT').'</td></tr>';
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