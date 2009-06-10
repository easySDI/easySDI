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

class displayManager{
	
	function getCSWresult ()
	{
		$id = JRequest::getVar('id');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;
				
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
		return $cswResults;
	}
	function showMetadata()
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
		displayManager::DisplayMetadata($style,$xml);
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
		
		displayManager::DisplayMetadata($style,$xml);
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
	
		$doc .= '</Diffusion></Metadata>';
		
		$document = new DomDocument();
		$document->loadXML($doc);
		
		$style = new DomDocument();
		if (file_exists(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/diffusion_metadata.xsl');
		}
		
		displayManager::DisplayMetadata($style,$document);
	}
	
	function DisplayMetadata ($xslStyle, $xml)
	{
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
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
			$product_creation_date = $db->loadResult();
		
		$query = "select update_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$product_update_date = $db->loadResult();

			
		/*$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

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
		if ($toolbar==1){
			$buttonsHtml .= "<table align=\"right\"><tr align='right'>
				<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTPDF")."\" id=\"exportPdf\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_EXPORTXML")."\" id=\"exportXml\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_PRINTMD")."\" id=\"printMetadata\"/></td>
					<td><div title=\"".JText::_("EASYSDI_ACTION_ORDERPRODUCT")."\" id=\"orderProduct\"/></a></td>
				</td></tr></table>";		
		}
		if ($print ==1 ){
			$myHtml .= "<script>window.print();</script>";
			
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
		
		
		//Manage display class
		
		$myHtml .= "window.addEvent('domready', function() {
		
		$('catalogPanel1').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=$id', '_self');
		});
		$('catalogPanel2').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showCompleteMetadata&id=$id', '_self');
		});
		$('catalogPanel3').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=com_easysdi_core&task=showDiffusionMetadata&id=$id', '_self');
		});
		$('exportPdf').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=$option&task=exportPdf&id=$id', '_self');
		});
		$('exportXml').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&format=raw&option=$option&task=exportXml&id=$id', '_self');
		});
		$('printMetadata').addEvent( 'click' , function() { 
			window.open('./index.php?tmpl=component&option=$option&task=$task&id=$id&toolbar=0&print=1','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
		});
		$('orderProduct').addEvent( 'click' , function() { 
			window.open('./index.php?option=com_easysdi_shop&view=shop', '_main');
		});

		task = '$task';
		$('catalogPanel1').className = 'closed';
		$('catalogPanel2').className = 'closed';
		$('catalogPanel3').className = 'closed';
		if(task == 'showMetadata'){
        	$('catalogPanel1').className = 'open';
		}
		if(task == 'showCompleteMetadata'){
        	$('catalogPanel2').className = 'open';
		}
		if(task == 'showDiffusionMetadata'){
        	$('catalogPanel3').className = 'open';
		}
		});\n"; 

		$myHtml .= "</script>";

		}
		
		//Workaround to avoid printf problem with text with a "%", must
		//be changed to "%%".
		$xmlToHtml = str_replace("%", "%%", $xmlToHtml);
		$xmlToHtml = str_replace("__ref__asit_", "%", $xmlToHtml);
		$myHtml .= $xmlToHtml;
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
		
		$temp = explode(" ", $product_creation_date);
		$temp = explode("-", $temp[0]);
		$product_creation_date = $temp[2].".".$temp[1].".".$temp[0];
		$temp = explode(" ", $product_update_date);
		$temp = explode("-", $temp[0]);
		$product_update_date = $temp[2].".".$temp[1].".".$temp[0];
		
		$img='<img width="$'.$logoWidth.'" height="'.$logoHeight.'" src="'.$partner_logo.'">';
		
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
		$id = JRequest::getVar('id');

		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);

		$xpath = new DomXPath($cswResults);
		$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$xpath->registerNamespace('gco','http://www.isotc211.org/2005/gco');
		$nodes = $xpath->query("//gmd:MD_Metadata");

		$dom = new DOMDocument();

		if($nodes->item(0) != "")
		{
			$xmlContent = $dom ->importNode($nodes->item(0),true);
			$dom->appendChild($xmlContent);
		}
		/*
		$xml = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.'metadata.xml';
		$file =fopen($xml, 'w');
		fwrite($file, $dom->saveXML());
		fclose($file);	
		*/
		error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		//header('Content-Transfer-Encoding: binary');
		header('Content-Tran§sfer-Encoding: none');
		header('Content-Type: text/xml');
		//header('Content-Type: application/force-download');		
		header('Content-Disposition: attachement; filename="metadata.xml"');
		header("Content-Description: File Transfer" );
 		//header("Expires: 0"); 
		//header("Content-Length: ".filesize($file));
		
		//readfile($file);
		
		echo $dom->saveXML();
	}
	
