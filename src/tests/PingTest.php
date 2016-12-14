<?php

use Slackbot\plugin\Ping;

class PingTest extends PHPUnit_Framework_TestCase
{
    public function testPong()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /ping'
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $ping = new Ping($slackbot);
        $pongResponse = $ping->pong();

        $this->assertEquals('ping', $pongResponse);
    }
    
    public function testGetSlackbot()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /ping'
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $ping = new Ping($slackbot);
        
        $getResult = $ping->getSlackbot();
        
        $this->assertEquals($slackbot, $getResult);
    }
}
