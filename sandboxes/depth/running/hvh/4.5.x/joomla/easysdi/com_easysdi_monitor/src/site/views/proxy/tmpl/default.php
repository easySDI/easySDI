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
 * Curl based proxy for Monitor
 *
 * initialy based on PageForward v1.5 by Joshua Dick
 *
 */
//error_reporting(0);

global $fh;
global $requ_headers;
global $resp_header;

global $monitorUser;
global $monitorPassword;

// Used for image/data handler
$req_Content = "";



$params  = JComponentHelper::getParams('com_easysdi_monitor');

// TODO Config changes needed
$monitorUrl = $params->get('servletUrl');
$monitorUser = $params->get('monitorUser');
$monitorPassword = $params->get('monitorPassword');

//logger
/*$myFile = JPATH_SITE.'/components/com_easysdi_monitor/views/proxy/tmpl/logs/log.txt';
$fh = fopen($myFile, 'w');*/
 
//request headers
$requ_headers = Array();

//response headers
$resp_header = Array();

$start_time = microtime();

//read request headers
/*fwrite($fh, "-----------------------------\n");
fwrite($fh, "REQUEST HEADERS\n");
fwrite($fh, "-----------------------------\n");*/
foreach ($_SERVER as $k => $v){
	  
	  //fwrite($fh, $k.":".$v."\n");
	  
	  if (substr($k, 0, 12) == "CONTENT_TYPE") 
    {
    	$k = "Content-Type";
    	$requ_headers[] = $k.":".$v;
		  //fwrite($fh, "forward->:".$k.":".$v."\n");
	  }
	  
	  
	  if (substr($k, 0, 14) == "CONTENT_LENGTH") 
    {
    	$k = "Content-Length";
    	$requ_headers[] = $k.":".$v;
		  //fwrite($fh, "forward->:".$k.":".$v."\n");
	  }
	 
	  
    if (substr($k, 0, 5) == "HTTP_") 
    { 
         $k = str_replace('_', ' ', substr($k, 5)); 
         $k = str_replace(' ', '-', ucwords(strtolower($k)));	 
	       if($k == "Accept-Encoding"){
		        //comment following lines to disable gzip
		        $requ_headers[] = $k.":".$v;
		        //fwrite($fh, "forward->:".$k.":".$v."\n");
	       }else{
	         $requ_headers[] = $k.":".$v;
	         //fwrite($fh, "forward->:".$k.":".$v."\n");
	       }
    } 
}

$proxy_url = isset($_GET['proxy_url'])?$_GET['proxy_url']:false;
if (!$proxy_url) {
    header("HTTP/1.0 400 Bad Request");
    echo "Failed because proxy_url parameter is missing";
    exit();
}
$url = "";
if (substr($proxy_url,0,1) == '/')
  $url = $monitorUrl.$proxy_url;
else
  $url = $monitorUrl.'/'.$proxy_url;

  
if(stristr($url, '?') === FALSE)
   $url .= "?";
  else
   $url .= "&";
  
foreach ($_GET as $key => $value){
	$i = 0;
	if($key != "proxy_url" && $key != "postBody"){
		
		if($key == "option"){
		   if($_GET['option'] == "com_easysdi_monitor")
		      continue;
	  }
	  if($key == "view"){
		   if($_GET['view'] == "proxy")
		      continue;
	  }
	  
		if($i < (sizeof($_GET)-1)){
			$url .= $key."=".$value."&";
		}else{
			$url .= $key."=".$value;
		}
	}
	$i++;
}

$indexStrStart = strrpos($url,"contenttype");
if(!($indexStrStart === false))
{	
	$tempStr = substr($url,$indexStrStart,strlen($url)-1);	
	$indexStrEnd = strpos($tempStr,"&");
	if($indexStrEnd === false)
	{
		$req_Content = substr($tempStr,0,strlen($tempStr)-1);	
	}else
	{
		$req_Content = substr($tempStr,0,$indexStrEnd);
	}
	$req_Content = substr($req_Content,strpos($req_Content,"=")+1,strlen($req_Content)-1);
}
     
  $HTML = getFile($url,$req_Content);
  
    foreach($resp_header as $h)
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
                       //fwrite($fh, "sending->".$h);
                       header($h);
                    }
                 }
       //remove the headers form the curl response text
        if($req_Content !== '')
            {
                    $HTML = str_replace($h, "", $HTML);
            }else
            {
                    $HTML = str_replace(trim($h), "", $HTML);
            }
    }
	
  print $HTML; //Output the page using print_r so that frames at least partially are written
  
  //disallows futher buffers in output 
  die();

function read_header($url, $str) {
   global $resp_header;
   if(strlen($str) > 0) {
     $resp_header[] = $str;
   }
   return strlen($str);   
}

//Retrieves a file from the web.
function getFile($fileLoc,$req_Content)
{

     global $requ_headers;
     global $monitorUser;
     global $monitorPassword;
     //global $fh;
   
     $verb = $_SERVER['REQUEST_METHOD'];
     //Sends user-agent of actual browser being used--unless there isn't one.
     $user_agent = $_SERVER['HTTP_USER_AGENT'];
     if (empty($user_agent)) {
        $user_agent = "Mozilla/5.0 (compatible; PageForward Proxy)";
     }
     $ch = curl_init($fileLoc);

     curl_setopt($ch, CURLOPT_USERPWD, $monitorUser.':'.$monitorPassword);
     curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
     
   if($verb == "GET" && $req_Content !== '' && strpos($req_Content,"image") !== false)
     {
        // Imagehandler
        header("Content-type: ".$req_Content);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
     }else{
        if($verb == "GET"){
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
        }else if($verb == "POST"){
           $data = json_decode(file_get_contents('php://input'),true);
           curl_setopt($ch, CURLOPT_HEADER, 1);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
           if ($data['data']['data.id']<>''){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
           }
           curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data['data']));

           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }else if($verb == "PUT"){
            $data = json_decode(file_get_contents('php://input'),true)['data'];
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');  
           curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($data));
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        }else if($verb == "DELETE"){
           $xmlBody = file_get_contents('php://input');
           curl_setopt($ch, CURLOPT_HEADER, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');  
           curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlBody);
           curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        }else{
             //send not implemented header
           header('Method Not Implemented',true,501);
           return "";
        }
     }
     
     $file = curl_exec($ch);
     
     $info = curl_getinfo($ch);
     
     //If auth requested
     if($info['http_code'] == 401){
     	   header('WWW-Authenticate: Basic realm="Authentification Monitor"',true,401); 
     }
     
     curl_close($ch);
     return $file;
}

?>