<?php
/*
PageForward v1.5
by Joshua Dick
http://pageforward.sourceforge.net

If you did not download this file from Sourceforge.net, please redownload from
http://sourceforge.net/project/showfiles.php?group_id=156341 to ensure that you have the lastest version.

PageForward includes support for loading media (images, videos, etc.) through the proxy. This feature is still
very experimental and may break some pages. Therefore, it is turned off by default; to turn it on, change
$proxify_media to true in the user config section.

CHANGELOG:
See http://pageforward.sf.net/changelog.txt for the PageFoward changelog.

PageForward (referred to as 'PF' for the remainder of this text) is a PHP program that will let you surf
the web through a makeshift proxy (the web server the program runs on) to bypass things like internet
content filters, or to browse the internet anonymously.

INSTALLATION AND USAGE:
Change the first two uncommented lines (the ones that start with dollar signs) below this information if
you need to. Upload this file to a web server running PHP. Make sure the program has proper execute permissions.
The program is used by going to http://your.webserver.com/[x.php]?url=[y] in a browser where [x.php] is the
name of this file, and [y] is the URL of a site that you wish to view through the proxy. Links you click on
will be 'redirected' through the proxy; things typed in the address bar of your browser will not (unless they
appear after the 'url='.) Note that you can change the name of this .php file to anything you want.

OTHER INFO:
This program is released under the GNU Lesser GPL. It is a modified version of Simple Browser Proxy
(located at http://sbp.sf.net) and I'd like to thank the original author for writing and freely distributing
SBP and its source code. PF is free to use on its own, but if you are integrating it or part of it into your
own program, please mention PF, the fact that it is released under the LGPL, and its home on the internet,
http://pageforward.sourceforge.net. The author of this program takes no responsibility for any
consequence of the use of this program. This program does not absolutely guarantee secure anonymous
browsing, it is only a simple proxy browser. Use it at your own risk.
*/
//error_reporting(0);
//**BEGIN USER CONFIG**
//Page to display by default (if no URL is supplied)




defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');


