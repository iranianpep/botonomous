<?php

namespace Botonomous;

use PHPUnit\Framework\TestCase;

/**
 * Class CommandExtractorTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class CommandExtractorTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * @throws \Exception
     */
    public function testRespondWithoutDefaultCommand()
    {
        $config = new Config();
        $config->set('defaultCommand', '');

        $message = 'dummy dummy dummy dummy';

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => $message,
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $this->assertEquals($this->outputOnNoCommand($message), $slackbot->respond());
    }

    /**
     * @param $message
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function outputOnNoCommand($message)
    {
        $config = new Config();
        $defaultCommand = $config->get('defaultCommand');

        $token = $config->get(self::VERIFICATION_TOKEN);

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest(['text' => $message, 'token' => $token]);

        if (!empty($defaultCommand)) {
            $commandObject = (new CommandContainer())->getAsObject($defaultCommand);
            /** @noinspection PhpUndefinedMethodInspection */
            $commandClass = $commandObject->getClass();

            /**
             * @var AbstractPlugin
             */
            $pluginObject = (new $commandClass($slackbot));

            /* @noinspection PhpUndefinedMethodInspection */
            return $pluginObject->index();
        }

        return (new Dictionary())->get('generic-messages')['noCommandMessage'];
    }

    /**
     * @throws \Exception
     */
    public function testRespondExceptException()
    {
        $config = new Config();
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $botUserId = '<@'.$config->get('botUserId').'>';
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => "{$botUserId} {$commandPrefix}commandWithoutFunctionForTest",
        ];

        $this->expectException('\Exception');
        $this->expectExceptionMessage(
            'Action / function: \'commandWithoutFunctionForTest\' does not exist in \'Botonomous\plugin\ping\Ping\''
        );

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $response = $slackbot->respond();

        // @codeCoverageIgnoreStart
        $this->assertEquals('', $response);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @throws \Exception
     */
    public function testGetCommandByMessage()
    {
        /**
         * Form the request.
         */
        $config = new Config();
        $commandPrefix = $config->get('commandPrefix');
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $result = (new CommandExtractor())->getCommandByMessage("{$commandPrefix}ping message");

        $this->assertEquals('index', $result->getAction());
        $this->assertEquals('Ping', $result->getPlugin());
    }

    /**
     * @throws \Exception
     */
    public function testGetCommandByMessageWithoutDefaultCommand()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
        ];

        $config->set('defaultCommand', '');

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $commandExtractor = new CommandExtractor();
        $commandExtractor->getCommandByMessage('dummy message without command');

        $this->assertEquals(
            (new Dictionary())->get('generic-messages')['noCommandMessage'],
            $commandExtractor->getError()
        );
    }

    /**
     * Test getCommandByMessage.
     */
    public function testGetCommandByMessageEmptyMessage()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => '',
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $commandExtractor = new CommandExtractor();
        $result = $commandExtractor->getCommandByMessage('');

        $this->assertEquals('Message is empty', $commandExtractor->getError());

        $this->assertEmpty($result);
    }

    public function testGetConfig()
    {
        $commandExtractor = new CommandExtractor();
        $config = new Config();
        $commandExtractor->setConfig($config);

        $this->assertEquals($config, $commandExtractor->getConfig());
    }

    public function testCalculateKeywordSimilarity()
    {
        $commandExtractor = new CommandExtractor();

        $commandContainer = new CommandContainer();

        $commands = [
            'ping' => [
                'plugin'      => 'Ping',
                'description' => 'Use as a health check',
                'keywords' => [
                    'play',
                    'sport',
                    'pong'
                ]
            ],
            'pong' => [
                'plugin'      => 'Ping',
                'action'      => 'pong',
                'description' => 'Use as a health check',
                'keywords' => [
                    'play',
                    'sport',
                    'ping'
                ]
            ],
            'dummy' => [
                'plugin'      => 'Ping',
                'action'      => 'pong',
                'description' => 'Use as a health check',
            ],
        ];

        $commandContainer->setAll($commands);

        $commandExtractor->setCommandContainer($commandContainer);

        $similarities = $commandExtractor->calculateKeywordSimilarity("let's play ping pong");

        $expected = [
            'ping' => 62.85714285714285409767398959957063198089599609375,
            'pong' => 51.4285714285714306015506736002862453460693359375
        ];

        $this->assertEquals($expected, $similarities);
    }
}
