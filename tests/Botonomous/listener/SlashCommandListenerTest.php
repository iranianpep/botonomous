<?php

namespace Botonomous\listener;

use Botonomous\BlackList;
use Botonomous\Config;
use Botonomous\Dictionary;
use Botonomous\Slackbot;
use Botonomous\utility\LoggerUtilityTest;
use Botonomous\utility\RequestUtility;
use PHPUnit\Framework\TestCase;

/** @noinspection PhpUndefinedClassInspection */
class SlashCommandListenerTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * Test listen.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testListen()
    {
        $post = ['user_id' => 'B123'];

        $this->assertEquals($post, $this->getListener($post)->listen());
    }

    /**
     * Test listenBot.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testListenBot()
    {
        $this->assertEmpty($this->getListener(['user_id' => 'USLACKBOT'])->listen());
    }

    /**
     * @param $post
     *
     * @return SlashCommandListener
     */
    private function getListener($post)
    {
        $listener = new SlashCommandListener();
        $config = new Config();
        $listener->setConfig($config);

        $requestUtility = new RequestUtility();
        $requestUtility->setPost($post);
        $listener->setRequestUtility($requestUtility);

        return $listener;
    }

    private function getRequest(Config $config, $commandPrefix, $token = null)
    {
        return [
                'token'        => $token === null ? $config->get(self::VERIFICATION_TOKEN) : $token,
                'text'         => "mybot: {$commandPrefix}ping",
                'user_id'      => 'dummyId',
                'user_name'    => $config->get('botUsername'),
                'trigger_word' => 'mybot:',
                'channel_id'   => 'C2147483705',
                'debug'        => true,
        ];
    }

    /**
     * @throws \Exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRun()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $request = $this->getRequest($config, $commandPrefix);
        (new LoggerUtilityTest())->setLogFile();

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);
        $slackbot->setListener($listener);

        $slackbot->setConfig($config);

        $confirmMessage = $slackbot->getDictionary()->getValueByKey('generic-messages', 'confirmReceivedMessage', [
            'user' => '<@dummyId> ',
        ]);

        $response = '';
        if (!empty($confirmMessage)) {
            $response .= '{"text":"'.$confirmMessage.'","channel":"C2147483705"}';
        }

        $response .= '{"text":"pong","channel":"C2147483705"}';

        $this->expectOutputString($response);

        $slackbot->run();
    }

    /**
     * @throws \Exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRunWithoutToken()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $request = $this->getRequest($config, $commandPrefix, '');
        (new LoggerUtilityTest())->setLogFile();

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);
        $slackbot->setListener($listener);

        $slackbot->setConfig($config);

        $this->expectException('\Exception');
        $this->expectExceptionMessage(SlashCommandListener::MISSING_TOKEN_MESSAGE);

        $slackbot->run();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRunWithAccessControl()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $request = $this->getRequest($config, $commandPrefix);
        (new LoggerUtilityTest())->setLogFile();

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        // set request
        $listener->setRequest($request);
        $slackbot->setListener($listener);

        $config->set('accessControlEnabled', true);

        $sorryResponse = (new Dictionary())->get('generic-messages')['whitelistedMessage'];

        $response = '{"text":"'.$sorryResponse.'","channel":"C2147483705"}';

        $this->expectOutputString($response);

        $slackbot->run();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testRunWithBlackListedAccessControl()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $request = $this->getRequest($config, $commandPrefix);
        (new LoggerUtilityTest())->setLogFile();

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        // set request
        $listener->setRequest($request);
        $slackbot->setListener($listener);

        $config->set('accessControlEnabled', true);

        $blackList = new BlackList($request);

        $dictionary = new Dictionary();
        $dictionary->setData([
            'access-control' => [
                'blacklist' => [
                    'userId' => [
                        'dummyId',
                    ],
                ],
            ],
        ]);

        $blackList->setDictionary($dictionary);

        $slackbot->setBlackList($blackList);

        $sorryResponse = (new Dictionary())->get('generic-messages')['blacklistedMessage'];

        $response = '{"text":"'.$sorryResponse.'","channel":"C2147483705"}';

        $this->expectOutputString($response);

        $slackbot->run();
    }

    /**
     * @throws \Exception
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSendByBot()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');

        /**
         * Form the request.
         */
        $request = [
            'token'   => $config->get(self::VERIFICATION_TOKEN),
            'user_id' => 'USLACKBOT',
            'debug'   => true,
        ];

        $this->expectException('\Exception');
        $this->expectExceptionMessage('Request comes from the bot');

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        $slackbot->run();
    }

    /**
     * @throws \Exception
     */
    public function testSendResponseSlashCommand()
    {
        $config = new Config();
        $config->set('listener', 'slashCommand');
        (new LoggerUtilityTest())->setLogFile();

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

        $slackbot->setConfig($config);

        $this->expectOutputString('{"text":"test response 1","channel":"#dummyChannel"}');

        $slackbot->getSender()->send('test response 1', '#dummyChannel');
    }

    /**
     * Test verifyOrigin.
     *
     * @throws \Exception
     */
    public function testVerifyOrigin()
    {
        $request = [];
        $slashCommandListener = new SlashCommandListener();
        $slashCommandListener->setRequest($request);

        $result = $slashCommandListener->verifyOrigin();

        $this->assertEquals([
            'success' => false,
            'message' => SlashCommandListener::MISSING_TOKEN_MESSAGE,
        ], $result);

        $request = ['token' => '12345'];
        $slashCommandListener->setRequest($request);

        $config = new Config();

        $config->set(self::VERIFICATION_TOKEN, '54321');

        $result = $slashCommandListener->verifyOrigin();

        $this->assertEquals([
            'success' => false,
            'message' => 'Token is not valid',
        ], $result);

        $config->set(self::VERIFICATION_TOKEN, '12345');

        $result = $slashCommandListener->verifyOrigin();

        $this->assertEquals([
            'success' => true,
            'message' => 'Awesome!',
        ], $result);

        $config->set(self::VERIFICATION_TOKEN, '');

        $slashCommandListener->setConfig($config);

        $this->expectException('\Exception');
        $this->expectExceptionMessage(SlashCommandListener::MISSING_TOKEN_CONFIG_MESSAGE);

        $slashCommandListener->verifyOrigin();
    }

    /**
     * Test extractRequest.
     */
    public function testExtractRequest()
    {
        $requestUtility = new RequestUtility();

        $post = ['test' => 'test'];
        $requestUtility->setPost($post);

        $listener = new SlashCommandListener();
        $listener->setRequestUtility($requestUtility);

        $this->assertEquals($post, $listener->extractRequest());
    }
}
