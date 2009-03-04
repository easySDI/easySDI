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
	
	function showMetadata(){

		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$id = JRequest::getVar('id');
		$toolbar =JRequest::getVar('toolbar',1);
		$print =JRequest::getVar('print',0);

		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
			

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
		if ($toolbar==1){
			echo "<table width='100%'><tr align='right'> <td><a  class=\"buttonheading\" target=\"_TOP\"  href=\"./index.php?tmpl=component&option=$option&task=exportPdf&id=$id\"> <img src=\"components/com_easysdi_shop/img/pdfButton.png\" alt=\"PDF\"  /></a> <a  class=\"buttonheading\" target=\"_TOP\" href=\"./index.php?tmpl=component&format=raw&option=$option&task=exportXml&id=$id\"> <img src=\"components/com_easysdi_shop/img/xmlButton.png\" alt=\"XML\"  /></a> <a  class=\"buttonheading\" target=\"_TOP\"  href=\"./index.php?tmpl=component&option=$option&task=showMetadata&id=$id&toolbar=0&print=1\" onclick=\"window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;\"><img src=\"components/com_easysdi_shop/img/printButton.png\" alt=\"PRINT\"  /></td></tr></table>";
		}
		if ($print ==1 ){
			echo "<script>window.print();</script>";

		}
		
		//Affichage des onglets
		echo "<a class='tab1' href=\"./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=$id\" rel=\"{handler:'iframe',size:{x:500,y:500}}\" >".JText::_(EASYSDI_METADATA_ABSTRACT_TAB)."</a>";
		echo "<div class='tab2' href=''>".JText::_(EASYSDI_METADATA_COMPLETE_TAB)."</div>";
		echo "<div class='tab3' href=''>".JText::_(EASYSDI_METADATA_DIFFUSION_TAB)."</div>";
		
		echo $myHtml ;

			
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
	
	function exportXml(){

		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
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
		$xmlContent = $dom ->importNode($nodes->item(0),true);
		$dom->appendChild($xmlContent);


		error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Tran§sfer-Encoding: none');
		header('Content-Type: text/xml');
		header('Content-Disposition: attachement; filename="metadata.xml"');

		echo $dom->saveXML();
	}
	
	function exportPdf(){
		
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$id = JRequest::getVar('id');

		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
			

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

		
		$document  = new DomDocument();
				
		$document ->load(JPATH_COMPONENT_SITE.'/xsl/xhtml-to-xslfo.xsl');
		$processor = new xsltProcessor();
		$processor->importStylesheet($document);
		
		
		//Problem with loadHTML() and encoding : work around method
		$pageDom = new DomDocument();   
   		$searchPage = mb_convert_encoding($myHtml, 'HTML-ENTITIES', "UTF-8");
    	$pageDom->loadHTML($searchPage);
    	$result = $processor->transformToXml($pageDom);    	
		//$result = $processor->transformToXml(DOMDocument::loadHTML($myHtml));
		
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		$bridge_url = config_easysdi::getValue("JAVA_BRIDGE_URL");
		 
		if ($bridge_url ){ 
			
		require_once($bridge_url);
			
		$java_library_path = 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'fop.jar;';
		$java_library_path .= 'file:'.JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'java'.DS.'fop'.DS.'FOPWrapper.jar';
			
		$fopcfg = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'config'.DS.'fop.xml';
		$foptmp = JPATH_COMPONENT_ADMINISTRATOR.DS.'xml'.DS.'tmp'.DS.uniqid().'pdf';
		
		
	
			
				try {
					@java_reset();		
				java_require($java_library_path);
			
				$j_fw = new Java("FOPWrapper");
				
				$version = $j_fw->FOPVersion();
				//Gï¿½nï¿½ration du document PDF sous forme de fichier
				$j_fw->convert($fopcfg,$result,$foptmp);
				
				@java_reset();

				$fp = fopen ($foptmp, 'r');
				$result = fread($fp, filesize($foptmp));
				fclose ($fp);

				 
				
				 error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-TranÂ§sfer-Encoding: none');
		header('Content-Type: application/octetstream; name="metadata.pdf"');
		header('Content-Disposition: attachement; filename="metadata.pdf"');

		echo $result;

		
			} catch (JavaException $ex) {
				$trace = new Java("java.io.ByteArrayOutputStream");
				$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
				print "java stack trace: $trace\n";
			}
		}else {
			$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  ),'error'); 
		}
	}
}

?>