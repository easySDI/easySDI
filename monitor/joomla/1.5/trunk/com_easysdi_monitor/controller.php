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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport("joomla.html.pagination");


class MonitorController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}
	
	function create(){
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	  	
		$row =	json_decode(JRequest::getVar("rows"));	
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		$saveSql = "INSERT INTO #__sdi_monitor_exports
		 (exportName, exportType, xsltUrl,exportDesc)
			VALUES ('".$row->exportName."','". $row->exportType."','".$row->xsltUrl."','".$row->exportDesc."')";
		try{
			
			$database->setQuery($saveSql);
			$rows = $database->loadObjectList();
			echo "{success:true}";
					
		
		}
		catch(Exception $e){
			echo "{success:false, error:".$e->getTraceAsString()."}";
		}
		die();
		
	
	}
	function read(){
		
		//echo "read";
	//	die();
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	  		
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		
		$limit		=  JRequest::getVar('limit', 15);
		$limitstart	=  JRequest::getVar('start', 0);
		//get the total
		$query = "select count(*)  from #__sdi_monitor_exports";
		$database->setQuery($query);
		$total = $database->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
		
		$selectsql = "select * from #__sdi_monitor_exports order by id desc";
		
		try{
			
			$database->setQuery( $selectsql, $pagination->limitstart, $pagination->limit);
			$rows = $database->loadObjectList();
			echo "{success:true,results:".$total.",rows:".json_encode($rows)."}";
					
		
		}
		catch(Exception $e){
			echo "{success:false, error:".$e->getTraceAsString()."}";
		}
		die();
	
	}
	function update(){
		//"id":"1","exportName":"name1","exportType":"csw","exportDesc":"desc111","xsltUrl":"url1"
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();
		$usertype= strtolower($User->usertype);	  	
		$row =	json_decode(JRequest::getVar("rows"));
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		$updateSql = "UPDATE			 #__sdi_monitor_exports 
						SET				 exportName='".$row->exportName."' 
										, exportType='".$row->exportType."'
										, exportDesc='".$row->exportDesc."'													
										, xsltUrl='".$row->xsltUrl."'									
					    WHERE id = ".$row->id;
		$database->setQuery($updateSql);
		$result =$database->query();
		if ($database->getErrorNum()) {
				
			echo "{success:false, error:".$e->getTraceAsString()."}";
			die();
		
		}
		echo "{success:true}";
		die();
	
		
	
	}
	function delete(){
		
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	
		$id =	JRequest::getVar("rows");  		
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
	
		$deletesql ="DELETE FROM #__sdi_monitor_exports where id =".$id;
		$database->setQuery($deletesql);
		$result =$database->query();
		if ($database->getErrorNum()) {
				
			echo "{success:false, error:".$e->getTraceAsString()."}";
			die();
		
		}
		echo "{success:true}";
		die();
		
		
	
	}
	
	function requestExportData(){
		
		$database=& JFactory::getDBO(); 
		$fullURI = explode("?",$_SERVER['REQUEST_URI']);
  	
  		$homeUrl = "http://".$_SERVER['HTTP_HOST']."/".$fullURI[0] ;
  		$homeUrl = str_replace("administrator/", "", $homeUrl);
		$exportTypeId = JRequest::getVar("exportID");
		$proxyUrls = JRequest::getVar("proxyUrls"); 
		
		$selectsql = "select xsltUrl, exportType from #__sdi_monitor_exports where id=".$exportTypeId;
	
  		$database->setQuery( $selectsql);
		$row = $database->loadAssoc();
		
		$proxyUrlsArr = explode(",", $proxyUrls );
		$xml = new DOMDocument;
		$xmlData ="";
		$output ="";
	
		foreach ($proxyUrlsArr as $proxyUrl){
				

			$jsondata =   $this->getS2S($homeUrl.$proxyUrl);
			$xmlData = $this->jsonToXml($jsondata);
			$xml->loadXML($xmlData);
			$xsl = new DOMDocument;
			if($row["xsltUrl"]!="")
			{
				$xsl->loadXML($this->getS2S($row["xsltUrl"]));
			}else
			{
				if( $row["exportType"] == "XML"){
					$xsl->load(JPATH_COMPONENT_ADMINISTRATOR.DS."views".DS."main".DS."tmpl".DS."xsl".DS."defaultXML.xsl");
				}else if($row["exportType"] == "XHTML")
				{
					$xsl->load(JPATH_COMPONENT_ADMINISTRATOR.DS."views".DS."main".DS."tmpl".DS."xsl".DS."defaultXHTML.xsl");
				}else // default csv
				{
					$xsl->load(JPATH_COMPONENT_ADMINISTRATOR.DS."views".DS."main".DS."tmpl".DS."xsl".DS."default.xsl");
				}
			}

			// Configure the transformer
			$proc = new XSLTProcessor;
			$proc->importStyleSheet($xsl); // attach the xsl rules
			$output = $output."\n".$proc->transformToXML($xml);

		}
		
		$xmldeclaration = '<?xml version="1.0" encoding="UTF-8"?>';
		
		if( $row["exportType"] == "CSV"){
			$contentType ="text/plain";
			$extension =".csv";
		}else if( $row["exportType"] == "XML"){
			$output = $xmldeclaration."\n".$output;
			$contentType ="text/xml";
			$extension =".xml";
		}else if( $row["exportType"] == "XHTML"){
			
			$contentType ="text/html";
			$extension =".html";
		}
		else{
			$contentType ="text/plain";
			$extension =".csv";
		}
		
		
		
		ini_set('zlib.output_compression', 0);
		header('Content-type:'.$contentType);
		//if (strpos($contentType, "html")===FALSE)
		header('Content-Disposition: attachment; filename="results'.$extension.'"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Pragma: public');
		header("Expires: 0");
		header("Content-Length: ".strlen($output)); // Attention, très important que la taille soit juste, sinon IE pos problème

		echo $output;
		
		die();
	}
	
	function getS2S($url){
		
		$cookiesList=array();
	  	foreach($_COOKIE as $key => $val)
	  	{
	  		$cookiesList[]=$key."=".$val;
	  	}
	  	$cookies= implode(";", $cookiesList);
	
	  	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);	
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	  	curl_setopt($ch, CURLOPT_COOKIE, $cookies);		
		// TODO fix problem with login
		curl_setopt($ch, CURLOPT_USERPWD, "admin:adm");
	
  		curl_setopt($ch, CURLOPT_POST, false);
  		  
	  	
	  	try{
	  		$output = curl_exec($ch);
	  	}
	  	catch(Exception $e){
	
	  		$output =  $e->getTraceAsString();
	  	}
	 // 	$info = curl_getinfo($ch);
	  	curl_close($ch);
	  	return $output ;
	}
	
	function jsonToXml($json){
		//include_once("XML/Serializer.php");
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS."lib".DS."XML".DS."Serializer.php");
	
	    $options = array (
	      'addDecl' => TRUE,
	      'encoding' => 'UTF-8',
	      'indent' => '  ',
	      'rootName' => 'json',
	      'mode' => 'simplexml'
	    );
	
	    $serializer = new XML_Serializer($options);
	    $obj = json_decode($json);
	    if ($serializer->serialize($obj)) {
	        return $serializer->getSerializedData();
	    } else {
	        return null;
	    }
		
	}

}