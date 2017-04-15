<?php

namespace Slackbot\plugin\ping;

use PHPUnit\Framework\TestCase;
use Slackbot\PhpunitHelper;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class PingTest extends TestCase
{
    public function __construct()
    {
        require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'PhpunitHelper.php';
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