	function exportPdf(){
		
	/*	$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$id = JRequest::getVar('id');

		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);*/
			
		$cswResults = displayManager::getCSWresult();
		
		$processor = new xsltProcessor();
		$style = new DomDocument();

		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');

		if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl")){
			$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
		}else{
			$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
		}
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
		
		$db =& JFactory::getDBO();
		$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($queryPartnerID);
			$partner_id = $db->loadResult();
		
		$query="select u.name from #__easysdi_community_partner p inner join #__users u on p.user_id = u.id WHERE p.partner_id = ".$partner_id;
   			$db->setQuery($query);
   			$supplier= $db->loadResult();
			
		$query = "select creation_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$product_creation_date = $db->loadResult();
		
		$query = "select update_date from #__easysdi_product where metadata_id = '".$id."'";
			$db->setQuery($query);
			$product_update_date = $db->loadResult();
			
		$temp = explode(" ", $product_creation_date);
		$temp = explode("-", $temp[0]);
		$product_creation_date = $temp[2].".".$temp[1].".".$temp[0];
		$temp = explode(" ", $product_update_date);
		$temp = explode("-", $temp[0]);
		$product_update_date = $temp[2].".".$temp[1].".".$temp[0];
		

		$myHtml = str_replace("__ref__asit_1\$s", "", $myHtml);
		$myHtml = str_replace("__ref__asit_2\$s", $supplier, $myHtml);
		$myHtml = str_replace("__ref__asit_3\$s", $product_creation_date, $myHtml);
		$myHtml = str_replace("__ref__asit_4\$s", $product_update_date, $myHtml);
		$myHtml = str_replace("__ref__asit_5\$s", "", $myHtml);
		$myHtml = str_replace("__ref__asit_6\$s", "", $myHtml);

		$document  = new DomDocument();	
		$document ->load(dirname(__FILE__).'/../xsl/xhtml-to-xslfo.xsl');
		$processor = new xsltProcessor();
		$processor->importStylesheet($document);
		
		//Problem with loadHTML() and encoding : work around method
		$pageDom = new DomDocument();   
   		$searchPage = mb_convert_encoding($myHtml, 'HTML-ENTITIES', "UTF-8");
    	@$pageDom->loadHTML($searchPage);
    	$result = $processor->transformToXml($pageDom);    	
		$bridge_url = config_easysdi::getValue("JAVA_BRIDGE_URL");
		$fop_url = config_easysdi::getValue("FOP_URL");
	 
