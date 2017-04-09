<?php


namespace Slackbot\Tests;

use PHPUnit\Framework\TestCase;
use Slackbot\Config;
use Slackbot\Sender;
use Slackbot\Slackbot;

class SenderTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    public function testSend()
    {
        $config = new Config();

        /**
         * Overwrite the config
         */

        $slackbot = new Slackbot($config);

        /**
         * Overwrite the slackbot
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'debug'     => true,
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
}
