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
    
    public function testRespond()
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
        $response = $slackbot->respond();

        $this->assertEquals('pong', $response);

        $IOs = [
            [
                'i' => [
                    'message' => "$botUsername /ping"
                ],
                'o' => 'pong'
            ],
            [
                'i' => [
                    'message' => "$botUsername /pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/ping"
                ],
                'o' => 'pong'
            ],
            [
                'i' => [
                    'message' => "/pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/unknownCommand"
                ],
                'o' => 'Sorry. I do not know anything about your command: \'/unknownCommand\'. I List the available commands using /help'
            ],
        ];

        foreach ($IOs as $io) {
            $response = $slackbot->respond($io['i']['message']);
            $this->assertEquals($io['o'], $response);
        }
    }
}
