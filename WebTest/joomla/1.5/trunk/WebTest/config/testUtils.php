<?php

define("USER_NOACCESS_ID", 71);
define("ADMIN_TYPE", 0);
define("USER_TYPE", 1);

class TESTPROPS{
	
	private static $propArray =null;
	private static $prop_instance =null;

	private function __construct() {
		$prop = trim(file_get_contents( dirname(__FILE__)."/topology_test.json"));	
		self::$propArray=json_decode($prop,true);
		
	
	}
	public static function getInstance()
	{
		if (!self::$prop_instance)
		{
			self::$prop_instance = new TESTPROPS();
		}

		return self::$prop_instance;
	}
	
	public function getProperties(){
		
		return self::$propArray;
	}
}


class DBUTIL   {

	private static $dbcon =null ;
	private static $db_Instance =null;

	private function __construct() {
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		self::$dbcon = new PDO('mysql:host='.$propArray["db.hostname"].";dbname=".$propArray["db.testdb"], $propArray["db.username"], $propArray["db.password"]);
		// set
		//	"catalog_mxquery_url" : "localhost:8080/MXQuery",
		//	"catalog_url": "localhost:8080/proxy/ogc/geonetwork",
		//	"proxy_url" : "localhost:8080/proxy"
	
		$sql ="update jos_sdi_configuration set value= ".$propArray["catalog_mxquery_url"]." where name code ='CATALOG_MXQUERYURL'";
		self::$dbcon->query($sql);
		$sql ="update jos_sdi_configuration set value= ".$propArray["catalog_url"]." where name code ='CATALOG_URL'";
		self::$dbcon->query($sql);
	
	}
	public static function getInstance()
	{
		if (!self::$db_Instance)
		{
			self::$db_Instance = new DBUTIL();
		}

		return self::$db_Instance;
	}
	
	public function executeSQL($sql){

		$stmt=   self::$dbcon->query($sql);
		return $stmt->fetchAll();
	}

	



}

class URLCON{
	
	private static $adminCookies = null;
	private static $userCookies = null;
	private static  $con_instance =null;
	private static $beforeAdminLoginCookies = null;
	private static $beforeUserLoginCookies = null;
	private static $userNoAccessCookies = null; // user id = 71 => Peter Kiegler , registered user only
	private static $adminLoginToken = null;
	private static $userLoginToken = null;
	
	private function __construct() {
		
		self::loginAsAdmin();
		//self::loginAsUser();
	}
	
	public function loginAsAdmin(){
		$adminlogintoken = self::getBeforeLoginToken(ADMIN_TYPE);
		self::getCookiesForAdminLogin();
		$params = array();
		$params["option"] ="com_login";
		$params["passwd"] ="kghoorbin";
		$params["task"] ="login";
		$params["username"] ="kghoorbin";
		$params[$adminlogintoken] =1;
		$params["testmode"]=1;
		
		echo json_encode($params);
//		$response =self::executeRequest($params, 0);
//		$fp = fopen('responseafterlogin.html', 'w');
//		fwrite($fp, $response);
//		fclose($fp);
		$responseHeader  =self::executeLoginRequestGetHeadersOnly($params, 0);
		echo "response header after login \n";
		echo $responseHeader;
		self::$adminCookies = self::getCookieFromResponseHeader($responseHeader);	
		echo "admin cookie =" .self::$adminCookies. "\n";
		
	}	

	
	public function loginAsUser(){
		
		$params = array();
		$params["option"] ="com_login";
		$params["passwd"] ="sgillieron";
		$params["task"] ="login";
		$params["username"] ="sgillieron";
		
		$responseHeader  =self::executeLoginRequestGetHeadersOnly($params, 1);
		self::$userCookies = self::getCookieFromResponseHeader($responseHeader);	
	//	echo "user cookie =" .self::$userCookies. "\n";
		
	}
	

