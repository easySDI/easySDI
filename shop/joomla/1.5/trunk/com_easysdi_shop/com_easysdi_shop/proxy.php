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

header("Content-Type: text/xml"); 

$url = ($_POST['url']) ? $_POST['url'] : $_GET['url'];
$url = str_replace ('\"','"',$url);
$url = str_replace (' ','%20',$url);


if ( substr($url, 0, 7) == 'http://' ) {
	   
  if (!$handle = fopen($url, "rb")){
  	
  	exit ;
  }; 
  while ( !feof($handle) ) { 
  echo fread($handle, 8192); 
 } 
  fclose($handle); 
}
?>