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


/**
 * This proxy is intended to be used with secure webservices.  
 */
class SITE_proxy{
	
	function proxy(){
		global  $mainframe;
		$db 		=& JFactory::getDBO();
		$user		= "";
		$password	= "";
		
		$url 		= ($_POST['url']) ? $_POST['url'] : $_GET['url'];
		
		if (SITE_proxy::contains("?",$url))
		{
			$url=$url."&"; 			
		}else
		{
			$url=$url."?";
		}
		
		$found = false;
		
		foreach($_GET AS $cle => $valeur)     
		{     	     	
			if ($found)
			{
				// Problème : le filtre des wfs est également décomposé dans la variable GET en paramètre chaque fois que le signe
				// égal est rencontré. Pour palier à ce problème on enlève les clés qui n'ont pas de valeur ocrrespondantes
				if ((strcasecmp ($valeur,"style"))|| ($valeur != null && strlen($valeur)>0 ))
				{		
					if (substr($valeur, 0, 7)=='filter:') {
						$valeur = $this->get_filter($valeur);
						//Le déploiement étant réalisé sur des serveurs Windows, on rencontre un problème d'encodage sur les filtres XML passés en GET :
						//Geoserver récupère ce paramètre avec la méthode string.getBytes() sans spécifier l'encodage. Sur un serveur windows, l'encodage
						//par défaut qui sera pris alors en compte est cp1252.
						//Pour que le parsing du filtre XML se déroule ensuite sans problème il faut donc que l'on spécifie le même encodage
						//dans l'entête XML afin que le bon parser soit instancié.
						$valeur = urlencode("<?xml version='1.0' encoding='windows-1252'?>") . $valeur;
					}			
					$url=$url."$cle=$valeur&";								
				}		     		
			}
			if ($cle == "url")
			{     		
				$found =true;     		
			}     	            
		}
		
		$url = str_replace ('\"','"',$url);
		$url = str_replace (' ','%20',$url);
		 
		$type = ($_POST['type']) ? $_POST['type'] : $_GET['type'];
				
		$basemapscontentid = ($_POST['basemapscontentid']) ? $_POST['basemapscontentid'] : $_GET['basemapscontentid'];
		$perimeterdefid = ($_POST['perimeterdefid']) ? $_POST['perimeterdefid'] : $_GET['perimeterdefid'];
		$locationid = ($_POST['locationid']) ? $_POST['locationid'] : $_GET['locationid'];
		$previewId = ($_POST['previewId']) ? $_POST['previewId'] : $_GET['previewId'];
		$gridId = ($_POST['gridId']) ? $_POST['gridId'] : $_GET['gridId'];
		$case = 0;
		if($basemapscontentid)
			$case = 1;
		if($perimeterdefid)
			$case = 2;
		if($locationid)
			$case = 3;
		if($previewId)
			$case = 4;
		if($gridId)
			$case = 5;
		
		switch ($case)
		{
			case 1:
				$query = "select * from #__sdi_basemapcontent where id = $basemapscontentid";
				$db->setQuery( $query);
				$row = $db->loadObject();
				SITE_proxy::getAuthentication ($row, $user, $password);
				break;
			case 2:
				$query = $query = "SELECT * FROM #__sdi_perimeter WHERE id = $perimeterdefid";
				$db->setQuery( $query);
				$row = $db->loadObject();
				SITE_proxy::getAuthentication ($row, $user, $password);
				break;
			case 3:
				$query = $query = "SELECT * FROM #__sdi_location WHERE id = $locationid";
				$db->setQuery( $query);
				$row = $db->loadObject();
				SITE_proxy::getAuthentication ($row, $user, $password);
				break;
			case 4:
				$query = $query = "SELECT * FROM #__sdi_product WHERE id = $previewId";
				$db->setQuery( $query);
				$row = $db->loadObject();
				SITE_proxy::getAuthentication ($row, $user, $password);
				break;
			case 5:
				$query = $query = "SELECT * FROM #__sdi_grid WHERE id = $previewId";
				$db->setQuery( $query);
				$row = $db->loadObject();
				
				if($type == "wfs")
				{
					if($row->wfsaccount_id && $row->wfsaccount_id <> 0)
					{
						$db =& JFactory::getDBO();
						$query = "SELECT username, password FROM #__users WHERE id IN (SELECT user_id FROM #__sdi_account WHERE id= $row->wfsaccount_id)";
						$db->setQuery( $query);
						$row = $db->loadObject();
						$user = $row->username;
						$password = $row->password;
					}
					else
					{
						$user = $object->wfsuser;
						$password = $object->wfspassword;
					}
				}
				else
				{
					if($row->wmsaccount_id && $row->wmsaccount_id <> 0)
					{
						$db =& JFactory::getDBO();
						$query = "SELECT username, password FROM #__users WHERE id IN (SELECT user_id FROM #__sdi_account WHERE id= $row->wmsaccount_id)";
						$db->setQuery( $query);
						$row = $db->loadObject();
						$user = $row->username;
						$password = $row->password;
					}
					else
					{
						$user = $object->wmsuser;
						$password = $object->wmspassword;
					}
				}
				break;
		}

		
// 		$session 	= curl_init($url);
// 		$postData 	= file_get_contents( "php://input" );
// 		$httpHeader = array();
		
// 		if (!empty($postData)) 
// 		{
// 			// Set the POST options.
// 			curl_setopt($session, CURLOPT_POST, 1);
// 			// post contains a raw XML document?
// 			if (substr($postData, 0, 1)=='<') 
// 			{
// 				//Contrairement au problème rencontré avec les filtres en GET, les requêtes en POST ne semblent pas
// 				//rencontrer de problème d'encodage. On ne rajoute donc aucune information.
// 				//Attention cependant : si la requête passe par plusieurs proxy installés sur des OS différents (Linux vs Windows)
// 				//son encodage semble être altéré (constatation faite avec un environnement de développement utilisant un proxy
// 				//sous Linux et un proxy sous Windows, proxy en version 2.1.4)
// 				
// 				$httpHeader[]='Content-Type: text/xml; ';
// 			}
// 			curl_setopt($session, CURLOPT_POSTFIELDS, $postData);
// 		}
// 		else 
// 		{
// 			$httpHeader[]='Content-Type: image/png; ';
// 		}
		
// 		if ($user != null && strlen($user)>0 && $password != null && strlen($password)>0) 
// 		{
// 			$httpHeader[]='Authorization: Basic '.base64_encode($user.':'.$password);
// 		}
// 		if (count($httpHeader)>0) 
// 		{
// 			curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
// 		}
	
// 		curl_setopt($session, CURLOPT_HEADER, true);
// 		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
// 		// Do the POST and then close the session
// 		$response = curl_exec($session);
// 		if (curl_errno($session) || strpos($response, 'HTTP/1.1 200 OK')===false) 
// 		{
// 			echo 'cUrl POST request failed.';
// 			if (curl_errno($session))
// 				echo 'Error number: '.curl_errno($session).'';
// 			echo "Server response ";
// 			echo $response;
// 		} 
// 		else 
// 		{
// 			$headers 	= curl_getinfo($session);
				
// 			if (strpos($headers['content_type'], ';')!==false) 
// 			{
// 				$fileType = array_pop(explode(';',$headers['content_type']));
// 				header("Content-Type: ".$fileType[0]);
// 			}
// 			header("Content-Type: image/png");
// 			echo $response;
// 		}
// 		curl_close($session);
		
		
		if ($type == "wms")
			header("Content-Type: image");
		else 
			header("Content-Type: text/xml");
		
		if ( substr($url, 0, 7) == 'http://' ) 
		{
			if ($user !=null && strlen($user)>0)
				$url = "http://$user:$password@".substr($url, 7);
			
			if (!$handle = fopen("$url", "rb"))				
				exit ;
			
			$stringData = stream_get_contents($handle);
			$stringUTF8 ='';
			if ($type == "wms")
			{
				$stringUTF8 = $stringData;
			}
			else
			{
				$xmlEncodingHeader = SITE_proxy::getEncodingHeaderFromXmlContent($stringData);
				fwrite($fh, "xmlEncodingHeader:".$xmlEncodingHeader );	
				
				//try first to read the encoding from the xml file header, if it is not utf-8
				if(strpos(strtoupper($xmlEncodingHeader), "UTF-8") == false){
					$stringUTF8 = utf8_encode($stringData);
				}
				//Check by guessing if it is utf-8
				else if(mb_check_encoding("UTF-8") )
				{
					$stringUTF8 = $stringData;
				}
				else
				{
					$stringUTF8 = utf8_encode($stringData);
				}
			}
			echo $stringUTF8;
			fclose($handle);
		}
		else if ( substr($url, 0, 8) == 'https://' ) 
		{
			if ($user !=null && strlen($user)>0)
				$url = "https://$user:$password@".substr($url, 8);				
						
			if (!$handle = fopen("$url", "rb"))
				exit ;
			
			$stringData = stream_get_contents($handle);
			$stringUTF8='';
			if ($type == "wms")
			{
				$stringUTF8 = $stringData;
			}
			else
			{
				$xmlEncodingHeader = SITE_proxy::getEncodingHeaderFromXmlContent($stringData);
				fwrite($fh, "xmlEncodingHeader:".$xmlEncodingHeader );	
				
				//try first to read the encoding from the xml file header, if it is not utf-8
				if(strpos(strtoupper($xmlEncodingHeader), "UTF-8") == false){
					$stringUTF8 = utf8_encode($stringData);
				}
				//Check by guessing if it was utf-8
				else if(mb_check_encoding("UTF-8") )
				{
					$stringUTF8 = $stringData;
				}
				else
				{
					$stringUTF8 = utf8_encode($stringData);
				}
			}
			echo $stringUTF8;
			fclose($handle); 
		}
	}
	
	
	
