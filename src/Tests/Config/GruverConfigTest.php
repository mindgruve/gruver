<?php

namespace Mindgruve\Gruver\Tests\Config;

use Mindgruve\Gruver\Config\GruverConfig;

class GruverConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group config
     */
    public function testValidFixture()
    {
        $gruverFixture = __DIR__.'/../Data/gruver-fixture.yml';
        $composeFixture = __DIR__.'/../Data/docker-compose-fixture.yml';
        $sut = new GruverConfig($gruverFixture, $composeFixture);
    }

    /**
     * @group config
     */
    public function testConfigGet()
    {
        $gruverFixture = __DIR__.'/../Data/gruver-fixture.yml';
        $composeFixture = __DIR__.'/../Data/docker-compose-fixture.yml';
        $sut = new GruverConfig($gruverFixture, $composeFixture);

        $this->assertEquals($_SERVER['PWD'], $sut->get('[application][directory]'));
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[config][email_notifications]'));
        $this->assertEquals(array('ksimpson@mindgruve.com'), $sut->get('[config][email_notifications]'));
    }
}
