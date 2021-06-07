<?php

class SampleMESTest extends PHPUnit\Framework\TestCase
{
    private $model;
    private $isUnitTest;
    
    public function setUp() : void {
        parent::setUp();

        $this->isUnitTest = "";
        
        if ( !class_exists("app\Model\AppModel") ) {
            require 'SampleMES/app/Model/AppModel.php';
        }
        
        $this->model = new app\Model\AppModel();
        
    }

    public function tearDown() : void {
        parent::tearDown();
    }
    
    public function testUnitTestsAreWorking()
    {
        $result = 7;

        $this->assertEquals(7, $result);
    }
    
    public function testCanSetAndGetApplicationVariable() 
    {
        $this->model->setVar("sn", "sn001");
        
        $this->assertEquals( "SN001", $this->model->getVar("sn") ); // sn and job should be forcing upper case only
    }
    
    public function testCanAddAndReadNotices() 
    {
        $msg = "Notice";
        $this->model->addNotice($msg);
        
        $this->assertEquals( $msg, $this->model->getNotices()[0] );
    }
    
    public function testCanAddAndReadWarnings() 
    {
        $msg = "Warning";
        $this->model->addWarning($msg);
        
        $this->assertEquals( $msg, $this->model->getWarnings()[0] );
    }
    
    public function testCanAddAndReadErrors() 
    {
        $msg = "Error";
        $this->model->addError($msg);
        
        $this->assertEquals( $msg, $this->model->getErrors()[0] );
    }
    
    public function testCanCleanseUserInput() 
    {
        $badMsg = "<>'\"&"; // Some bad characters we dont want users using maliciously
        $goodMsg = htmlspecialchars(trim($badMsg)); // This is the minimum level of security we need in the application
        
        $this->assertEquals( $goodMsg, $this->model->cleanse($badMsg) );
    }
    
    public function testCanCheckValidCredentials() 
    {
        
        $username = "UnitTests";
        $isUnitTest = $this->isUnitTest;
        $action = "checkCredentials";
        $actual = include "Communication/API.php";
        
        $this->assertTrue( $actual );
    }
    
    public function testCanCheckInvalidCredentials() 
    {
        
        $username = "OtherUser";
        $isUnitTest = $this->isUnitTest;
        $action = "checkCredentials";
        $actual = include "Communication/API.php";
        
        $this->assertFalse( $actual );
    }
}