class SITE_proxy {

private static $fh = null;
private static $requ_headers = null;
private static $resp_header = null;
private static $pwd = "";
private static $isRequestForWps = false;

function fetch($url, $output=true, $currentUser=null){

$default_url = "http://www.google.com";
//Tag to prepend page titles
$title_tag = "PF--";
//Attempt to load media (images, movies, scripts, etc.) through the proxy (EXPERIMENTAL)
$proxify_media = false;
//**END USER CONFIG**

//logger
$myFile = "C:\wamp\www\web2\components\com_easysdi_publish\core\log_new.txt";
self::$fh = fopen($myFile, 'w');
fwrite(self::$fh, "-----------------------------\n");

//fwrite(self::$fh, "SERVER values:".print_r($_SERVER, true)."\n");
 
//request headers
self::$requ_headers = Array();

//response headers
self::$resp_header = Array();

$start_time = microtime();

//read request headers
fwrite(self::$fh, "-----------------------------\n");
fwrite(self::$fh, "REQUEST HEADERS\n");
fwrite(self::$fh, "-----------------------------\n");
foreach ($_SERVER as $k => $v){
	  
	  //fwrite(self::$fh, $k.":".$v."\n");
	  
	   if (substr($k, 0, 12) == "CONTENT_TYPE") 
    {
    	$k = "Content-Type";
    	          self::$requ_headers[] = $k.":".$v;
		  fwrite(self::$fh, $k.":".$v."\n");
	  }
	  
	  
	  if (substr($k, 0, 14) == "CONTENT_LENGTH") 
    {
    	$k = "Content-Length";
    	self::$requ_headers[] = $k.":".$v;
		  fwrite(self::$fh, $k.":".$v."\n");
	  }
	 
	  
    if (substr($k, 0, 5) == "HTTP_") 
    { 
         $k = str_replace('_', ' ', substr($k, 5)); 
         $k = str_replace(' ', '-', ucwords(strtolower($k)));	 
	       if($k == "Accept-Encoding"){
		        //comment following lines to disable gzip
		        //self::$requ_headers[] = $k.":".$v;
		        //fwrite(self::$fh, $k.":".$v."\n");
	       }else{
	         self::$requ_headers[] = $k.":".$v;
	         fwrite(self::$fh, $k.":".$v."\n");
	       }
    } 
}
fwrite(self::$fh, "-----------------------------\n");
fwrite(self::$fh, "\n");

//if get values are set, append the to the url

fwrite(self::$fh, "received url:".$url."\n");

  
if(stristr($url, '?') === FALSE)
   $url .= "?";
  else
   $url .= "&";
   
fwrite(self::$fh, "-----------------------------\n");
fwrite(self::$fh, "REQUEST PARAMS\n");
fwrite(self::$fh, "-----------------------------\n");
foreach ($_GET as $key => $value){
	$i = 0;
	
	if($key != "proxy_url" && $key != "postBody"&& $key != "option"&& $key != "task"&& $key != "Itemid"){
		if($i < (sizeof($_GET)-1)){
			$url .= $key."=".$value."&";
		}else{
			$url .= $key."=".$value;
		}
		fwrite(self::$fh, $key."=".$value."\n");
	}
	$i++;
}

//is the request for the wps?
if (strstr($url, "servletPublish") != false){
   self::$isRequestForWps = true;
   fwrite(self::$fh,"Handling a request for the WPSPublisher\n");
}else{
	 fwrite(self::$fh,"Handling an HTTP request\n");
}

//replace servletPublish with the real url with user credentials
$wpsAddress = config_easysdi::getValue("WPS_PUBLISHER");
$joomlaUser = JFactory::getUser();
$database =& JFactory::getDBO();
$query = "select password from #__users where username='".$joomlaUser->name."'";
$database->setQuery($query);
self::$pwd = $database->loadresult();
$url_wps = parse_url($wpsAddress);

$wpsAddress = str_replace("//", "//".$joomlaUser->name.":".self::$pwd, $wpsAddress);

$url = str_replace("servletPublish", $url_wps['scheme']."://".$url_wps['host'].":".$url_wps['port']."/sdi_publish/wps/services", $url);

  fwrite(self::$fh, "-----------------------------\n");
  fwrite(self::$fh, "resulting url:".$url."\n");
  fwrite(self::$fh, "\n");
    

  fwrite(self::$fh, "getfile\n");
  $HTML = SITE_proxy::getFile($url, $currentUser);
  fwrite(self::$fh, "end getfile\n");
  
  fwrite(self::$fh, "Raw response:-->".$HTML."<--\n");
        
	//print_r(self::$resp_header);
	//self::$resp_header[] = "Host:www.valid.asitvd.ch:8080";
	fwrite(self::$fh, "-----------------------------\n");
	fwrite(self::$fh, "Response headers\n");
	fwrite(self::$fh, "-----------------------------\n");
	foreach(self::$resp_header as $h)
	{          
         //because curl read entirely the response, we
         //do not have to forward this.	
		     if($h == "Transfer-Encoding: chunked\r\n"){
		     	  $HTML = str_replace($h, "", $HTML);
		        continue;   
		     }
		        
		     //don't send empty header
		     if(trim($h) != ""){
			//Only send Content-Type header, if sending HTTP 1.1 OK
			//It causes a bug if response length > 8000 char
		     	if (substr($h, 0, 12) == "Content-Type")
	       	        {
		           fwrite(self::$fh, "sending->".$h);
		     	   header($h);
		     	}
	       	     }

	   //remove the headers form the curl response text
	   $HTML = str_replace(trim($h), "", $HTML);
  	}
  	
  	//suppress all crlf 
  	$HTML = str_replace("\r\n", "", $HTML);
  	
  	
  	
  	
  fwrite(self::$fh, "-----------------------------\n");
  fwrite(self::$fh, "\n");
	
	fwrite(self::$fh, "output:".$HTML);
	
	/*
	sending->HTTP/1.1 200 OK
sending->Date: Sun, 03 Oct 2010 18:02:54 GMT
sending->Server: Apache/2.2.11 (Win32) PHP/5.2.9-2
sending->Last-Modified: Sun, 03 Oct 2010 18:02:47 GMT
sending->ETag: "27000000061587-94d-491ba3c407360"
sending->Accept-Ranges: bytes
sending->Content-Length: 2381
sending->Keep-Alive: timeout=5, max=100
sending->Connection: Keep-Alive
sending->Content-Type: text/plain
sending->
	*/
	//echo "-->".$HTML."<--";
  
	
				if($output){
        	print $HTML; //Output the page using print_r so that frames at least partially are written
          flush();
          fclose(self::$fh);
    			exit;
        }else{
        	return $HTML;
        }
        
        //Calculate execution time and add HTML comment with that info
        //$duration = microtime_diff($start_time, microtime());
        //$duration = sprintf("%0.3f", $duration);
        //echo ("\n<!-- PageForward v1.5 took $duration seconds to construct this page.-->");
    //}
    
}

//Finds the nth position of a string within a string. (Stolen from http://us3.php.net/strings).
function strnpos($haystack, $needle, $occurance, $pos = 0) {
    
    for ($i = 1; $i <= $occurance; $i++) {
        $pos = strpos($haystack, $needle, $pos) + 1;
    }
    return $pos - 1;
}

//URL parser that works better than PHP's built-in function.
function parseURL($url)
{
    //protocol(1), auth user(2), auth password(3), hostname(4), path(5), filename(6), file extension(7) and query(8)
    $pattern = "/^(?:(http[s]?):\/\/(?:(.*):(.*)@)?([^\/]+))?((?:[\/])?(?:[^\.]*?)?(?:[\/])?)?(?:([^\/^\.]+)\.([^\?]+))?(?:\?(.+))?$/i";
    preg_match($pattern, $url, $matches);
    
    $URI_PARTS["scheme"] = $matches[1];
    $URI_PARTS["host"] = $matches[4];
    $URI_PARTS["path"] = $matches[5];
    
    return $URI_PARTS;
}

//Calculates the differences in microtime captures
function microtime_diff($a, $b)
{
    list($a_dec, $a_sec) = explode(" ", $a);
    list($b_dec, $b_sec) = explode(" ", $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}

function read_header($url, $str) {
   if(strlen($str) > 0) {
     self::$resp_header[] = $str;
   }
   return strlen($str);   
}

//Retrieves a file from the web.
function getFile($fileLoc, $currentUser)
{     
     $verb = $_SERVER['REQUEST_METHOD'];
     if(stristr($fileLoc, 'operation=listPublicationServers'))
         $verb='GET';
     //Sends user-agent of actual browser being used--unless there isn't one.
     $user_agent = $_SERVER['HTTP_USER_AGENT'];
     if (empty($user_agent)) {
        $user_agent = "Mozilla/5.0 (compatible; PageForward Proxy)";
     }
     //$fileLoc = "http://localhost:8080/geoserver/ows";
     $ch = curl_init($fileLoc);
 
     //Tells CURL not to send expect:100 continue header. (transformDataset)
     //cause then he do not read the other ones..
     //But Jetty on geoserver doesn't seem to like this, so
     //do not set it for him. (getfeatureinfo on layer preview fails)
     if(stristr($fileLoc, '/geoserver/') === FALSE){
     		fwrite(self::$fh, "Removed 100 continue header \n");
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
     }else{
        fwrite(self::$fh, "kept 100 continue header \n");
     }
     //authentication for Post Put
     if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
	       fwrite(self::$fh, "Auth with: ".$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']."\n");
         curl_setopt($ch, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']);
     }else if(stristr($fileLoc, 'geoserver/rest/styles/')){
     	   //add credentials for geoserver rest interface automatically
     	   /*
     	   $wpsPublish = config_easysdi::getValue("WPS_PUBLISHER");
     	   $wpsConfig = $wpsPublish."/config";

				 //get diffuser list from wps
				 $url = $wpsConfig."?operation=listPublicationServers";
  	     $doc = SITE_proxy::fetch($url, false);
  	     $xml = simplexml_load_string($doc);
  	     $servers = $xml->server;
  	     
  	     //get diffuser id from user
  	     $database =& JFactory::getDBO();
  	     $joomlaUser = JFactory::getUser();
			   $query = "select u.publish_user_diff_server_id from #__sdi_publish_user u, #__sdi_account p where u.easysdi_user_id=p.id AND p.user_id=".$joomlaUser->id;
				 $database->setQuery($query);
				 $sid = $database->loadResult();
				 
				 $diffusor = $servers->xpath("//server[@id=$sid]");
				 $diffusor = $diffusor[0];
				 			*/	 
				 			
				 fwrite(self::$fh, "Auth with (for geoserver rest auto login): ".$currentUser->diffusion_username.':'.$currentUser->diffusion_password."\n");
         curl_setopt($ch, CURLOPT_USERPWD, $currentUser->diffusion_username.':'.$currentUser->diffusion_password);
     }
     if($verb == "GET"){
	      fwrite(self::$fh, "GET:".$fileLoc."\n");
	      //curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	      curl_setopt($ch, CURLOPT_HTTPHEADER,self::$requ_headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'SITE_proxy::read_header');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
     }else if($verb == "POST"){
	      $xmlBody = file_get_contents('php://input');
        fwrite(self::$fh, "POST\n");
	      fwrite(self::$fh, "xmlBody:".$xmlBody."\n");
        curl_setopt($ch, CURLOPT_HTTPHEADER,self::$requ_headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'SITE_proxy::read_header');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     }else if($verb == "PUT"){
        fwrite(self::$fh, "PUT\n");
	      $xmlBody = file_get_contents('php://input');
	      curl_setopt($ch, CURLOPT_HTTPHEADER,self::$requ_headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'SITE_proxy::read_header');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	      fwrite(self::$fh, "xmlBody:".$xmlBody."\n");
     }else if($verb == "DELETE"){
        fwrite(self::$fh, "DELETE\n");
	      $xmlBody = file_get_contents('php://input');
	      curl_setopt($ch, CURLOPT_HTTPHEADER,self::$requ_headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'SITE_proxy::read_header');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	      fwrite(self::$fh, "xmlBody:".$xmlBody."\n");
     }else{
     	  //send not implemented header
        header('Method Not Implemented',true,501);
        return "";
     }
     
     $file = curl_exec($ch);
     fwrite(self::$fh, "curl raw output:".$file);
     $error;
     if ($error = curl_error($ch)) {
	      fwrite(self::$fh, "CURL ERROR:".$error."\n");
     }else{
	      fwrite(self::$fh, "CURL SUCCEEDED:".$error."\n");
     }
     
     $info = curl_getinfo($ch);
     
      //fwrite(self::$fh, "CURL infos:".print_r($info)."\n");
     
     //If auth requested
     if($info['http_code'] == 401){
     	   header('WWW-Authenticate: Basic realm="GeoServer Realm"',true,401); 
     	   //header("HTTP/1.1 401 Unauthorized");
         //header(WWW-Authenticate	Basic realm="GeoServer Realm");
     }
     
     curl_close($ch);
     return $file;
}
}
?>