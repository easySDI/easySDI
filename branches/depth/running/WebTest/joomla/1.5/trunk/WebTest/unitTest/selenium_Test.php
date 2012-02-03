<?php
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework.php';
//require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
 
class WebTest extends PHPUnit_Extensions_SeleniumTestCase
{
    protected function setUp()
    {
        $this->setBrowser('*firefox');
        $this->setBrowserUrl('http://localhost/sdiv2/administrator');
    }
 
    public function testTitle()
    {
        $this->open('http://localhost/sdiv2/administrator');
        $this->assertTitleEquals('Example Web Page');
    }
}
if (!defined('PHPUnit_MAIN_METHOD')) {
    WebTest::testSimple();
}
?>
