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
		$user="";
		$password="";
		
		$url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
		
		
		if (SITE_proxy::contains("?",$url)){
			$url=$url."&"; 			
		}else{
			
			$url=$url."?";
		}
		
		$found = false;
		
		//$myFile = "C://testFile2.txt";
		//$fh = fopen($myFile, 'w') or die("can't open file");
		//fwrite($fh, count($_GET)."\n");
		
		foreach($_GET AS $cle => $valeur)     
		{     	     	
			if ($found){
				//$url=$url."$cle=".urlencode($valeur)."&";
				// Problème : le filtre des wfs est également décomposé dans la variable GET en paramètre chaque fois que le signe
				// égal est rencontré. Pour palier à ce problème on enlève les clés qui n'ont pas de valeur ocrrespondantes
				if ((strcasecmp ($valeur,"style"))|| ($valeur != null && strlen($valeur)>0 )){					
					$url=$url."$cle=$valeur&";								
				//fwrite($fh, $cle." ====> ".$valeur."\n");
				}		     		
			}
			if ($cle == "url"){     		
				$found =true;     		
			}     	            
		}
		
		$url = str_replace ('\"','"',$url);
		$url = str_replace (' ','%20',$url);
		 
		//fclose($fh);
		
		$basemapscontentid = ($_POST['basemapscontentid']) ? $_POST['basemapscontentid'] : $_GET['basemapscontentid'];
		if ($basemapscontentid != null && strlen($basemapscontentid)>0)
		{
			//Get the username and password for this basemapId	
			global  $mainframe;
			$db =& JFactory::getDBO(); 

			$query = "select * from #__sdi_basemapcontent where id = $basemapscontentid"; 
			$db->setQuery( $query);
			$row = $db->loadObject();

			SITE_proxy::getAuthentication ($row, $user, $password);
			//$user = $row->user; 
			//$password = $row->password;
		}
		else
		{
			$perimeterdefid = ($_POST['perimeterdefid']) ? $_POST['perimeterdefid'] : $_GET['perimeterdefid'];
			if ($perimeterdefid != null && strlen($perimeterdefid)>0)
			{
				//Get the username and password for this basemapId	
				global  $mainframe;
				$db =& JFactory::getDBO(); 
	
				$query = $query = "SELECT * FROM #__sdi_perimeter WHERE id = $perimeterdefid"; 
				$db->setQuery( $query);
				$row = $db->loadObject();
				SITE_proxy::getAuthentication ($row, $user, $password);

					
			}		
			else
			{
				$locationid = ($_POST['locationid']) ? $_POST['locationid'] : $_GET['locationid'];
				if ($locationid != null && strlen($locationid)>0)
				{
					//Get the username and password for this basemapId	
					global  $mainframe;
					$db =& JFactory::getDBO(); 
		
					$query = $query = "SELECT * FROM #__sdi_location WHERE id = $locationid"; 
					$db->setQuery( $query);
					$row = $db->loadObject();
					SITE_proxy::getAuthentication ($row, $user, $password);
							 
				}	
				else
				{
					$previewId = ($_POST['previewId']) ? $_POST['previewId'] : $_GET['previewId'];
					if($previewId != null && strlen($previewId)>0)
					{
						//Get the username and password for the product preview	
						global  $mainframe;
						$db =& JFactory::getDBO(); 
			
						$query = $query = "SELECT * FROM #__sdi_product WHERE id = $previewId"; 
						$db->setQuery( $query);
						$row = $db->loadObject();
						SITE_proxy::getAuthentication ($row, $user, $password, "preview");
						
					}
				}
			}
		}
		
		$type = ($_POST['type']) ? $_POST['type'] : $_GET['type'];
		if ($type == "wms"){
			header("Content-Type: image");
		}else {
			header("Content-Type: text/xml");
		}
		if ( substr($url, 0, 7) == 'http://' ) {
			
			if ($user !=null && strlen($user)>0){
				$url = "http://$user:$password@".substr($url, 7);
														
			}
			
			//$myFile = "/home/users/asitvd/http";
			//$fh = fopen($myFile, 'w') or die("can't open file");
			//fwrite($fh, $url );  
			
			if (!$handle = fopen("$url", "rb")){				
				exit ;
			}; 
			$stringData = stream_get_contents($handle);
			$stringUTF8 ='';
			if ($type == "wms")
			{
				$stringUTF8 = $stringData;
			}
			else
			{
				$xmlEncodingHeader = SITE_proxy::getEncodingHeaderFromXmlContent($stringData);
				//fwrite($fh, "xmlEncodingHeader:".$xmlEncodingHeader );	
				
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
				/*
				if(mb_check_encoding($stringData,"UTF-8") )
				{
					$stringUTF8 = $stringData;
				}
				else
				{
					$stringUTF8 = utf8_encode($stringData);
				}
				*/
			}
			
			
			//fwrite($fh, $stringUTF8 );
			echo $stringUTF8;
			
			/*while ( !feof($handle) ) {
				$stringData = fread($handle, 8192);
				 
				echo $stringData;
				fwrite($fh, $stringData );  
			} */
			fclose($handle);
			//fclose($fh); 
		}else if ( substr($url, 0, 8) == 'https://' ) {
			
			if ($user !=null && strlen($user)>0){
				$url = "https://$user:$password@".substr($url, 8);				
			}
			//$myFile = "/home/users/asitvd/http";
			//$fh = fopen($myFile, 'w') or die("can't open file");
			//fwrite($fh, $url ); 
			
			if (!$handle = fopen("$url", "rb")){
				exit ;
			}; 
			$stringData = stream_get_contents($handle);
			$stringUTF8='';
			if ($type == "wms")
			{
				$stringUTF8 = $stringData;
			}
			else
			{
				$xmlEncodingHeader = SITE_proxy::getEncodingHeaderFromXmlContent($stringData);
				//fwrite($fh, "xmlEncodingHeader:".$xmlEncodingHeader );	
				
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
			//fwrite($fh, $stringUTF8 );
			echo $stringUTF8;
			
			
			/*while ( !feof($handle) ) { 
				echo fread($handle, 8192); 
			} */
			//fclose($fh); 
			fclose($handle); 
		}
	}
	
	
	
	function contains($str, $content, $ignorecase=true){
		if ($ignorecase){
			$str = strtolower($str);
			$content = strtolower($content);
		}  
		return strpos($content,$str) ? true : false;
	}
	
	function getEncodingHeaderFromXmlContent($stringData){
		$xmlEncodingHeader = "";
		$posStartTag = strpos($stringData, "<?xml"); 
		$posEndTag = strpos($stringData, "?>");
		if ($posStartTag !== false && $posEndTag) {
			$strXmlHeader =  substr($stringData, $posStartTag, $posEndTag-$posStartTag);
			//xmlheader<?xml version='1.0' encoding="ISO-8859-1"
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