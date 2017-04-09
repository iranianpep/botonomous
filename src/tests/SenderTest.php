<?php


namespace Slackbot\Tests;

use PHPUnit\Framework\TestCase;
use Slackbot\Config;
use Slackbot\Sender;
use Slackbot\Slackbot;

class SenderTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    public function testSendDebug()
    {
        $config = new Config();
        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
        ];

        $request['debug'] = true;

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        $sender = new Sender($slackbot);

        $sender->send('#general', 'test response');

        $response = '{"text":"test response","channel":"#general"}';

        $this->expectOutputString($response);
    }

    public function testSendSlackJson()
    {
        $config = new Config();
        $config->set('response', 'json');
        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot
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

        $sender = new Sender($slackbot);

        $sender->send('#general', 'test response');

        $response = '{"text":"test response","channel":"#general"}';

        $this->expectOutputString($response);
    }

    public function testGetConfig()
    {
        $config = new Config();
        $originalChannel = $config->get('channel');
        $config->set('channel', $originalChannel . 'Changed');

        $sender = new Sender((new Slackbot()));
        $sender->setConfig($config);

        $this->assertEquals($config, $sender->getConfig());

        // reset config value
        $config->set('channel', $originalChannel);
    }
}
