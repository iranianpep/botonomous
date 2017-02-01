<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\Slackbot;

/** @noinspection PhpUndefinedClassInspection */
class WebhookListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testRun()
    {
        $config = new Config();
        $config->set('listenerType', 'webhook');
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $request = [
            'token'     => $config->get('outgoingWebhookToken'),
            'text'      => "{$commandPrefix}ping",
            'user_id'   => 'dummyId',
            'user_name' => $config->get('botUsername'),
        ];

        $config->set('response', 'json');
        $config->set('chatLogging', false);

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);
        $slackbot->setListener($listener);

        $slackbot->setConfig($config);

        $confirmMessage = $slackbot->getConfig()->get('confirmReceivedMessage');

        $response = '';
        if (!empty($confirmMessage)) {
            $response .= '{"text":"'.$confirmMessage.'","channel":"#general"}';
        }

        $response .= '{"text":"pong","channel":"#general"}';

        $this->expectOutputString($response);

        $slackbot->run();
    }

    /**
     * @throws \Exception
     */
    public function testSendByBot()
    {
        $config = new Config();
        $config->set('listenerType', 'webhook');

        /**
         * Form the request.
         */
        $request = [
            'token'   => $config->get('outgoingWebhookToken'),
            'user_id' => 'USLACKBOT',
        ];

        $config->set('response', 'json');

        try {
            $slackbot = new Slackbot();

            // get listener
            $listener = $slackbot->getListener();

            // set request
            $listener->setRequest($request);

            $slackbot->setConfig($config);
        } catch (\Exception $e) {
            $this->assertEquals('Request is not coming from Slack', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function testSend()
    {
        $config = new Config();
        $config->set('listenerType', 'webhook');

        /**
         * Form the request.
         */
        $request = [
            'token'     => $config->get('outgoingWebhookToken'),
            'debug'     => true,
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
        ];

        $config->set('response', 'json');

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        $this->expectOutputString('{"text":"test response","channel":"#general"}');

        $slackbot->send('general', 'test response');
    }
}
