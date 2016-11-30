<?php

class SlackbotTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetRequest()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $this->assertEquals($request, $slackbot->getRequest());

        $this->assertEquals($config->get('outgoingWebhookToken'), $slackbot->getRequest('token'));
    }

    public function testGetConfig()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $this->assertEquals($config, $slackbot->getConfig());
    }
}
