<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\Slackbot;
use Slackbot\utility\RequestUtility;
use Slackbot\WebhookListener;

/** @noinspection PhpUndefinedClassInspection */
class WebhookListenerTest extends \PHPUnit_Framework_TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

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
            'token'     => $config->get(self::VERIFICATION_TOKEN),
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

        $confirmMessage = $slackbot->getConfig()->get('confirmReceivedMessage', [
            'user' => '<@dummyId> ',
        ]);

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
            'token'   => $config->get(self::VERIFICATION_TOKEN),
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
        $this->sendByResponseType('json');
    }

    /**
     * @throws \Exception
     */
    public function testSendResponseSlashCommand()
    {
        $this->sendByResponseType('slashCommand');
    }

    /**
     * @param $response
     */
    private function sendByResponseType($response)
    {
        $config = new Config();
        $config->set('listenerType', 'webhook');

        /**
         * Form the request.
         */
        $request = [
            'token'     => $config->get(self::VERIFICATION_TOKEN),
            'debug'     => true,
            'user_id'   => 'dummyId',
            'user_name' => 'dummyUsername',
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $config->set('response', $response);
        $slackbot->setConfig($config);

        $this->expectOutputString('{"text":"test response","channel":"#general"}');

        $slackbot->send('general', 'test response');
    }

    /**
     * Test verifyOrigin.
     *
     * @throws \Exception
     */
    public function testVerifyOrigin()
    {
        $request = [];
        $webhookListener = new WebhookListener();
        $webhookListener->setRequest($request);

        $result = $webhookListener->verifyOrigin();

        $this->assertEquals([
            'success' => false,
            'message' => 'Token is missing',
        ], $result);

        $request = ['token' => '12345'];
        $webhookListener->setRequest($request);

        $config = new Config();

        $config->set(self::VERIFICATION_TOKEN, '54321');

        $result = $webhookListener->verifyOrigin();

        $this->assertEquals([
            'success' => false,
            'message' => 'Token is not valid',
        ], $result);

        $config->set(self::VERIFICATION_TOKEN, '12345');

        $result = $webhookListener->verifyOrigin();

        $this->assertEquals([
            'success' => true,
            'message' => 'Awesome!',
        ], $result);

        $config->set(self::VERIFICATION_TOKEN, '');

        $webhookListener->setConfig($config);

        $this->setExpectedException('Exception', 'Token must be set in the config');

        $webhookListener->verifyOrigin();
    }

    /**
     * Test isThisBot.
     *
     * @throws \Exception
     */
    public function testIsThisBotMissingBotId()
    {
        $webhookListener = new WebhookListener();

        $this->setExpectedException('Exception', 'Bot user id must be provided');

        $webhookListener->isThisBot();
    }

    /**
     * Test isThisBot.
     *
     * @throws \Exception
     */
    public function testIsThisBotMissingBotName()
    {
        $request = ['user_id' => '12345'];
        $webhookListener = new WebhookListener();
        $webhookListener->setRequest($request);

        $this->setExpectedException('Exception', 'Bot user name must be provided');

        $webhookListener->isThisBot();
    }

    /**
     * Test extractRequest.
     */
    public function testExtractRequest()
    {
        $requestUtility = new RequestUtility();

        $post = ['test' => 'test'];
        $requestUtility->setPost($post);

        $listener = new WebhookListener();
        $listener->setRequestUtility($requestUtility);

        $this->assertEquals($post, $listener->extractRequest());
    }
}
