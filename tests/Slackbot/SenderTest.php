<?php

namespace Slackbot;

use PHPUnit\Framework\TestCase;

class SenderTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    private function getSlackbot($debug = null, $responseType = null)
    {
        $config = new Config();

        if ($responseType !== null) {
            $config->set('response', $responseType);
        }

        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot.
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
        ];

        if ($debug !== null) {
            $request['debug'] = $debug;
        }

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        return $slackbot;
    }

    public function testSendDebug()
    {
        $sender = new Sender($this->getSlackbot(true));

        $sender->send('#dummyChannel', 'test response', []);

        $response = '{"text":"test response","channel":"#dummyChannel","attachments":"[]"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackJson()
    {
        $sender = new Sender($this->getSlackbot(null, 'json'));

        $sender->send('#dummyChannel', 'test response');

        $response = '{"text":"test response","channel":"#dummyChannel"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackSlack()
    {
        $sender = new Sender($this->getSlackbot(null, 'slack'));

        $sender->send('#dummyChannel', 'test response');

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
}
