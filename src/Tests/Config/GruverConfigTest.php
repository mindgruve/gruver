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
        $testYaml = __DIR__ . '/../Temp/' . uniqid();
        $sut = new GruverConfig($testYaml);

    }

    /**
     * @group config
     */
    public function testValidFixture()
    {
        $gruverFixture = __DIR__ . '/../Data/gruver-fixture.yml';
        $composeFixture = __DIR__ . '/../Data/docker-compose-fixture.yml';
        $sut = new GruverConfig($gruverFixture, $composeFixture);
    }

    /**
     * @group config
     */
    public function testConfigGet()
    {
        $gruverFixture = __DIR__ . '/../Data/gruver-fixture.yml';
        $composeFixture = __DIR__ . '/../Data/docker-compose-fixture.yml';
        $sut = new GruverConfig($gruverFixture, $composeFixture);

        $this->assertEquals($_SERVER['PWD'], $sut->get('[application][directory]'));
        $this->assertEquals('mindgruve.com', $sut->getApplicationName());
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[application][email_notifications]'));
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[application][email_notifications]'));
    }

}