<?php

namespace Slackbot\Tests;

use Slackbot\Command;
use Slackbot\Config;
use Slackbot\Slackbot;

/**
 * Class SlackbotTest
 */
class SlackbotTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetRequest()
    {
        $config = new Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new Slackbot($request);

        $this->assertEquals($request, $slackbot->getRequest());

        $this->assertEquals($config->get('outgoingWebhookToken'), $slackbot->getRequest('token'));
    }

    public function testGetConfig()
    {
        $config = new Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new Slackbot($request);

        $this->assertEquals($config, $slackbot->getConfig());
    }
    
    public function testRespond()
    {
        $config = new Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /ping'
        ];

        $slackbot = new \Slackbot\Slackbot($request);
        $response = $slackbot->respond();

        $this->assertEquals('pong', $response);

        $message = '';

        $inputsOutputs = [
            [
                'i' => [
                    'message' => "$botUsername /ping"
                ],
                'o' => 'pong'
            ],
            [
                'i' => [
                    'message' => "$botUsername /pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/ping"
                ],
                'o' => 'pong'
            ],
            [
                'i' => [
                    'message' => "/pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/pong"
                ],
                'o' => 'ping'
            ],
            [
                'i' => [
                    'message' => "/unknownCommand"
                ],
                'o' => $config->get('unknownCommandMessage', ['command' => 'unknownCommand'])
            ],
            [
                'i' => [
                    'message' => "dummy message without any command"
                ],
                'o' => $this->outputOnNoCommand($message)
            ],
            [
                'i' => [
                    'message' => "sfdsf /ping"
                ],
                'o' => $this->outputOnNoCommand($message)
            ],
            [
                'i' => [
                    'message' => "ddfg dfdfg df gdfg"
                ],
                'o' => $this->outputOnNoCommand($message)
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $response = $slackbot->respond($inputOutput['i']['message']);

            $output = $inputOutput['o'];

            if (is_callable($inputOutput['o'])) {
                $output = call_user_func($inputOutput['o'], $inputOutput['i']['message']);
            }

            $this->assertEquals($output, $response);
        }
    }

    private function outputOnNoCommand($message)
    {
        $config = new Config();
        $defaultCommand = $config->get('defaultCommand');

        $token = $config->get('outgoingWebhookToken');

        $slackbot = new Slackbot(['text' => $message, 'token' => $token]);

        if (!empty($defaultCommand)) {
            $command = (new Command())->get($defaultCommand);
            $commandClass = $command['class'];
            return (new $commandClass($slackbot))->index();
        }

        return $config->get('noCommandMessage');
    }

    public function testRespondExceptException()
    {
        $config = new Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /commandWithoutFunctionForTest'
        ];

        $this->setExpectedException(
            '\Exception',
            'Action / function: \'commandWithoutFunctionForTest\' does not exist in \'Slackbot\plugin\Ping\''
        );

        $slackbot = new Slackbot($request);
        $slackbot->respond();
    }
}
