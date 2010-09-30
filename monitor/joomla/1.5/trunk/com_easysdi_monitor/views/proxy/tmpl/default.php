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
 *
 * original author: Troy Wolf <troy@troywolf.com>
 *
 *   DESCRIPTION: Allow scripts to request content they otherwise may not be
 *                able to. For example, AJAX (XmlHttpRequest) requests from a
 *                client script are only allowed to make requests to the same
 *                host that the script is served from. This is to prevent
 *                "cross-domain" scripting. With proxy.php, the javascript
 *                client can pass the requested URL in and get back the
 *                response from the external server.
 *
 *         USAGE: "proxy_url" required parameter. For example:
 *                http://www.mydomain.com/proxy.php?proxy_url=http://www.yahoo.com
 *
 */


//prevent php to output error here, otherwise errors will be written in the xml output.
defined('_JEXEC') or die('Restricted access');

//check we have an administrator logged in
$user =& JFactory::getUser();
if($user->usertype != "Super Administrator" && $user->usertype != "Administrator"){
	echo "not an admin";
	exit;
}

error_reporting(0);
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once("class_http.php");
$monitorUrl = config_easysdi::getValue("MONITOR_URL");
$myFile = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_monitor'.DS.'views'.DS.'proxy'.DS.'tmpl'.DS.'logs'.DS.'log.txt';
$fh = fopen($myFile, 'w');
$verb = $_SERVER['REQUEST_METHOD'];
$usr = $_SERVER['PHP_AUTH_USER'];
$pwd = $_SERVER['PHP_AUTH_PW'];

$xmldata = file_get_contents('php://input');
if (empty($xmldata)) {
     fwrite($fh, "no data \n");
} else {
     fwrite($fh, "raw data:".$xmldata."\n");
}

/*
if(count($_POST) > 0 || strlen($xmldata)>0)
	$verb = "POST";
*/

fwrite($fh, "logger opened \n");
fwrite($fh, "GET values:".print_r($_GET, true)."\n");
fwrite($fh, "POST values:".print_r($_POST, true)."\n");
fwrite($fh, "SERVER values:".print_r($_SERVER, true)."\n");
fwrite($fh, "Server request method:".$_SERVER['REQUEST_METHOD']."\n");
fwrite($fh, "Auth header username :".$_SERVER['PHP_AUTH_USER']."\n");
fwrite($fh, "Auth header password :".$_SERVER['PHP_AUTH_PW']."\n");


$proxy_url = isset($_GET['proxy_url'])?$_GET['proxy_url']:false;
if (!$proxy_url) {
    header("HTTP/1.0 400 Bad Request");
    echo "proxy.php failed because proxy_url parameter is missing";
    exit();
}

if (substr($proxy_url,0,1) == '/')
  $proxy_url = $monitorUrl.$proxy_url;
else
  $proxy_url = $monitorUrl.'/'.$proxy_url;

fwrite($fh, "Proxy Url :".$proxy_url."\n");

// Instantiate the http object used to make the web requests.
// More info about this object at www.troywolf.com/articles
if (!$h = new http()) {
    header("HTTP/1.0 501 Script Error");
    echo "proxy.php failed trying to initialize the http object";
    exit();
}


//Feed headers

foreach ($_SERVER as $k => $v){
    if (substr($k, 0, 5) == "HTTP_") 
    { 
         $k = str_replace('_', ' ', substr($k, 5)); 
         $k = str_replace(' ', '-', ucwords(strtolower($k))); 
         $h->headers[$k] = $v; 
    } 
} 

//add content type if given

if(isset($_SERVER['CONTENT_TYPE']))
	$h->headers['Content-Type'] = $_SERVER['CONTENT_TYPE']; 



//if get values are set, append the to the url
$proxy_url .= "?";
foreach ($_GET as $key => $value){
	$i = 0;
	if($key != "proxy_url" && $key != "postBody" && $key != "option" && $key != "view"){
		fwrite($fh, "appening key :".$key." value :".$value."\n");
		if($i < (sizeof($_GET)-1)){
			$proxy_url .= $key."=".$value."&";
		}else{
			$proxy_url .= $key."=".$value;
		}
	}
	$i++;
}

//Dispatch special WPS request (doWPSTransformDataset).
$xmlHandle = null;
$content = null;

//postbody = send an xml request with the content of postbody. Mootools
//is not able to handle this correctly.
if(isset($_POST['postBody'])){
	$content = $_POST['postBody']; 
}	


//if we have xml data and no post var, it's an xml request
if(strlen($xmldata) > 0 && count($_POST) == 0)
  
	//Monitor hack:
	//Monitor does not support json collection with root, but EXTJs always send with root
	//so: such a request:
	//{"data":{"url":"foo","id":"bar"}}
	//should become
	//{{"url":"foo","id":"bar"}}
	
	//$content = str_replace("\"undefined\":", "", $xmldata);
	//$content = substr($content, 1, -1);  // retourne "abcde"
	//fwrite($fh, "After Monitor hack :".$content."\n");

	$content = $xmldata;

