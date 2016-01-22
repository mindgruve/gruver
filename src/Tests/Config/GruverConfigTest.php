<?php

namespace Mindgruve\Gruver\Tests\Config;

use Mindgruve\Gruver\Config\ConfigLoader;
use Mindgruve\Gruver\Config\GruverConfig;

class ConfigLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @group config
     * @expectedException \Exception
     */
    public function testConstructorFileDoesNotExists()
    {
        $testYaml = __DIR__.'/../Temp/'.uniqid();
        $sut = new GruverConfig($testYaml);

    }

    /**
     * @group config
     */
    public function testValidFixture()
    {
        $testYaml = __DIR__.'/../../Resources/config/gruver_fixture.yml';
        $sut = new GruverConfig($testYaml);
    }

    /**
     * @group config
     */
    public function testConfigGet()
    {
        $testYaml = __DIR__.'/../../Resources/config/gruver_fixture.yml';
        $sut = new GruverConfig($testYaml);

        $this->assertEquals($_SERVER['PWD'], $sut->get('[application][directory]'));
        $this->assertEquals('mindgruve.com', $sut->getApplicationName());
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[application][email_notifications]'));
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[application][email_notifications]'));
    }

}