<?php

class PhpChainTest extends PHPUnit\Framework\TestCase
{
    public function testUnitTestsAreWorking()
    {
        $result = 7;

        $this->assertEquals(7, $result);
    }

    public function testCoreFilesExist() 
    {
        $requiredFiles = [
            "SampleMES/index.php",
            "SampleMES/app/Login.php",
            "SampleMES/config/app.json",
            "Communication/data/pkUnitTests.json",
            "Communication/data/skUnitTests.json",
            "Communication/data/UnitTests.port",
            "Communication/API.php",
            "Communication/gossip.php",
            "Communication/index.php",
            "Communication/run.sh"
        ];
        
        foreach ( $requiredFiles as $file ) {
            $this->assertTrue( file_exists($file) );
        }
    }
}