/*
if(isset($_GET['request'])){
	$request = $_GET['request']; 
	if($request == "doWPSTransformDataset")
	{
		if (!$xmlHandle = fopen("../xml/doWPSTransformDataset.xml", "rb")){
					fwrite($fh, "xmlhandle : content null \n");
					exit ;
				}; 
				while ( !feof($xmlHandle) ) { 
					fwrite($fh, "xmlhandle : content ok \n");
					$content = fread($xmlHandle, 8192); 
				} 
				fclose($xmlHandle); 
	}
}
*/

fwrite($fh, "xmlContent :".$content."\n");


if($content != null){
	$h->xmlrequest = $content;
	fwrite($fh, "xml request \n");
}
else{
	$h->postvars = $_POST;
	fwrite($fh, "post request \n");
}

fwrite($fh, "check for json in:".$_SERVER['CONTENT_TYPE']."\n");
//if(stristr($string, 'terre') === FALSE) {
if(stristr($_SERVER['CONTENT_TYPE'], 'json') === FALSE){
}else{
	$h->postvars = $xmldata;
	
	//HACK
	/*
	if($_SERVER['REQUEST_METHOD'] != "DELETE"){
	   $temp = explode("{", $h->postvars);
   	   $temp = explode("}", $temp[2]);
   	   $kvp = explode(",", $temp[0]);
   	   $new_string  = "{\"data\":{";
   	   for($i = 0; $i < count($kvp); $i++){
	   	    $strkeyval = "";
	   	    if(stristr($kvp[$i], 'slaStartTime') === FALSE && stristr($kvp[$i], 'slaEndTime') === FALSE && stristr($kvp[$i], 'url') === FALSE ) {
	   		    $temp = explode(":", $kvp[$i]);
	   	            $key = trim($temp[0]);
	   	            $val = trim($temp[1]);
	   	            if(!startsWith($val,"\""))
	   		        $val = "\"".$val."\"";
	   		    $strkeyval = $key.":".$val;
	   	    }
	   	    else
	   	    {
	   	            $strkeyval = $kvp[$i];
	   	    }
	   	    
	   	   if($i < (count($kvp)-1))
	   		   $new_string .= $strkeyval.",";
	   	   else
	   	   	   $new_string .= $strkeyval;
   	   }
   	   $new_string .= "}}";
   	   $h->postvars  = $new_string;
	}
	*/
	
	
	
}

fwrite($fh, "final url is :".$proxy_url."\n");


//check if auth needed
$url = parse_url($proxy_url);
$url['query'] = str_replace("?", "&",$url['query']);
//print_r($url);
/*
�scheme - e.g. http 
�host 
�port 
�user 
�pass 
�path 
�query - apr�s le marqueur de question ? 
�fragment - apr�s la hachure # 
*/

//require auth
//$username = $usr == "" ? $url['user'] : $usr;
//$password = $pwd == "" ? $url['pass'] : $pwd;
/**
We have to fix one time with which credentials we authenticate. (Url or headers)
*/
$username = $url['user'];
$password = $url['pass'];
	  if($username != ""){
			if($url['port'] == "")
				$h->url = $url['scheme']."://".$url['host'].$url['path']."?".$url['query'];
			else
			  $h->url = $url['scheme']."://".$url['host'].":".$url['port'].$url['path']."?".$url['query'];
			  //fetch($url="", $timetolive=0, $name="", $user="", $pwd="", $verb="GET")
			    if (!$h->fetch($h->url, 0, null, $username,$password, $verb)){
			       //header("HTTP/1.0 501 Script Error");
			       fwrite($fh, "proxy.php had an error attempting to query the url with auth, ".$h->log."\n");
			  }
			  fwrite($fh, "Auth with username:".$username." password:".$password." on:".$h->url."\n");
		}
		//no auth
		else{
			$h->url = $proxy_url;
			//fetch($url="", $timetolive=0, $name="", $user="", $pwd="", $verb="GET")
			if (!$h->fetch($h->url, 0, null, null, null, $verb)){
			    //header("HTTP/1.0 501 Script Error");
			    fwrite($fh, "proxy.php had an error attempting to query the url with auth, ".$h->log."\n");
			}
		}

// Forward the headers to the client.
$ary_headers = split("\n", $h->header);

fwrite($fh, "headers :".$ary_headers."\n");

fwrite($fh, "log: ".$h->log."\n");

//send headers back to caller
foreach($ary_headers as $hdr) { header($hdr); }

// Send the response body to the client.
fwrite($fh, "Response is :".$h->body."\n");

$enc = mb_detect_encoding($h->body);
fwrite($fh, "encoding is".$enc."\n");

//HACK return a good log structure 
//@see http://forge.easysdi.org/issues/show/207
//no hack = do only echo $h->body;
$lgHd = '{"success":true,"message":"Les logs ont bien été récupérés","data":';
if(stristr($h->body, $lgHd) === FALSE){
   echo $h->body;
   fwrite($fh, "log no hacked json \n");
}else{
   //echo $h->body;
   echo substr($h->body, strlen($lgHd), -1);
   fwrite($fh, "log hacked json".substr($h->body, strlen($lgHd), -1)."\n");
}
   
//close log
fclose($fh);
die;

function startsWith($haystack,$needle,$case=true) {
       if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
       return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
    }
?>