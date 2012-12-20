<?php 

	// Load Joomla! configuration file
	require_once ('configuration.php');
	$jconfig 			= new JConfig();
	$tablePrefix		= $jconfig->dbprefix ;
	$catalogUrlBase 	= "http://localhost:8080/proxy/ogc/LOCAL_CATALOG";
	$catalogUser		= "user";
	$catalogPassword 	= "password";
	$queryXPath			= "//gmd:MD_Metadata/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:citation/gmd:CI_Citation/gmd:title";
	$user				= "62";

	// Connection du database
	$db =& mysql_pconnect($jconfig->host, $jconfig->user, $jconfig->password);
	
	if (!$db)
	{
		die('Can not etablish connection with the database : ' . mysql_error());
	}
	$db_selected = mysql_select_db($jconfig->db, $db);
	if (!$db_selected) {
		die ('Can not select the database : ' . mysql_error());
	}
	
	//Set UTF8
	mysql_query("SET NAMES utf8");
	
	// Get metadata 
	$mdList	= mysql_query("SELECT guid FROM ".$tablePrefix."sdi_metadata");
	if(!$mdList){
		die('Can not get metadata guid.');
	}
	$mds = array();
	while ($row = mysql_fetch_object( $mdList ))
	{
		$mds[] = $row;
	}
	mysql_free_result( $mdList );
	
	//Get languages
	$langs = array();
	$langList = mysql_query( "SELECT l.id, l.name, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi,l.gemetlang FROM ".$tablePrefix."sdi_language l, ".$tablePrefix."sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
	if(!$langList){
		die('Can not get languages.');
	}
	while ($row = mysql_fetch_object( $langList ))
	{
		$langs[] = $row;
	}
	mysql_free_result( $langList );
	
	// Get namespaces
	$namespaces = array();
	$namespacelist = mysql_query( "SELECT prefix, uri FROM ".$tablePrefix."sdi_namespace ORDER BY prefix" );
	if(!$namespacelist){
		die('Can not get namespace.');
	}
	while ($row = mysql_fetch_object( $namespacelist ))
	{
		$namespaces[] = $row;
	}
	mysql_free_result( $namespacelist );
	$gmdURI				= "";
	$gcoURI				= "";
	
	$countok = 0;
	$countko = 0;
	// Loop through metadata
	foreach ($mds as $md)
	{
	
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=".$md->guid;
		$cswResults = DOMDocument::loadXML(CURLRequest("GET", $catalogUrlGetRecordById));
		
		$doc = new DOMDocument('1.0', 'UTF-8');
		
		if ($cswResults <> false and $cswResults->childNodes->item(0)->hasChildNodes())
		{
			print_r("<br>".'Metadata found :'.$md->guid);
			$countok ++;
			$xpath = new DOMXPath($cswResults);
		}
		else if ($cswResults->childNodes->item(0)->nodeName == "ows:ExceptionReport")
		{
			print_r("<br>".'Error for '.$md->guid.' : '.$cswResults->childNodes->item(0)->nodeValue);
			$countko ++;
			continue;
		}
		else
		{
			print_r("<br>".'Metadata not found :'.$md->guid);
			$countko ++;
			continue;
		}
		$xpath->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
		$xpath->registerNamespace('srv','http://www.isotc211.org/2005/srv');
		$xpath->registerNamespace('xlink','http://www.w3.org/1999/xlink');
		$xpath->registerNamespace('gts','http://www.isotc211.org/2005/gts');
		foreach ($namespaces as $namespace)
		{
			$xpath->registerNamespace($namespace->prefix,$namespace->uri);
			if($namespace->prefix == "gmd") $gmdURI = $namespace->uri;
			if($namespace->prefix == "gco") $gcoURI = $namespace->uri;
		}
		
		$elements = $xpath->query($queryXPath);
		if (!is_null($elements) && $elements->length > 0) {
			$element 				= $elements->item(0);
			$CharacterString_EL 	= $element->getElementsByTagNameNS($gcoURI,"CharacterString");
			$PT_FreeText_EL 		= $element->getElementsByTagNameNS($gmdURI,"PT_FreeText");
				
			if($CharacterString_EL->length == 1 && $PT_FreeText_EL->length == 0)//Not a multilingual field : the unique value must be save for all the languages
			{
				$nodeValue = $CharacterString_EL->item(0)->nodeValue;
				foreach($langs as $lang)
				{
					$countList = mysql_query ("SELECT COUNT(*) as n FROM ".$tablePrefix."sdi_translation WHERE element_guid ='".$md->guid."' AND language_id=".$lang->id);
					$count = array();
					while ($row = mysql_fetch_object( $countList ))
					{
						$count[] = $row;
					}
					mysql_free_result( $countList );
					if($count[0]->n > 0){
						$query = "UPDATE ".$tablePrefix."sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user."
						WHERE element_guid = '".$md->guid."' AND language_id=".$lang->id;
					}else{
						$query = "INSERT INTO ".$tablePrefix."sdi_translation (element_guid, language_id, title, created, createdby)
						VALUES ('".$md->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user." )";
					}
					$result  = mysql_query($query);
					if (!$result){
						print_r("Can not store the title of : ".$md->guid);
					}
					mysql_free_result( $result );
				}
			}
				
			else if($PT_FreeText_EL->length > 0)//Multilingual field
			{
				$nodeValue = $CharacterString_EL->item(0)->nodeValue;
				foreach($langs as $lang)
				{
					if($lang->defaultlang)
					{
						$countList = mysql_query ("SELECT COUNT(*) as n FROM ".$tablePrefix."sdi_translation WHERE element_guid ='".$md->guid."' AND language_id=".$lang->id);
						$count = array();
						while ($row = mysql_fetch_object( $countList ))
						{
							$count[] = $row;
						}
						mysql_free_result( $countList );
						if($count[0]->n > 0){
							$query = "UPDATE ".$tablePrefix."sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user."
							WHERE element_guid = '".$md->guid."' AND language_id=".$lang->id;
						}else{
							$query = "INSERT INTO ".$tablePrefix."sdi_translation (element_guid, language_id, title, created, createdby)
							VALUES ('".$md->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user." )";
						}
						$result  = mysql_query($query);
						if (!$result){
							print_r("Can not store the title for : ".$md->guid);
						}
						mysql_free_result( $result );
						break;
					}
				}
				
				for ($i = 0 ; $i < $PT_FreeText_EL->length ; $i++ )
				{
					$freeTextNode = $PT_FreeText_EL->item($i);
 					$textGroupNodes = $freeTextNode->getElementsByTagNameNS($gmdURI,"textGroup");
 					$textGroupNode = $textGroupNodes->item(0);
 					$LocalisedCharacterStringNodes = $textGroupNode->getElementsByTagNameNS($gmdURI,"LocalisedCharacterString");
 					$LocalisedCharacterStringNode = $LocalisedCharacterStringNodes->item(0);
 					$attributes = $LocalisedCharacterStringNode->attributes;
 					$locale = $attributes->getNamedItem("locale");
 					$nodeValue = $LocalisedCharacterStringNode->nodeValue;
 					foreach($langs as $lang)
 					{
 						if("#".$lang->code == $locale->nodeValue)
 						{
							$countList = mysql_query ("SELECT COUNT(*) as n FROM ".$tablePrefix."sdi_translation WHERE element_guid ='".$md->guid."' AND language_id=".$lang->id);
 							$count = array();
							while ($row = mysql_fetch_object( $countList ))
							{
								$count[] = $row;
							}
							mysql_free_result( $countList );
 							if($count[0]->n > 0){
 								$query = "UPDATE ".$tablePrefix."sdi_translation SET title = '".addslashes($nodeValue)."' , updated = NOW(), updatedby = ".$user."
 											WHERE element_guid = '".$md->guid."' AND language_id=".$lang->id;
 							}else{
 								$query = "INSERT INTO ".$tablePrefix."sdi_translation (element_guid, language_id, title, created, createdby)
 											VALUES ('".$md->guid."', ".$lang->id.", '".addslashes($nodeValue)."', NOW(), ".$user." )";
 							}
 							$result  = mysql_query($query);
							if (!$result){
								print_r("Can not store the title for : ".$md->guid);
							}
							mysql_free_result( $result );
 							break;
 						}
 					}
 					
				}
			}
		}
		else
		{
			//No title found in the metadata, so delete in database
			$query = "DELETE FROM  ".$tablePrefix."sdi_translation WHERE element_guid = '".$md->guid."' ";
			$result  = mysql_query($query);
			if (!$result){
				print_r("Can not delete the title for : ".$md->guid);
			}
			mysql_free_result( $result );
		}
	}

	// Close database connection
	mysql_close($db);
	print_r('<br>Total 	  : '.count($mds));
	print_r('<br>Total OK : '.$countok);
	print_r('<br>Total KO : '.$countko);
	print_r('<hr>Synchronization done!<hr>');
	
	function CURLRequest($type, $url)
	{
		global $catalogUser, $catalogPassword ;
		$cookiesList=array();
		foreach($_COOKIE as $key => $val)
		{
			$cookiesList[]=$key."=".$val;
		}
		$cookies= implode(";", $cookiesList);
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"'));
		curl_setopt($ch, CURLOPT_COOKIE, $cookies);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_ENCODING, "");
	
		if ($type=="POST")
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
		}
		else if ($type=="GET")
		{
			curl_setopt($ch, CURLOPT_POST, 0);
		}

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
		curl_setopt($ch, CURLOPT_USERPWD, $catalogUser.":".$catalogPassword);
	
		$output = curl_exec($ch);
		curl_close($ch);
	
		return $output;
	}
?>