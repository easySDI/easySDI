<?php

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework.php';
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
	$httpcon->executeRequest($params, 0);

	$sql = "select id from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	self::assertEquals(1, count($rows));
	$report = $rows[0];
	self::$report_id = $report[id];
	
	echo "reportid =".$report[id]."fin";
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
	$params["xsltUrl"] ="xsltUrl";
	$params["metadataIdSql"] ="select id from #__sdi_metadata";
	$params["ogcfilter"] ="nofilter";
	$params["reportcode"] ="reportcode";
	$params["description"] ="reportcode";
	$params["applicationType"] =2;
	$params["cid"] =self::$report_id;
	$params["xQueryReportName"]="myUnitTestReport";
	 

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0);
	
	$sql = "select * from jos_sdi_xqueryreport where xqueryname='myUnitTestReport'";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);

	self::assertEquals(1, count($rows));
		$report  = $rows[0];
	
	self::assertEquals("select id from #__sdi_metadata", trim($report["sqlfilter"]));
        
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
	$httpcon->executeRequest($params, 0);
	
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
	$usersToRemove = array(71,72);
	$usersToAddlist = join(";", $usersToRemove);	
	$params["remove"] =$usersToAddlist;

	$httpcon =URLCON::getInstance();
	$httpcon->executeRequest($params, 0);
	$allUsers = array(81,82,123); // 123 is t
	$sql = "select user_id from jos_sdi_xqueryreportassignation where report_id=".self::$report_id." order by user_id";
	$db = DBUTIL::getInstance();
	$rows = $db->executeSQL($sql);
	

	self::assertEquals(count($allUsers), count($rows)); // 4 added plus creator.
		//$report  = $rows[0];
	$i= 0;
	foreach($rows as $row){
		self::assertEquals($allUsers[$i], $row["user_id"]);
		$i++;
	}
 }
 
 function testAdminProvideXML(){
 	
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

 	//test witjout cid
 	
 	//test without access

 }
 
 
 function testAdminExecuteQuery(){
 	self::markTestIncomplete(
          'This test has not been implemented yet.'
        );
 	
 }
 
 function testUserProvideXML(){
 	
// 	self::markTestIncomplete(
//          'This test has not been implemented yet.'
//        );

 }
 
 
 function testUserExecuteQuery(){
 	self::markTestIncomplete(
          'This test has not been implemented yet.'
        );
 	
 }
 
 function testDeleteReport(){
 	self::markTestIncomplete(
          'This test has not been implemented yet.'
        );
 }
 protected function tearDown() {
  parent::tearDown();
 }
 
 static function main() {

  $suite = new PHPUnit_Framework_TestSuite( __CLASS__);
  PHPUnit_TextUI_TestRunner::run( $suite);
 }
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    xqueryCreateReport::main();
}
?>