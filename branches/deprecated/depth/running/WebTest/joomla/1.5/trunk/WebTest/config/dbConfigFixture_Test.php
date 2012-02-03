<?php
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
//require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Framework/TestCase.php';
require "testUtils.php";


class configTests extends PHPUnit_Framework_TestCase
{
   
    public function testDBVersion(){    	
  
    	$db= DBUTIL::getInstance();
    	$sql = "select currentversion from jos_sdi_list_module where code ='CATALOG'";
    	$resultArray = $db->executeSQL($sql);
    	self::assertEquals("2.0.2", $resultArray[0]["currentversion"], "The catalog is not at current version. Expected 2.0.2");
    }
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    //setUp();
    configTests::testDBVersion();
}

?>