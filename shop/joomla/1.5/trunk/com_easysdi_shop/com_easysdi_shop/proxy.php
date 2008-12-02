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

header("Content-Type: text/xml"); 

$url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
$url = str_replace ('\"','"',$url);

    
if ( substr($url, 0, 7) == 'http://' ) { 
  $handle = fopen($url, "rb"); 
  while ( !feof($handle) ) { 
  echo fread($handle, 8192); 
 } 
  fclose($handle); 
}
  
/*try{
    // Get the REST call path from the AJAX application
    // Is it a POST or a GET?
    $url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
    $url = str_replace ('\"','"',$url);

    // Open the Curl session
    $session = curl_init($url);

    // If it's a POST, put the POST data in the body
    if ($_POST['url']) {
        $postvars = '';
        while ($element = current($_POST)) {
            $postvars .= key($_POST).'='.$element.'&';
            next($_POST);
        }
        curl_setopt ($session, CURLOPT_POST, true);
        curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);
    }

    // Don't return HTTP headers. Do return the contents of the call
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

    // Make the call
    $xml = curl_exec($session);
    // The web service returns XML. Set the Content-Type appropriately
    header("Content-Type: text/xml");
	curl_close($session);
    echo $xml;

    
}
catch (Exception $e) {
    echo "Capture de l'exception : ",  $e->getMessage(), "\n";
}
*/
?>