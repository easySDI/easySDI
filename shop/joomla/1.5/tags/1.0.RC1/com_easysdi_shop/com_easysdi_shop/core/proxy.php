<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
		
		
		$url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
		
		
		if (SITE_proxy::contains("?",$url)){
			$url=$url."&"; 			
		}else{
			
			$url=$url."?";
		}
		
		$found = false;
		/*$myFile = "C://testFile2.txt";
		$fh = fopen($myFile, 'w') or die("can't open file");
		  
			fwrite($fh, count($_GET)."\n");*/	
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
		if ($basemapscontentid != null && strlen($basemapscontentid)>0){
			//Get the username and password for this basemapId	
				global  $mainframe;
				$db =& JFactory::getDBO(); 
	
				$query = "select * from #__easysdi_basemap_content where id = $basemapscontentid"; 
				$db->setQuery( $query);
				$row = $db->loadObject();

				$user = $row->user; 
				$password = $row->password;
					
		}else{
			
		 
		$perimeterdefid = ($_POST['perimeterdefid']) ? $_POST['perimeterdefid'] : $_GET['perimeterdefid'];
		if ($perimeterdefid != null && strlen($perimeterdefid)>0){
			//Get the username and password for this basemapId	
				global  $mainframe;
				$db =& JFactory::getDBO(); 
	
				$query = $query = "SELECT * FROM #__easysdi_perimeter_definition WHERE id = $perimeterdefid"; 
				$db->setQuery( $query);
				$row = $db->loadObject();

				$user = $row->user; 
				$password = $row->password;
				
					
		}		else{
		$locationid = ($_POST['locationid']) ? $_POST['locationid'] : $_GET['locationid'];
		if ($locationid != null && strlen($locationid)>0){
			//Get the username and password for this basemapId	
				global  $mainframe;
				$db =& JFactory::getDBO(); 
	
				$query = $query = "SELECT * FROM #__easysdi_location_definition WHERE id = $locationid"; 
				$db->setQuery( $query);
				$row = $db->loadObject();

				$user = $row->user; 
				$password = $row->password;
					 
		}	}
		
		
					
			
			
		
			
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
			
			/*$myFile = "C://testFile.txt";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $url );  
			*/



			
			
			
			if (!$handle = fopen("$url", "rb")){				
				exit ;
			}; 
			
			while ( !feof($handle) ) {
				$stringData = fread($handle, 8192);
				 
				echo $stringData;
			//	fwrite($fh, $stringData );  
			} 
			fclose($handle);
		//	fclose($fh); 
		}else if ( substr($url, 0, 8) == 'https://' ) {
			
			if ($user !=null && strlen($user)>0){
				$url = "https://$user:$password@".substr($url, 8);				
			}
			
			
			if (!$handle = fopen("$url", "rb")){
				
				exit ;
			}; 
			while ( !feof($handle) ) { 
				echo fread($handle, 8192); 
			} 
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
	
	
	
}
?>