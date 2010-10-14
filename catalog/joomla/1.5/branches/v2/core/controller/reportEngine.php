<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d'Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class reportEngine{
	
	function getReport()
	{
		global $mainframe;
		$user =& JFactory::getUser();
		$database=& JFactory::getDBO();
		
		/* Début du code de génération du rapport */
		$query="";
		$results=array();
		
		// Récupération des paramètres, en minuscules (sauf metadata_guid[] qu'on ne touche pas)
		$params = array();
		$objecttype_code = strtolower(JRequest::getVar ('metadatatype', ''));
		$params['metadatatype'] = $objecttype_code;
		$format = strtolower(JRequest::getVar ('format', ''));
		$params['format'] = $format;
		$reporttype = strtolower(JRequest::getVar ('reporttype', ''));
		$params['reporttype'] = $reporttype;
		$metadata_guid = JRequest::getVar ('metadata_guid', array(0));
		$params['metadata_guid'] = $metadata_guid;
		$language = strtolower(JRequest::getVar ('language', ''));
		$params['language'] = $language;
		$lastVersion = strtolower(JRequest::getVar ('lastVersion', ''));
		$params['lastVersion'] = $lastVersion;
		
		// Contrôler la validité de la requête avant de la passer plus loin
		if (!reportEngine::verifyRequest($params, $user))
			exit;
		
		// Rassembler les guids de métadonnées indiqués en une string de la forme (guid1, guid2, guid3, ..., guidn)
		$guids = "";
		foreach ($metadata_guid as &$mg)
		{
			$mg="'".$mg."'";
		}
		$guids = implode(",", $metadata_guid);
		
		// Récupérer tous les guids qui sont publics
		$query = "	SELECT m.guid as metadata_guid, o.id as object_id
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id=m.id
					INNER JOIN #__sdi_object o ON ov.object_id=o.id
					INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
					INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
					WHERE v.code='public'
				";

		// Qui sont indiqués dans le paramètre metadata_guid
		$query .= " AND m.guid IN (".$guids.")";
		
		// Qui sont du type indiqué dans le paramètre metadatatype
		$query .= " AND ot.code = '".$objecttype_code."'";
		$database->setQuery($query);
		$results = $database->loadObjectList();
		//echo $database->getQuery()."<br>";		
		
		// Si c'est la dernière version qui est demandée, il faut faire des traitements supplémentaires
		if ($lastVersion == "yes")
		{
			foreach ($results as &$result)
			{
				// Pour chaque métadonnée qui a satisfait aux critères précédents, trouver sa dernière version
				$query = "SELECT m.guid as metadata_guid 
									  FROM #__sdi_objectversion ov 
									  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
									  WHERE ov.object_id=".$result->object_id." 
									  ORDER BY ov.created DESC";
				
				$database->setQuery($query);
				//echo $database->getQuery()."<br>";
				$result->metadata_guid = $database->loadResult();
			}
		}
		
		// Construction du filtre
		if (count($results) > 0)
		{
			$filter = "";
			foreach ($results as $result)
			{
				$filter  .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>".$result->metadata_guid."</ogc:Literal></ogc:PropertyIsEqualTo>";
			}
			if (count($results) > 1)
				$filter = "<ogc:Or>".$filter."</ogc:Or>";
	
			$filter = "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">".$filter."</ogc:Filter>";
			
			// Construire une requête Geonetwork GetRecords pour demander les métadonnées choisies pour le rapport
			$xmlBody = SITE_catalog::BuildCSWRequest(10, 1, "datasetcollection dataset application service", "full", "1.1.0", $filter, "title", "ASC");
			
			//echo htmlspecialchars($xmlBody);

			// Envoi de la requête
			$catalogUrlBase = config_easysdi::getValue("catalog_url");
			$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
			$cswResults = DOMDocument::loadXML($xmlResponse);

			//echo htmlspecialchars($cswResults->saveXML())."<hr>";
			
			// Traitement du retour CSW pour générer le rapport
			if ($cswResults !=null and $cswResults !="")
			{
				// Contrôler si le XML ne contient pas une erreur
				if ($cswResults->childNodes->item(0)->nodeName == "ows:ExceptionReport")
				{
					// Retourner une erreur au format XML, formatée par un XSl
					$xmlError = new DomDocument();
					$style = new DomDocument();
					$xmlError->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'OWSEXCEPTION.xml');
					$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'XHTML_GETREPORT_ERRORS.xsl');
					$processor = new xsltProcessor();
					$processor->setParameter('', 'error_type', "OWSEXCEPTION");
					$processor->setParameter('', 'user_language', $user->getParam('language', ''));
					$processor->importStylesheet($style);
					$xmlToHtml = $processor->transformToXml($xmlError);
					if ($xmlToHtml <> "")
					{
						file_put_contents($xmlToHtml, 'error.html');
						reportEngine::setResponse($xmlToHtml, 'error.html', 'application/html', 'error.html');
						exit;
					}
				}
				else
				{
					$myDoc= new DomDocument();
					
					// Noeud artificiel supérieur pour englober la métadonnée et les paramètres
					$XMLNewRoot = $myDoc->createElement("Metadata");
					$myDoc->appendChild($XMLNewRoot);
					
					// Construire les paramètres
					$rootList = $cswResults->getElementsByTagName("MD_Metadata");
					foreach($rootList as $root)
					{
						if ($root->parentNode->nodeName == "csw:SearchResults")
						{
							$importedPart = $myDoc->importNode($root, true);
							$XMLNewRoot->appendChild($importedPart);
						}
					}
		
					//echo htmlspecialchars($myDoc->saveXML())."<hr>";

					$style = new DomDocument();
					$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'report.xsl');
					$processor = new xsltProcessor();
					$processor->setParameter('', 'language', $language);
					$processor->setParameter('', 'format', $format);
					$processor->setParameter('', 'reporttype', $reporttype);
					$processor->importStylesheet($style);
					$xml = $processor->transformToXML($myDoc);
					//echo htmlspecialchars($xml);
					//$xml = new DomDocument();
					//$xml = $processor->transformToDoc($myDoc);
					//echo htmlspecialchars($xml->saveXML());
					$tmp = uniqid();
					$tmpfile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'tmp'.DS.$tmp;
					//print_r(libxml_get_last_error());
					reportEngine::buildReport($format, $xml, $tmpfile, $tmp);
				}
			}
		}
		else
		{
			// Retourner une erreur au format XML, formatée par un XSl
			$xmlError = new DomDocument();
			$style = new DomDocument();
			$xmlError->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'NOMETADATA.xml');
			$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'XHTML_GETREPORT_ERRORS.xsl');
			$processor = new xsltProcessor();
			$processor->setParameter('', 'error_type', "NOMETADATA");
			$processor->setParameter('', 'user_language', $user->getParam('language', ''));
			$processor->importStylesheet($style);
			$xmlToHtml = $processor->transformToXml($xmlError);
			if ($xmlToHtml <> "")
			{
				file_put_contents($xmlToHtml, 'error.html');
				reportEngine::setResponse($xmlToHtml, 'error.html', 'application/html', 'error.html');
				exit;
			}
		}
	}

	function buildReport($format, $file, $tmpfile, $tmp)
	{
		switch ($format)
		{
			case "xml":
				file_put_contents($tmpfile.'.xml', $file);
				reportEngine::setResponse($file, $tmpfile.'.xml', 'text/xml', 'report.xml');
				break;
			case "csv":
				file_put_contents($tmpfile.'.csv', $file);
				reportEngine::setResponse($file, $tmpfile.'.csv', 'text/csv', 'report.csv');
				break;
			case "rtf":
				file_put_contents($tmpfile.'.rtf', $file);
				reportEngine::setResponse($file, $tmpfile.'.rtf', 'text/rtf', 'report.rtf');
				break;
			case "xhtml":
				file_put_contents($tmpfile.'.html', $file);
				reportEngine::setResponse($file, $tmpfile.'.html', 'text/html', 'report.html');
				break;
			case "pdf":
				$exportpdf_url = config_easysdi::getValue("JAVA_BRIDGE_URL");
	
				if ($exportpdf_url )
				{ 
					$fopcfg = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xml'.DS.'config'.DS.'fop.xml';
					$foptmp = $tmpfile.'.pdf';
					$fopfotmp = $tmpfile.'.fo';
					//Check foptmp against the schema before processing
					//avoid JavaBrigde to fail
					file_put_contents($fopfotmp, $file);
					
					//Génération du document PDF sous forme de fichier
					$res = "";
					//Url to the export pdf servlet
					$url = $exportpdf_url."?cfg=fop.xml&fo=$tmp.fo&pdf=$tmp.pdf";
					//echo $url;die();break;
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
						
						reportEngine::setResponse($result, $foptmp, 'application/pdf', 'report.pdf');
					}else
					{
						//If there was an error when generating the pdf, write it.
						$mainframe->redirect("index.php?option=com_easysdi_core&tmpl=component&task=reportPdfError&res=".urlencode($res));
					}
				}else {
					printf(JText::_(  'CORE_UNABLE TO LOAD THE CONFIGURATION KEY FOR FOP JAVA BRIDGE'  )); 
				}
				break;
			default:
				break;
		}
	}
	
	/*
	 * Nom: verifyRequest
	 * But: Contrôler que la requête contient bien tous les paramètres requis et qu'ils ont les valeurs attendues
	 */
	function verifyRequest($params, $user)
	{
		// Contrôler que tous les paramètres sont renseignés
		foreach ($params as $key => $param)
		{
			if ($param == '' or count($param) == 0)
			{
				// Retourner une erreur au format XML, formatée par un XSl
				$xmlError = new DomDocument();
				$style = new DomDocument();
				$xmlError->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'MISSINGPARAMETER.xml');
				$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'XHTML_GETREPORT_ERRORS.xsl');
				$processor = new xsltProcessor();
				$processor->setParameter('', 'error_type', "MISSINGPARAMETER");
				$processor->setParameter('', 'user_language', $user->getParam('language', ''));
				$processor->setParameter('', 'missing_parameter', $key);
				$processor->importStylesheet($style);
				$xmlToHtml = $processor->transformToXml($xmlError);
				file_put_contents($xmlToHtml, 'error.html');
				reportEngine::setResponse($xmlToHtml, 'error.html', 'application/html', 'error.html');
				return false;
			}
		}
		
		// Valeurs autorisées pour format: XML/CSV/RTF/XHTML/PDF
		$formatValues = array ('xml', 'csv', 'rtf', 'xhtml', 'pdf');
		if (array_search($params['format'], $formatValues) === false)
		{
			// Retourner une erreur au format XML, formatée par un XSl
			$xmlError = new DomDocument();
			$style = new DomDocument();
			$xmlError->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'FORMATINVALID.xml');
			$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'XHTML_GETREPORT_ERRORS.xsl');
			$processor = new xsltProcessor();
			$processor->setParameter('', 'error_type', "FORMATINVALID");
			$processor->setParameter('', 'user_language', $user->getParam('language', ''));
				$processor->importStylesheet($style);
			$xmlToHtml = $processor->transformToXml($xmlError);
			file_put_contents($xmlToHtml, 'error.html');
			reportEngine::setResponse($xmlToHtml, 'error.html', 'application/html', 'error.html');
			return false;
		}
		
		// Valeurs autorisées pour lastVersion: yes/no   
		$lastversionValues = array ('yes', 'no');
		if (array_search($params['lastVersion'], $lastversionValues) === false)
		{
			// Retourner une erreur au format XML, formatée par un XSl
			$xmlError = new DomDocument();
			$style = new DomDocument();
			$xmlError->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'LASTVERSIONINVALID.xml');
			$style->load(JPATH_COMPONENT_ADMINISTRATOR.DS.'xsl'.DS.'getreport'.DS.'XHTML_GETREPORT_ERRORS.xsl');
			$processor = new xsltProcessor();
			$processor->setParameter('', 'error_type', "LASTVERSIONINVALID");
			$processor->setParameter('', 'user_language', $user->getParam('language', ''));
				$processor->importStylesheet($style);
			$xmlToHtml = $processor->transformToXml($xmlError);
			file_put_contents($xmlToHtml, 'error.html');
			reportEngine::setResponse($xmlToHtml, 'error.html', 'application/html', 'error.html');
			return false;
		}

		return true;
	}
	
	function setResponse($file, $filename, $contenttype, $downloadname)
	{
		error_reporting(0);
		ini_set('zlib.output_compression', 0);
                        
		header('Content-type: '.$contenttype);
		header('Content-Disposition: attachment; filename="'.$downloadname.'"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Pragma: public');
		header("Expires: 0"); 
		header("Content-Length: ".filesize($filename));
		
		echo $file;
		//Very important, if you don't call this, the content-type will have no effect
		die();
	}
}

?>