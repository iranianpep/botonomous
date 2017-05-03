<?php

namespace Botonomous\plugin\ping;

use Botonomous\PhpunitHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class PingTest extends TestCase
{
    /**
     * Test pong.
     */
    public function testPong()
    {
        $slackbot = (new PhpunitHelper())->getSlackbot();
        $ping = new Ping($slackbot);

        $this->assertEquals('ping', $ping->pong());
    }

    /**
     * Test getSlackbot.
     */
    public function testGetSlackbot()
    {
        $slackbot = (new PhpunitHelper())->getSlackbot();
        $ping = new Ping($slackbot);

        $this->assertEquals($slackbot, $ping->getSlackbot());
    }
}
