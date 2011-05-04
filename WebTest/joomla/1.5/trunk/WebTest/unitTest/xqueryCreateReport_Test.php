<?php

require_once 'PHPUnit/Autoload.php';
//require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once dirname(__FILE__)."/../config/testUtils.php";

class xqueryCreateReport extends PHPUnit_Framework_TestCase {
	
	protected static $report_id =null;
	protected function setUp() {
		parent::setUp ();

 }
 
 // this function tests create operation
 function testCreateNewReport() {
 	
 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="saveXQueryReport";
	$params["xQueryReportName"]="myUnitTestReport";

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0, ADMIN_USER);

	$sql = "select id from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	self::assertEquals(1, count($rows));
	$report = $rows[0];
	self::$report_id = $report[id];
	
	//echo "reportid =".$report[id]."fin";
//
//  $fp = fopen('response.html', 'w');
//	fwrite($fp, $response);
//	fclose($fp);
 }
 
 // this function tests update operations 
 function testUpdateProperties(){
// 	 self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

		
    $params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="saveXQueryReport";
	$params["xsltUrl"] ="";
	$params["metadataIdSql"] ="select guid from #__sdi_metadata";
	$params["ogcfilter"] ="nofilter";
	$params["reportcode"] =	"for \$x in \$doc/csw:GetRecordsResponse/csw:SearchResults	return \$x/gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString";
	
	$params["description"] ="reportcode";
	$params["applicationType"] =0;
	$params["cid"] =self::$report_id;
	$params["xQueryReportName"]="myUnitTestReport";
	 

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0, ADMIN_USER);
	
	$sql = "select * from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);

	self::assertEquals(1, count($rows));
		$report  = $rows[0];
	
	self::assertEquals("select guid from #__sdi_metadata", trim($report["sqlfilter"]));
        
 }
 
 function testAssignToUsers(){
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="saveXQueryUserReportAccess";	
	$params["cid"] =self::$report_id;
	
	// 81 = bruno magoni
	// 82 = thierry bussien
	// 72 = marcel droz
	// 71 = peter kiegler
	$usersToAdd = array(71,72,81,82);
	$usersToAddlist = join(";", $usersToAdd);	
	$params["add"] =$usersToAddlist;

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0, ADMIN_USER);
	
	$sql = "select user_id from jos_sdi_xqueryreportassignation where report_id=".self::$report_id." order by user_id";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	
	$allUsers = array_merge($usersToAdd, array(123)); // 123 is test user kiran ghoorbin
	self::assertEquals(5, count($rows)); // 4 added plus creator.
		//$report  = $rows[0];
	$i= 0;
	foreach($rows as $row){
		self::assertEquals($allUsers[$i], $row["user_id"]);
		$i++;
	}
	
	//self::assertEquals("select id from #__sdi_metadata", trim($report["sqlfilter"]));
 }
 
 function testRemoveUserAccess(){

 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="saveXQueryUserReportAccess";	
	$params["cid"] =self::$report_id;
	
	// 81 = bruno magoni
	// 82 = thierry bussien
	// 72 = marcel droz
	// 71 = peter kiegler
	$usersToRemove = array(72,82);
	$usersToAddlist = join(";", $usersToRemove);	
	$params["remove"] =$usersToAddlist;

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0, ADMIN_USER);
	$allUsers = array(71,81,123); // 123 is t
	$sql = "select user_id from jos_sdi_xqueryreportassignation where report_id=".self::$report_id." order by user_id";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	

	self::assertEquals(count($allUsers), count($rows)); // 4 added plus creator.
	
	$i= 0;
	foreach($rows as $row){
		self::assertEquals($allUsers[$i], $row["user_id"]);
		$i++;
	}
 }
 
 function testAdminProvideXML(){
 	
 	//test without cid
 	$params = array();
 	
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="provideXMLDataForXQueryReport";


	$httpcon =URLCON::getInstance();
	$response =$httpcon->executeRequest($params, 0, ADMIN_USER);
 	//echo "response1". $response ."\n";
	self::assertTrue(stripos($response, "error" ) !== FALSE, "this should be an error response");
 	//test without access
	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="provideXMLDataForXQueryReport";
	$params["cid"] =self::$report_id;


	//another admin who is not explicitly mentionned to have access
	$httpcon =URLCON::getInstance();
	$response =$httpcon->executeRequest($params, 0, ADMIN_USER_THIERRY);
 //	echo "response2". $response ."\n";
	self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "this should be valid xml"); 	
	
	
 	//test with correct cid and access 	
	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="provideXMLDataForXQueryReport";
	$params["cid"] =self::$report_id;
	$httpcon =URLCON::getInstance();
	$response =$httpcon->executeRequest($params, 0, ADMIN_USER);
	//echo "response3". $response ."\n";
	self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "This should be a valid xml" );
	
 }
 
 
 function testAdminExecuteQuery(){
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
	//admin who defined it
 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="processXQueryReport";	
	$params["cid"] =self::$report_id;

	$httpcon =URLCON::getInstance();
	$response = $httpcon->executeRequest($params, 0, ADMIN_USER);
	$response = trim($response);
	//echo "response process".$response."\n";
 	//self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "This should be a valid xml" );
	self::assertTrue(stripos($response, "exception") ===FALSE, "This should be a valid response, no exception" );
	self::assertTrue(stripos($response, "error")===FALSE, "This should be a valid response, no error" );
	self::assertTrue(strlen($response)>0, "This should be a valid response not blank" );
 	
 	
 	//another admin of the company
 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="processXQueryReport";	
	$params["cid"] =self::$report_id;

	$httpcon =URLCON::getInstance();
	$response = $httpcon->executeRequest($params, 0, ADMIN_USER_THIERRY);
	$response = trim($response);
 	//self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "This should be a valid xml" );

	self::assertTrue(stripos($response, "exception") ===FALSE, "This should be a valid response, no exception" );
	self::assertTrue(stripos($response, "error")===FALSE, "This should be a valid response, no error" );
	self::assertTrue(strlen($response)>0, "This should be a valid response, not blank" );
 }
 
 function testUserProvideXML(){
 	
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

 	//test with correct cid and access 	
	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="provideXMLDataForXQueryReport";
	$params["cid"] =self::$report_id;
	$httpcon =URLCON::getInstance();
	$response =$httpcon->executeRequest($params, 1, REG_USER_KIEGLER);

	self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "This should be a valid xml" );

 }
 
 
 function testUserExecuteQuery(){
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="processXQueryReport";	
	$params["cid"] =self::$report_id;

	$httpcon =URLCON::getInstance();
	$response=$httpcon->executeRequest($params, 1, REG_USER_KIEGLER);
	
	//self::assertTrue(simplexml_load_string(trim($response))!== FALSE, "This should be a valid xml" );
	self::assertTrue(stripos($response, "exception") ===FALSE, "This should be a valid response, no exception" );
	self::assertTrue(stripos($response, "error")===FALSE, "This should be a valid response, no error" );
	self::assertTrue(strlen($response)>0, "This should be a valid response, not blank" );
	
 	
 }
 
 function testDeleteReport(){
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

 	$params = array();
	$params["option"] ="com_easysdi_catalog";	
	$params["task"] ="deleteXQueryReport";	
	$params["cid"] =self::$report_id;

	$httpcon =URLCON::getInstance();
	$response = $httpcon->executeRequest($params, 0, ADMIN_USER);
 	$sql = "select id from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	self::assertEquals(0, count($rows));
	
	$sql = "select * from jos_sdi_xqueryreportassignation where report_id=xqueryname=".self::$report_id;
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	self::assertEquals(0, count($rows));
	
 }
 protected function tearDown() {
 	
// 	$sql = "delete  * from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
//	$db = DBUTIL::getInstance();
//	$db->executeSQL($sql);
  	parent::tearDown();
 }
 
 static function main() {

  $suite = new PHPUnit_Framework_TestSuite( __CLASS__);
  PHPUnit_TextUI_TestRunner::run( $suite);
//	self::testCreateNewReport();
//	self::testAssignToUsers();
//	self::testUpdateProperties();
//	self::testRemoveUserAccess();
//	self::testAdminProvideXML();
//	self::testAdminExecuteQuery();
//	self::testUserProvideXML();
//	self::testUserProvideXML();
//	self::testDeleteReport();
 }
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    xqueryCreateReport::main();
}
?>