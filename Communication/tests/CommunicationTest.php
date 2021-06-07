<?php

class CommunicationTest extends PHPUnit\Framework\TestCase
{
    private $user;
    private $isUnitTest;

    public function setUp() : void {
        parent::setUp();

        $this->user = "UnitTests";
        $this->isUnitTest = "";

        // Create test user data file if not exists
        if ( !file_exists("Communication/data/{$this->user}.json") ) { // No transactions or defects will exist yet
            $this->testCanAddDefect();
            $this->testCanAddTransaction();
        }
    }

    public function tearDown() : void {
        parent::tearDown();

        // Delete the test user data file
        unlink("Communication/data/{$this->user}.json");
    }

    public function testUnitTestsAreWorking()
    {
        $result = 7;

        $this->assertEquals(7, $result);
    }

    public function testCanAddDefect()
    {
        $user = $this->user;
        $isUnitTest = $this->isUnitTest;
        $sn = 'SNUnitTest001';
        $defectName = 'Missing Part';
        $now = (new \DateTime())->format("Y-m-d H:i:s");
        $action = "addDefect";
        $actual = include "Communication/API.php";

        $this->assertTrue( $actual );
    }

    public function testCanAddTransaction()
    {
        $user = $this->user;
        $isUnitTest = $this->isUnitTest;
        $sn = 'SNUnitTest001';
        $job = 'ROUTING001';
        $operation = 'Initialisation';
        $now = (new \DateTime())->format("Y-m-d H:i:s");
        $action = "addTransaction";
        $actual = include "Communication/API.php";

        $this->assertTrue( $actual );
    }
    
    public function testCanGetRoutings()
    {
        $user = $this->user;
        $isUnitTest = $this->isUnitTest;

        $action = "getRoutings";
        $actual = include "Communication/API.php";

        if ( isset($actual["ROUTING001"]) ) {
            $this->assertEquals(get_class(unserialize($actual["ROUTING001"])), "Blockchain");

        } else if ( isset($actual["ROUTING002"]) ) {
            $this->assertEquals(get_class(unserialize($actual["ROUTING002"])), "Blockchain");

        } else {
            fail("Can not get routings.");
        }

        $this->assertEquals(gettype($actual), "array");
    }

    public function testCanGetTransactions()
    {
        $user = $this->user;
        $isUnitTest = $this->isUnitTest;

        $action = "getTransactions";
        $actual = include "Communication/API.php";

        if ( isset($actual["SNUnitTest001"]) ) {
            $this->assertEquals(get_class(unserialize($actual["SNUnitTest001"])), "Blockchain");

        } else {
            fail("Can not get routings.");
        }

        $this->assertEquals(gettype($actual), "array");
    }

    public function testCanGetDefects()
    {
        $user = $this->user;
        $isUnitTest = $this->isUnitTest;

        $action = "getDefects";
        $actual = include "Communication/API.php";

        if ( isset($actual["SNUnitTest001"]) ) {
            $this->assertEquals(get_class($actual["SNUnitTest001"]), "Blockchain");

        } else {
            fail("Can not get routings.");
        }

        $this->assertEquals(gettype($actual), "array");
    }

    public function testCanCreateCheckForAndRemoveLockFile() {
        
        include 'Communication/API.php'; // Include the functions to create, checkFor and remove .lock files
        
        createLock("", "UnitTests");
        $this->assertTrue( file_exists("Communication/data/UnitTests.lock") );
        
        // Check using the API that the lock is in place
        $this->assertTrue( isLocked("", "UnitTests") );
        
        removeLock("", "UnitTests");
        $this->assertFalse( file_exists("Communication/data/UnitTests.lock") );
    }
}
