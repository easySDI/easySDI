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
		global  	$mainframe;
		$db 		=& JFactory::getDBO();
		$user		= "";
		$password	= "";
		$url 		= ($_POST['url']) ? $_POST['url'] : $_GET['url'];
		$found 		= false;
		
		$url = urldecode($url);
				
		if (SITE_proxy::contains("?",$url))
			$url=$url."&"; 			
		else
			$url=$url."?";
		
// 		$myFile = "C://proxy.txt";
// 		$fh = fopen($myFile, 'w') or die("can't open file");
		
		
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
					// fwrite($fh, $cle." ====> ".$valeur."\n");
				}		     		
			}
			if ($cle == "url")
			{     		
				$found =true;     		
			}     	            
		}
// 		fwrite($fh,$url."\n");
// 		fclose($fh);
// 		$url 		= str_replace ('\"','"',$url);
// 		$url 		= str_replace (' ','%20',$url);
		$type 		= ($_POST['type']) ? $_POST['type'] : $_GET['type'];
				
		$basemapscontentid 	= ($_POST['basemapscontentid']) ? $_POST['basemapscontentid'] : $_GET['basemapscontentid'];
		$perimeterdefid		= ($_POST['perimeterdefid']) ? $_POST['perimeterdefid'] : $_GET['perimeterdefid'];
		$locationid 		= ($_POST['locationid']) ? $_POST['locationid'] : $_GET['locationid'];
		$previewId 			= ($_POST['previewId']) ? $_POST['previewId'] : $_GET['previewId'];
		$gridId 			= ($_POST['gridid']) ? $_POST['gridid'] : $_GET['gridid'];
		$case 				= 0;
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
				$query = $query = "SELECT * FROM #__sdi_grid WHERE id = $gridId";
				$db->setQuery( $query);
				$grid = $db->loadObject();
				
				if($type == "wfs")
				{
					if($grid->wfsaccount_id && $grid->wfsaccount_id <> 0)
					{
						$query = "SELECT username, password FROM #__users WHERE id IN (SELECT user_id FROM #__sdi_account WHERE id= $grid->wfsaccount_id)";
						$db->setQuery( $query);
						$row = $db->loadObject();
						$user = $row->username;
						$password = $row->password;
					}
					else
					{
						$user = $grid->wfsuser;
						$password = $grid->wfspassword;
					}
				}
				else
				{
					if($grid->wmsaccount_id && $grid->wmsaccount_id <> 0)
					{
						$query = "SELECT username, password FROM #__users WHERE id IN (SELECT user_id FROM #__sdi_account WHERE id= $grid->wmsaccount_id)";
						$db->setQuery( $query);
						$row = $db->loadObject();
						$user = $row->username;
						$password = $row->password;
					}
					else
					{
						$user = $grid->wmsuser;
						$password = $grid->wmspassword;
					}
				}
				break;
		}
		
		$session 	= curl_init($url);
		$postData 	= file_get_contents( "php://input" );
// 		fwrite($fh,$postData."\n");
		
		$httpHeader = array();

		if (!empty($postData)) 
		{
			// Set the POST options.
			curl_setopt($session, CURLOPT_POST, 1);
			// post contains a raw XML document?
			if (substr($postData, 0, 1)=='<') 
			{
				//Contrairement au problème rencontré avec les filtres en GET, les requêtes en POST ne semblent pas
				//rencontrer de problème d'encodage. On ne rajoute donc aucune information.
				//Attention cependant : si la requête passe par plusieurs proxy installés sur des OS différents (Linux vs Windows)
				//son encodage semble être altéré (constatation faite avec un environnement de développement utilisant un proxy
				//sous Linux et un proxy sous Windows, proxy en version 2.1.4)
				$httpHeader[]='Content-Type: text/xml; ';
			}
			curl_setopt($session, CURLOPT_POSTFIELDS, $postData);
		}
		
		if ($user != null && strlen($user)>0 && $password != null && strlen($password)>0) 
		{
			$httpHeader[]='Authorization: Basic '.base64_encode($user.':'.$password);
		}
		if (count($httpHeader)>0) 
		{
			curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
		}

		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// Do the POST and then close the session
		echo curl_exec($session);
		if (curl_errno($session) ) 
		{
			echo 'cUrl POST request failed.';
			if (curl_errno($session))
				echo 'Error number: '.curl_errno($session).'';
			echo "Server response ";
		} 
		curl_close($session);
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