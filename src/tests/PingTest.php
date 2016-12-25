<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\plugin\ping\Ping;

/**
 * Class PingTest.
 */
class PingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test pong.
     */
    public function testPong()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);
        $pongResponse = $ping->pong();

        $this->assertEquals('ping', $pongResponse);
    }

    /**
     * Test getSlackbot.
     */
    public function testGetSlackbot()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);
        $getResult = $ping->getSlackbot();

        $this->assertEquals($slackbot, $getResult);
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

        return new \Slackbot\Slackbot($request);
    }
}
