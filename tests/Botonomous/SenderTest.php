<?php

namespace Botonomous;

use Botonomous\client\ApiClient;
use Botonomous\client\ApiClientTest;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Client;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Handler\MockHandler;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\HandlerStack;
use /* @noinspection PhpUndefinedClassInspection */
    GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    private function getSlackbot($debug = true)
    {
        $config = new Config();
        $config->set('log', false);

        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot.
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
            'debug'     => $debug,
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

        $sender->send('test response 2', '#dummyChannel', []);

        $response = '{"text":"test response 2","channel":"#dummyChannel","attachments":"[]"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackJson()
    {
        $sender = new Sender($this->getSlackbot());

        $sender->send('test response 3', '#dummyChannel');

        $response = '{"text":"test response 3","channel":"#dummyChannel"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackWithDebug()
    {
        $sender = new Sender($this->getSlackbot());

        $sender->send('test response 4', '#dummyChannel');

        $response = '{"text":"test response 4","channel":"#dummyChannel"}';

        $this->expectOutputString($response);
    }

    public function testSendSlack()
    {
        $config = new Config();
        $config->set('listener', 'event');

        $sender = new Sender($this->getSlackbot(false));

        $apiClient = (new ApiClientTest())->getApiClient(
            '{ "ok": true, "ts": "1405895017.000506", "channel": "C024BE91L", "message": {} }'
        );

        $sender->setApiClient($apiClient);

        $result = $sender->send('test response 5', '#dummyChannel');

        $this->assertTrue($result);

        // reset the config
        $config->set('listener', 'slashCommand');
    }

    public function testSendSlashCommand()
    {
        $sender = new Sender($this->getSlackbot(false));

        $mock = new MockHandler([
            new Response(200, [], ''),
        ]);

        /** @noinspection PhpUndefinedClassInspection */
        $handler = new HandlerStack($mock);
        /** @noinspection PhpUndefinedClassInspection */
        $client = new Client(['handler' => $handler]);

        // $client
        $sender->setClient($client);

        $result = $sender->send('test response 6', '#dummyChannel');

        $this->assertTrue($result);
    }

    public function testGetConfig()
    {
        $config = new Config();
        $originalTimezone = $config->get('timezone');
        $config->set('timezone', 'America/Los_Angeles');

        $sender = new Sender((new Slackbot()));
        $sender->setConfig($config);

        $this->assertEquals($config, $sender->getConfig());

        // reset config value
        $config->set('timezone', $originalTimezone);
    }

    public function testGetClient()
    {
        $client = new Client();

        $sender = new Sender(new Slackbot());
        $this->assertEquals($client, $sender->getClient());
    }

    public function testGetApiClient()
    {
        $apiClient = new ApiClient();

        $sender = new Sender(new Slackbot());
        $sender->setApiClient($apiClient);

        $this->assertEquals($apiClient, $sender->getApiClient());
    }

    public function testGetApiClientNotSet()
    {
        $apiClient = new ApiClient();

        $sender = new Sender(new Slackbot());
        $this->assertEquals($apiClient, $sender->getApiClient());
    }
}