		if ($bridge_url ){ 
			require_once($bridge_url);
			if ($fop_url )
			{ 

				$tmp = uniqid();
				$fopcfg = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'config'.DS.'fop.xml';
				$fopxml = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.$tmp.'.xml';
				$fopxsl = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.$tmp.'.xsl';
				$fopfo = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.$tmp.'.fo';
				$foptmp = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.$tmp.'.pdf';
				
				try {
					// Ecrire le xml temporaire
					$fp = fopen ($fopxml, 'w');
					fwrite($fp, $pageDom->saveXML());
					fclose ($fp);
					
					// Ecrire le xslt temporaire
					$fp = fopen ($fopxsl, 'w');
					fwrite($fp, $document->saveXML());
					fclose ($fp);
					
					// Usefull FOP libraries
					java_require("http://localhost:8080/fop-0.95/build/fop.jar;
						      	 http://localhost:8080/fop-0.95/lib/xmlgraphics-commons-1.3.1.jar;
								 http://localhost:8080/fop-0.95/lib/batik-all-1.7.jar;
								 http://localhost:8080/fop-0.95/lib/avalon-framework-4.2.0.jar;
								 http://localhost:8080/fop-0.95/lib/xml-apis-1.3.04.jar;
								 http://localhost:8080/fop-0.95/lib/commons-io-1.3.1.jar;
								 http://localhost:8080/fop-0.95/lib/commons-logging-1.0.4.jar");
					
					//Create the PDF file based on the FO file
					displayManager::convertXML2FO($fopxml, $fopxsl, $fopfo);
					displayManager::convertFO2PDF($fopfo, $foptmp);
					
					// Remove temporaries files
					unlink($fopxml);
					unlink($fopxsl);
					unlink($fopfo);
				    
					if (file_exists($foptmp)) {
					    header('Content-Description: File Transfer');
					    header('Content-Type: application/octet-stream');
					    header('Content-Disposition: attachment; filename=metadata.pdf');
					    header('Content-Transfer-Encoding: binary');
					    header('Expires: 0');
					    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					    header('Pragma: public');
					    header('Content-Length: ' . filesize($foptmp));
					    ob_clean();
					    flush();
					    readfile($foptmp);
					    exit;
					}
					/*					
					@java_reset();
						
				 	error_reporting(0);
					ini_set('zlib.output_compression', 0);
					
					header('Pragma: public');
					header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
					header('Content-Transfer-Encoding: binary');
					header('Content-type: application/pdf');
					header('Content-Disposition: attachement; filename="metadata.pdf"');
					header("Expires: 0"); 
					header("Content-Length: ".filesize($foptmp));
					
					ob_clean();
				    flush();
				    @readfile($foptmp);
					*/
					
				}
				catch (JavaException $ex) {
					echo "An exception occured: "; echo $ex; echo "<br>\n";
				}
			}
			else {
				$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP'  ),'error');
				
				// FOP 0.93 - à tester
				$java_library_path = 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'fop.jar;';
				$java_library_path .= 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'FOPWrapper.jar';
				
				$tmp = uniqid();
				$fopcfg = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'config'.DS.'fop.xml';
				$foptmp = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.$tmp.'.pdf';
				
				@java_reset();		
				
				java_require($java_library_path);
				$j_fw = new Java("FOPWrapper");
				$version = $j_fw->FOPVersion();
				//Génération du document PDF sous forme de fichier
				$j_fw->convert($fopcfg,$result,$foptmp);
				
				@java_reset();
						
			 	error_reporting(0);
				ini_set('zlib.output_compression', 0);
				
				header('Pragma: public');
				header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
				header('Content-Transfer-Encoding: binary');
				header('Content-type: application/pdf');
				header('Content-Disposition: attachement; filename="metadata.pdf"');
				header("Expires: 0"); 
				header("Content-Length: ".filesize($foptmp));
				
				ob_clean();
			    flush();
			    readfile($foptmp);
				
			    echo $result;
			}
		}else {
			$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  ),'error'); 
		}
	}
	
	function convertXML2FO($xml, $xslt, $fo)
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
		$factory = new java("javax.xml.transform.TransformerFactory");
		$factory = $factory->newInstance();
		$xsltStream = new java("javax.xml.transform.stream.StreamSource", $xslt);
		//$xsltStream->setSystemId($xmlSystemId);
		$transformer = $factory->newTransformer($xsltStream);

		//Setup input for XSLT transformation
		$src = new java("javax.xml.transform.stream.StreamSource", $xml);
		//Resulting SAX events (the generated FO) must be piped through to FOP
		$res = new java("javax.xml.transform.stream.StreamResult", $out);
		//Start XSLT transformation and FOP processing
		$transformer->transform($src, $res);
	}
	catch (JavaException $ex) {
			echo "An exception occured: "; echo $ex; echo "<br>\n";
	}
	if($out != null)
		$out->close();		
	}
		
	function convertFO2PDF($fo, $pdf)
	{
		try
		{
			$fop_mime_constants = new JavaClass('org.apache.fop.apps.MimeConstants');
			// configure fopFactory as desired
			$fopFactory = new java("org.apache.fop.apps.FopFactory");
			$fopFactory = $fopFactory->newInstance();
			
			// configure foUserAgent as desired
			$foUserAgent = $fopFactory->newFOUserAgent();
	
			// Setup output
			$pdf = new java("java.io.File", $pdf);
			$pdf= $pdf->getAbsolutePath();
				
			$out = new java("java.io.FileOutputStream", $pdf);
			$out = new java("java.io.BufferedOutputStream", $out);
	
			// Construct fop with desired output format
			$fop = $fopFactory->newFop($fop_mime_constants->MIME_PDF, $foUserAgent, $out);
	
			//Setup XSLT
			$factory = new java("javax.xml.transform.TransformerFactory");
			$factory = $factory->newInstance();
			$transformer = $factory->newTransformer();
	
			// Set the value of a <param> in the stylesheet
			$transformer->setParameter("versionParam", "2.0");

			//Setup input for XSLT transformation
			$src = new java("javax.xml.transform.stream.StreamSource", $fo);
        
			// Resulting SAX events (the generated FO) must be piped through to FOP
			$res = new java("javax.xml.transform.sax.SAXResult", $fop->getDefaultHandler());
	
			//Start XSLT transformation and FOP processing
			$transformer->transform($src, $res);
		}
		catch (JavaException $ex) {
			echo "An exception occured: "; echo $ex; echo "<br>\n";
		}
		if($out != null)
			$out->close();
	}
}

?>