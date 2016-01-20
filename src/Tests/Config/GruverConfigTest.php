<?php

namespace Mindgruve\Gruver\Tests\Config;

use Mindgruve\Gruver\Config\ConfigLoader;
use Mindgruve\Gruver\Config\GruverConfig;

class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Exception
     */
    public function testConstructorFileDoesNotExists()
    {
        $testYaml = __DIR__.'/../Temp/'.uniqid();
        $sut = new GruverConfig($testYaml);

    }

    public function testValidFixture()
    {
        $testYaml = __DIR__.'/fixture.yml';
        $sut = new GruverConfig($testYaml);
    }


}