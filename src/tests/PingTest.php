<?php

namespace Slackbot\Tests;

use Slackbot\plugin\ping\Ping;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class PingTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        require_once 'PhpunitHelper.php';
        parent::__construct();
    }

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
