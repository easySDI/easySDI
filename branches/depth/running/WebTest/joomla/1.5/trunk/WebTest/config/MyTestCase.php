<?php
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

class MyTestCase extends PHPUnit_Framework_TestCase {

 protected function setUp() {
  parent::setUp ();

 }

 function testSimple() {
  echo "horray !";
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
    MyTestCase::main();
}
 ?>