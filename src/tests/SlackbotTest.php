<?php

namespace Slackbot\Tests;

use Slackbot\CommandContainer;
use Slackbot\Config;
use Slackbot\plugin\AbstractPlugin;
use Slackbot\Slackbot;

/**
 * Class SlackbotTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class SlackbotTest extends \PHPUnit_Framework_TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * @throws \Exception
     */
    public function testRespond()
    {
        $config = new Config();
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => "{$botUsername} {$commandPrefix}ping",
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $response = $slackbot->respond();

        $this->assertEquals('pong', $response);

        $message = '';

        $inputsOutputs = [
            [
                'i' => [
                    'message' => "$botUsername {$commandPrefix}ping",
                ],
                'o' => 'pong',
            ],
            [
                'i' => [
                    'message' => "$botUsername {$commandPrefix}pong",
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => "{$commandPrefix}ping",
                ],
                'o' => 'pong',
            ],
            [
                'i' => [
                    'message' => "{$commandPrefix}pong",
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => "{$commandPrefix}pong",
                ],
                'o' => 'ping',
            ],
            [
                'i' => [
                    'message' => "{$commandPrefix}unknownCommand",
                ],
                'o' => $config->get('unknownCommandMessage', ['command' => 'unknownCommand']),
            ],
            [
                'i' => [
                    'message' => 'dummy message without any command',
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
            [
                'i' => [
                    'message' => "dummy {$commandPrefix}ping",
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
            [
                'i' => [
                    'message' => 'dummy dummy dummy dummy',
                ],
                'o' => $this->outputOnNoCommand($message),
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $response = $slackbot->respond($inputOutput['i']['message']);

            $output = $inputOutput['o'];

            // @codeCoverageIgnoreStart
            if (is_callable($inputOutput['o'])) {
                $output = call_user_func($inputOutput['o'], $inputOutput['i']['message']);
            }
            // @codeCoverageIgnoreEnd

            $this->assertEquals($output, $response);
        }
    }

    /**
     * @throws \Exception
     */
    public function testSetGetRequest()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $this->assertEquals($request, $slackbot->getRequest());

        $this->assertEquals($config->get(self::VERIFICATION_TOKEN), $slackbot->getRequest('token'));
    }

    /**
     * @throws \Exception
     */
    public function testGetConfig()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $this->assertEquals($config, $slackbot->getConfig());
    }

    /**
     * @throws \Exception
     */
    public function testSetConfig()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        $slackbot->setConfig($config);

        $this->assertEquals($config, $slackbot->getConfig());
    }

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

        return $config->get('noCommandMessage');
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
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => "{$botUsername} {$commandPrefix}commandWithoutFunctionForTest",
        ];

        $this->setExpectedException(
            '\Exception',
            'Action / function: \'commandWithoutFunctionForTest\' does not exist in \'Slackbot\plugin\ping\Ping\''
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

        $result = $slackbot->getCommandByMessage("{$commandPrefix}ping message");

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

        $slackbot->getCommandByMessage('dummy message without command');

        $this->assertEquals($config->get('noCommandMessage'), $slackbot->getLastError());
    }
}
