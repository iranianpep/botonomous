<?php

namespace Botonomous;

use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    private function getSlackbot($responseType = 'json')
    {
        $config = new Config();
        $config->set('chatLogging', false);
        $config->set('response', $responseType);

        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot.
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
        ];

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        return $slackbot;
    }

    public function testSendDebug()
    {
        $sender = new Sender($this->getSlackbot());

        $sender->send('#dummyChannel', 'test response 2', []);

        $response = '{"text":"test response 2","channel":"#dummyChannel","attachments":"[]"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackJson()
    {
        $sender = new Sender($this->getSlackbot());

        $sender->send('#dummyChannel', 'test response 3');

        $response = '{"text":"test response 3","channel":"#dummyChannel"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackSlack()
    {
        $sender = new Sender($this->getSlackbot('slack'));

        $sender->send('#dummyChannel', 'test response 4');

        $response = '';

        $this->expectOutputString($response);
    }

    public function testGetConfig()
    {
        $config = new Config();
        $originalChannel = $config->get('channel');
        $config->set('channel', $originalChannel.'Changed');

        $sender = new Sender((new Slackbot()));
        $sender->setConfig($config);

        $this->assertEquals($config, $sender->getConfig());

        // reset config value
        $config->set('channel', $originalChannel);
    }

    public function testGetClient()
    {
        $client = new Client();

        $sender = new Sender((new Slackbot()));
        $this->assertEquals($client, $sender->getClient());
    }
}
