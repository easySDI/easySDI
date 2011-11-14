<?php

define("USER_NOACCESS_ID", 71);
define("ADMIN_TYPE", 0);
define("USER_TYPE", 1);
define("ADMIN_USER","bmagoniadmin");
define("ADMIN_USER_HVANHOECKE","hvanhoecke");
define("REG_USER_MAGONI","bmagoni");
define("REG_USER_KIEGLER","pkiegler");
define("REG_USER_MDROZ","mdrozedit");

//user 75 peter kiegler => pkiegler/test , registered user
//user 66 mdroz editor =>mdrozedit /test , registered user
//user 81 bruno magoni manager => manager/test , registered user
//user 77 hvanhoecke => hvanhoecke/test , admin user.
//user 62 bmagoniadmin => bmagoniadmin/test , admin user.

/*class TEST{
	
	
	public static function main(){
		
		$string = file_get_contents( dirname(__FILE__)."/bmagoni.html");
	//	echo $string;
		preg_match('(<input type="hidden".*name="return".*>)', $string, $matches);
		foreach ($matches as $match)
			echo "found: ".$match."\n";
	
}
}*/



class TESTPROPS{
	
	private static $propArray =null;
	private static $prop_instance =null;

	private function __construct() {
		$prop = trim(file_get_contents( dirname(__FILE__)."/topology_test.json"));	
		self::$propArray=json_decode($prop,true);
		

	}
	public function setCookies($cookies,$user){
		
		self::$propArray["users"][$user]["cookies"]=$cookies;
	
	}
	
	public function setToken($token,$user){
		
		self::$propArray["users"][$user]["token"]=$token;
	}
	public function setReturnValue($value,$user){
		
		self::$propArray["users"][$user]["return"]=$value;
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
	
		//$sql ="update jos_sdi_configuration set value= ".$propArray["catalog_mxquery_url"]." where name code ='CATALOG_MXQUERYURL'";
	//	self::$dbcon->query($sql);
		//$sql ="update jos_sdi_configuration set value= ".$propArray["catalog_url"]." where name code ='CATALOG_URL'";
	//	self::$dbcon->query($sql);
	
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
	try{
		$stmt=   self::$dbcon->query($sql);
		if($stmt!='')
			return $stmt->fetchAll();
		else
			return array(); // empty array.
		
	}catch(Exception $e){
		echo $e->getTraceAsString();
	}
	}

	



}

class URLCON{
	
	//private static $adminCookies = null;
	//private static $userCookies = null;
	private static  $con_instance =null;
	private static $beforeAdminLoginCookies = null;
	private static $beforeUserLoginCookies = null;
	private static $userNoAccessCookies = null; // user id = 71 => Peter Kiegler , registered user only
	private static $adminLoginToken = null;
	private static $userLoginToken = null;
	
	private function __construct() {
		
		self::setCookiesForLogin();
		self::setTokenForLogin();
		self::loginAsAdmins();
		self::loginAsUsers();
		
		
	}
	
	public function loginAsAdmins(){
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		//$adminlogintoken = self::getBeforeLoginToken(ADMIN_TYPE);
		//$adminLoginCookies = self::getCookiesForAdminLogin();
		$params = array();
		$params["option"] ="com_login";
		//$params["passwd"] =$propArray["users"]["kghoorbin"]["password"];
		$params["task"] ="login";
		//$params["username"] =$propArray["users"]["kghoorbin"]["login"];
		
		$params["testmode"]=1;
		
		//echo json_encode($params);
//		$response =self::executeRequest($params, 0);
//		$fp = fopen('responseafterlogin.html', 'w');
//		fwrite($fp, $response);
//		fclose($fp);

		foreach($propArray["admins"] as $adminUser){
			
			$params[$propArray["users"][$adminUser]["token"]]=1;
			$params["passwd"] =$propArray["users"][$adminUser]["password"];	
			$params["username"] =$propArray["users"][$adminUser]["login"];				
			$responseHeader  =self::executeLoginRequestGetHeadersOnly($params, ADMIN_TYPE, $adminUser);
			$cookies = self::getCookieFromResponseHeader($responseHeader);	
		
			$props->setCookies($cookies, $adminUser);
			
		}
//		$responseHeader  =self::executeLoginRequestGetHeadersOnly($params, ADMIN_TYPE);
//		echo "response header after login \n";
//		echo $responseHeader;
//		self::$adminCookies = self::getCookieFromResponseHeader($responseHeader);	
//		$props->setCookiesAfterLogin(self::$adminCookies,"kghoorbin" );

		
	}	

	
	public function loginAsUsers(){
		
		//$userlogintoken = self::getBeforeLoginToken(USER_TYPE);

		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		foreach($propArray["registered"] as $simpleuser){
			$params = array();
			$params["testmode"]=1;
			$params["task"] ="login";
			$params["testmode"]=1;
			$params["return"]=$propArray["users"][$simpleuser]["return"];	
			$params[$propArray["users"][$simpleuser]["token"]]=1;
			$params["passwd"] =$propArray["users"][$simpleuser]["password"];	
			$params["username"] =$propArray["users"][$simpleuser]["login"];				
			$responseHeader  =self::executeLoginRequestGetHeadersOnly($params, USER_TYPE, $simpleuser);	
			$cookies = self::getCookieFromResponseHeader($responseHeader);				
			$props->setCookies($cookies, $simpleuser);
			
		}

		
	}
	

