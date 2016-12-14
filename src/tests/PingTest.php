<?php

use Slackbot\Config;
use Slackbot\plugin\Ping;

class PingTest extends PHPUnit_Framework_TestCase
{
    public function testPong()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);
        $pongResponse = $ping->pong();

        $this->assertEquals('ping', $pongResponse);
    }
    
    public function testGetSlackbot()
    {
        $slackbot = $this->getSlackbot();
        $ping = new Ping($slackbot);
        $getResult = $ping->getSlackbot();
        
        $this->assertEquals($slackbot, $getResult);
    }

    private function getSlackbot()
    {
        $config = new Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /ping'
        ];

        return new \Slackbot\Slackbot($request);
    }
}
