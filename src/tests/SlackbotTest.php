<?php

class SlackbotTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetRequest()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $this->assertEquals($request, $slackbot->getRequest());

        $this->assertEquals($config->get('outgoingWebhookToken'), $slackbot->getRequest('token'));
    }

    public function testGetConfig()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $request = [
            'token' => $config->get('outgoingWebhookToken')
        ];

        $slackbot = new \Slackbot\Slackbot($request);

        $this->assertEquals($config, $slackbot->getConfig());
    }
    
    public function testRespond()
    {
        $config = new \Slackbot\Config();

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

        $IOs = [
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
                'o' => function($message) {
                    // If there is no command, get the default one, if default one is empty get noCommandMessage
                    $config = new \Slackbot\Config();
                    $defaultCommand = $config->get('defaultCommand');
                    $token = $config->get('outgoingWebhookToken');

                    $slackbot = new \Slackbot\Slackbot(['text' => $message, 'token' => $token]);

                    if (!empty($defaultCommand)) {
                        $command = (new \Slackbot\Command())->get($defaultCommand);
                        $commandClass = $command['class'];
                        return (new $commandClass($slackbot))->index();
                    } else {
                        return $config->get('noCommandMessage');
                    }
                }
            ],
            [
                'i' => [
                    'message' => "sfdsf /ping"
                ],
                'o' => function($message) {
                    $config = new \Slackbot\Config();
                    $defaultCommand = $config->get('defaultCommand');

                    $token = $config->get('outgoingWebhookToken');

                    $slackbot = new \Slackbot\Slackbot(['text' => $message, 'token' => $token]);

                    if (!empty($defaultCommand)) {
                        $command = (new \Slackbot\Command())->get($defaultCommand);
                        $commandClass = $command['class'];
                        return (new $commandClass($slackbot))->index();
                    } else {
                        return $config->get('noCommandMessage');
                    }
                }
            ],
            [
                'i' => [
                    'message' => "ddfg dfdfg df gdfg"
                ],
                'o' => function($message) {
                    $config = new \Slackbot\Config();
                    $defaultCommand = $config->get('defaultCommand');

                    $token = $config->get('outgoingWebhookToken');

                    $slackbot = new \Slackbot\Slackbot(['text' => $message, 'token' => $token]);

                    if (!empty($defaultCommand)) {
                        $command = (new \Slackbot\Command())->get($defaultCommand);
                        $commandClass = $command['class'];
                        return (new $commandClass($slackbot))->index();
                    } else {
                        return $config->get('noCommandMessage');
                    }
                }
            ],
        ];

        foreach ($IOs as $io) {
            $response = $slackbot->respond($io['i']['message']);

            if (is_callable($io['o'])) {
                $output = call_user_func($io['o'], $io['i']['message']);
            } else {
                $output = $io['o'];
            }

            $this->assertEquals($output, $response);
        }
    }

    public function testRespondExceptException()
    {
        $config = new \Slackbot\Config();

        /**
         * Form the request
         */
        $botUsername = '@' . $config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text' => $botUsername . ' /commandWithoutFunctionForTest'
        ];

        $this->setExpectedException('Exception', 'Action / function: \'commandWithoutFunctionForTest\' does not exist in \'Slackbot\plugin\Ping\'');

        $slackbot = new \Slackbot\Slackbot($request);
        $slackbot->respond();
    }
}
