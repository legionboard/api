<?php

namespace LegionBoard\tests\units\Lib;

require_once __DIR__ . '/../../../lib/legionboard.php';

use atoum;

class LegionBoard extends atoum
{

    public function setUp() {
        copy(__DIR__ . '/configuration.ini', __DIR__ . '/../../../lib/configuration.ini');
    }

    public function beforeTestMethod($method)
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function tearDown() {
        unlink(__DIR__ . '/../../../lib/configuration.ini');
    }

    public function testEndpoint()
    {
        $endpoint = 'activities';
        $this
            ->assert('endpoint')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setEndpoint($endpoint))
            ->then
                ->string($this->invoke($this->testedInstance)->getEndpoint())
                ->isEqualTo($endpoint);
    }

    public function testFile()
    {
        $file = \LegionBoard\tests\Utilities::getMockFile();
        $this
            ->assert('file')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setFile($file))
            ->then
                ->string($this->invoke($this->testedInstance)->getFile())
                ->isEqualTo($file);
    }

    public function testId()
    {
        $identification = '123';
        $this
            ->assert('id')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setID($identification))
            ->then
                ->string($this->invoke($this->testedInstance)->getID())
                ->isEqualTo($identification);
    }

    public function testMethod()
    {
        $method = 'PUT';
        $this
            ->assert('method')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setMethod($method))
            ->then
                ->string($this->invoke($this->testedInstance)->getMethod())
                ->isEqualTo($method);
    }

    public function testPreviousHash()
    {
        $previousHash = '1234567890abcdef';
        $this
            ->assert('previous hash')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setPreviousHash($previousHash))
            ->then
                ->string($this->invoke($this->testedInstance)->getPreviousHash())
                ->isEqualTo($previousHash);
    }

    /**
     * @php < 7.2
     */
    public function testProcessAPINotExisting()
    {
        $endpoint = 'notexisting';
        $this
            ->assert('process api with non-existing endpoint')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setEndpoint($endpoint))
            ->then
                ->string($json = $this->invoke($this->testedInstance)->processAPI())
                    ->contains('error')
                    ->contains('message')
                    ->hasLengthGreaterThan(50)
                ->object(json_decode($json))
                    ->hasSize(1);
    }

    public function testStatus()
    {
        $status = '404';
        $this
            ->assert('status')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setStatus($status))
            ->then
                ->string($this->invoke($this->testedInstance)->getStatus())
                ->isEqualTo($status);
    }

    public function testVersionCode()
    {
        $versionCode = '123';
        $this
            ->assert('version code')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setVersionCode($versionCode))
            ->then
                ->string($this->invoke($this->testedInstance)->getVersionCode())
                ->isEqualTo($versionCode);
    }

    public function testVersionName()
    {
        $versionName = '0.1.2';
        $this
            ->assert('version name')
            ->if($this->newTestedInstance(\LegionBoard\tests\Utilities::getMockRequest()))
            ->and($this->invoke($this->testedInstance)->setVersionName($versionName))
            ->then
                ->string($this->invoke($this->testedInstance)->getVersionName())
                ->isEqualTo($versionName);
    }
}