	// request type = 0 for administrator backend
	private function executeLoginRequestGetHeadersOnly($params,$requestType =0, $user){
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		$ch = curl_init();		
	  	if($requestType==ADMIN_TYPE)
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]);
	  	else
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_loginroot"]);
	  	
	  	if ($user){
		  	if( $requestType==ADMIN_TYPE)
		  	  	curl_setopt($ch, CURLOPT_COOKIE, $propArray["users"][$user]["cookies"]);
		  	else
		  	  	curl_setopt($ch, CURLOPT_COOKIE, $propArray["users"][$user]["cookies"]);
	  	}
	  		
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
	  	
// 	  		$fp = fopen($user.'.html', 'w');
// 			fwrite($fp, $output);
// 			fclose($fp);
	  	
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
	
	
	public function executeRequest( $namevaluepair, $requestType=ADMIN_TYPE, $user){
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		$ch = curl_init();
		if($requestType==0)
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]);
	  	else
	  		curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_root"]);
	  	
	  	if( $requestType==0)
	  	  	curl_setopt($ch, CURLOPT_COOKIE, $propArray["users"][$user]["cookies"]);
	  	 else
	  	  	curl_setopt($ch, CURLOPT_COOKIE, $propArray["users"][$user]["cookies"]);
	  	
	  	
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

// 	  	curl_close($ch);
// 	  	$fp = fopen($user.'request.html', 'w');
// 	  	fwrite($fp, $output);
// 	  	fclose($fp);
		return $output;
	}
	
	private function setCookiesForLogin(){
		
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
		
		foreach($propArray["registered"] as $simpleuser){
						
			$responseHeader =self::executeLoginRequestGetHeadersOnly(null, USER_TYPE,null);
			$cookies = self::getCookieFromResponseHeader($responseHeader);	
			$props->setCookies($cookies, $simpleuser);
		
			
		}
		
		foreach($propArray["admins"] as $adminuser){
						
			$responseHeader =self::executeLoginRequestGetHeadersOnly(null, ADMIN_TYPE,null);
			$cookies = self::getCookieFromResponseHeader($responseHeader);	
			$props->setCookies($cookies, $adminuser);
			
		}
		
		
	}
	
	
	public function setTokenForLogin(){
	
		
		$props = TESTPROPS::getInstance();
		$propArray=$props->getProperties();
	
	//	$accountTypes =array("admins", "registered");
	$accountTypes =array( "registered");
		foreach ($accountTypes as $type){
			
			foreach($propArray[$type] as $user){
				$ch = curl_init();
				if($type == "registered")
					curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_frontend_loginroot"]);
				else
					curl_setopt($ch,CURLOPT_URL , $propArray["geodbmeta_http_admin_root"]); 	
						
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	 
			  	curl_setopt($ch, CURLOPT_POST, 0);	  	
			  	
			  	try{
			  		$output = curl_exec($ch);
			  	}
			  	catch(Exception $e){
			
			  		$output =  $e->getTraceAsString();
			  	}
		
			  	curl_close($ch);
		
	  // $fp = fopen($user.'response.html', 'w');
	//fwrite($fp, $output);
	//fclose($fp);
			   $indexofNameQuote = strrpos($output, "name=\"" );
			   $indexofNameQuote += 6; // the length of name=" is 6.
			   $indexofNextQuote = strpos($output, "\"",$indexofNameQuote );
			   $tokenLength = $indexofNextQuote- $indexofNameQuote;
			   $token = substr($output,$indexofNameQuote, $tokenLength );
			   $props->setToken($token, $user );
			   
			   if($type == "registered"){
			     preg_match('(<input type="hidden".*name="return".*>)', $output, $matches);
			  	 $match = trim($matches[0]);
			  	 $indexofValueQuote = strrpos($match, "value=\"" );
			  	 $indexofValueQuote += 7; // the length of name=" is 6.
			  	 $indexofNextQuote = strpos($match, "\"",$indexofValueQuote );
			  	 $returnValueLength = $indexofNextQuote- $indexofValueQuote;
			  	 $returnValue= substr($match,$indexofValueQuote, $returnValueLength );
			  	 $props->setReturnValue($returnValue, $user );
			  	 
			  	 
			   }
				
			}
		}
		
		
	 
	  	
	  	
	}
	
	
	
	
	
	
}

/*
if (!defined('PHPUnit_MAIN_METHOD')) {
	$urlinstance =URLCON::getInstance();
 //  $urlinstance->loginAsAdmin();

    echo "done";
}*/
////
//if (!defined('PHPUnit_MAIN_METHOD')) {
//	$urlinstance =TESTPROPS::getInstance();  
//
//     echo "done";
//}

//if (!defined('PHPUnit_MAIN_METHOD')) {
//	TEST::main();  
//     echo "done";
//}
?>