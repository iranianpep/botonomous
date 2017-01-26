<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\plugin\ping\Ping;
use Slackbot\Slackbot;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class PingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test pong.
     */
    public function testPong()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);

        $this->assertEquals('ping', $ping->pong());
    }

    /**
     * Test getSlackbot.
     */
    public function testGetSlackbot()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);

        $this->assertEquals($slackbot, $ping->getSlackbot());
    }

    /**
     * @throws \Exception
     *
     * @return \Slackbot\Slackbot
     */
    private function getSlackbot()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text'  => $botUsername.' /ping',
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        return $slackbot;
    }
}