	function contains($str, $content, $ignorecase=true)
	{
		if ($ignorecase)
		{
			$str = strtolower($str);
			$content = strtolower($content);
		}  
		return strpos($content,$str) ? true : false;
	}
	
	function getEncodingHeaderFromXmlContent($stringData)
	{
		$xmlEncodingHeader = "";
		$posStartTag = strpos($stringData, "<?xml"); 
		$posEndTag = strpos($stringData, "?>");
		if ($posStartTag !== false && $posEndTag) {
			$strXmlHeader =  substr($stringData, $posStartTag, $posEndTag-$posStartTag);
			$pos = strpos($strXmlHeader, "encoding=");
			if($pos !== false){
				$xmlEncodingHeader = substr($strXmlHeader, ($pos + 9), strlen($strXmlHeader)-$pos - 9);
			}
		}
		return $xmlEncodingHeader;
	}
	
	function getAuthentication ($object, &$user, &$password, $type="")
	{
		if ($object->easysdi_account_id && $object->easysdi_account_id <> 0)
		{
			 $db =& JFactory::getDBO(); 
			 $query = "SELECT username, password FROM #__users WHERE id IN (SELECT user_id FROM #__sdi_account WHERE id= $object->account_id)";
			 $db->setQuery( $query);
			 $row = $db->loadObject();
			 $user = $row->username;
			 $password = $row->password;
		}
		else
		{
			if($type == "preview")
			{
				$user = $object->viewuser;
				$password = $object->viewpassword;
			}
			else
			{
				 $user = $object->user;
				 $password = $object->password;
			}
		}
	}
}
?>