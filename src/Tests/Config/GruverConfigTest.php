<?php

namespace Mindgruve\Gruver\Tests\Config;

use Mindgruve\Gruver\Config\ConfigLoader;

class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Exception
     */
    public function testConstructorFileDoesNotExists()
    {
        $testYaml = __DIR__.'/../Temp/'.uniqid();
        $sut = new ConfigLoader($testYaml);

    }

    public function testValidFixture()
    {
        $testYaml = __DIR__.'/fixture.yml';
        $sut = new ConfigLoader($testYaml);
    }


}