	// request type = 0 for administrator backend
	private function executeLoginRequestGetHeadersOnly($params,$requestType =0){
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		$ch = curl_init();		
	  	if($requestType==0)
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]);
	  	else
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_root"]);
	  		
	  	if( $requestType==0)
	  	  	curl_setopt($ch, CURLOPT_COOKIE, self::$adminCookies);
	  	else
	  	  	curl_setopt($ch, CURLOPT_COOKIE, self::$userCookies);
	  		
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 	//only callin the head
	 	//reference :http://icfun.blogspot.com/2008/07/php-get-server-response-header-by.html
	 	curl_setopt($ch, CURLOPT_HEADER, true);
	 	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
	 	
	  	curl_setopt($ch, CURLOPT_POST, true);	
		
	  	$dataArray = array();
	  
	  	$data =self::getUrlParams($params);	
	
	  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	  	
	  	try{
	  		$output = curl_exec($ch);
	  	}
	  	catch(Exception $e){
	
	  		$output =  $e->getTraceAsString();
	  	}

	  	curl_close($ch);  	
	  	
	  	return $output;
	}
	
	private function getCookieFromResponseHeader($responseHeader){
		$headers =explode("\n",$responseHeader );
		
		
		$cookiesArr = array();
		foreach($headers as $header){
			if(strpos($header, "Set-Cookie")!==false){
				$fullcookie = explode(":", $header);
				$cookiesArr[] = trim($fullcookie[1]);
			}
		}
		return join(";",$cookiesArr);
	}
	
	private function getUrlParams($namevaluePair){
		
		$data="";
		if($namevaluePair){
		$dataArray = array();
	  	
	  	while (list($key, $val) = each($namevaluePair)) {
	  		$dataArray[]= "$key=$val";
	  	}

	  	$data =   join("&",$dataArray);
		}
	  	
	  	return $data;
	}
	
	public static function getInstance()
	{
		if (!self::$con_instance)
		{
			self::$con_instance = new URLCON();
		}

		return self::$con_instance;
	}
	
	// request type = 0 if for admin access, 1 = front end access. The url and cookies are different for each case.
	
	
	public function executeRequest( $namevaluepair, $requestType=0){
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		$ch = curl_init();
		if($requestType==0)
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]);
	  	else
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_root"]);
	  	
	  	if( $requestType==0)
	  	  	curl_setopt($ch, CURLOPT_COOKIE, self::$adminCookies);
	  	 else
	  	  	curl_setopt($ch, CURLOPT_COOKIE, self::$userCookies);
	  	
	  	
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	 
	  	curl_setopt($ch, CURLOPT_POST, true);
	
		
	  	$data =self::getUrlParams($namevaluepair);
	
	  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	  	
	  	try{
	  		$output = curl_exec($ch);
	  	}
	  	catch(Exception $e){
	
	  		$output =  $e->getTraceAsString();
	  	}

	  	curl_close($ch);
	  
		return $output;
	}
	
	private function getCookiesForAdminLogin(){
		if(self::$adminCookies==null){
			$responseHeader =self::executeLoginRequestGetHeadersOnly(null, 0);
			$cookies = self::getCookieFromResponseHeader($responseHeader);
			self::$adminCookies= $cookies;
			self::$beforeAdminLoginCookies =$cookies;
		}
		echo "cookies for admin login". self::$adminCookies;
	}
	
	public function getCookiesForRequest(){
		return self::$adminCookies;
	}
	
	private function getBeforeLoginToken($type=ADMIN_TYPE){
	
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		$ch = curl_init();		
		
		if($type==ADMIN_TYPE){
			if(self::$adminLoginToken ==null){
	  			curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]); 	  		
			}
			else 
				 return  self::$adminLoginToken;  
		}
	  	else{
	  		if(self::$userLoginToken ==null){
	  			curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_root"]);
	  		}
	  		else
	  			return self::$userLoginToken;
	  	}
	  		
	 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	 
	  	curl_setopt($ch, CURLOPT_POST, 0);	  	
	  	
	  	try{
	  		$output = curl_exec($ch);
	  	}
	  	catch(Exception $e){
	
	  		$output =  $e->getTraceAsString();
	  	}

	  	curl_close($ch);

	 
	   $indexofNameQuote = strrpos($output, "name=\"" );
	   $indexofNameQuote += 6; // the length of name=" is 6.
	   $indexofNextQuote = strpos($output, "\"",$indexofNameQuote );
	   $tokenLength = $indexofNextQuote- $indexofNameQuote;
	   $token = substr($output,$indexofNameQuote, $tokenLength );
	   
	   if($type==ADMIN_TYPE){
	  	 	self::$adminLoginToken  =$token;
	  	 	
	   }
	   else{
			self::$userLoginToken = $token;
			
	   }		
	  
	   return $token;
	 
	  	
	  	
	}
	
	
	
	
}
//if (!defined('PHPUnit_MAIN_METHOD')) {
//	$urlinstance =URLCON::getInstance();
//    $urlinstance->loginAsAdmin();
//
//     echo "done";
//}
?>