<?php

namespace Botonomous;

use Botonomous\listener\EventListener;
use Botonomous\listener\SlashCommandListener;
use Botonomous\plugin\AbstractPlugin;
use Botonomous\utility\FormattingUtility;
use Botonomous\utility\RequestUtility;
use PHPUnit\Framework\TestCase;

/**
 * Class SlackbotTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class SlackbotTest extends TestCase
{
    const VERIFICATION_TOKEN = 'verificationToken';

    /**
     * Test run.
     */
    public function testRunEmptyState()
    {
        $requestUtility = new RequestUtility();
        $requestUtility->setGet(
            [
                'action'       => 'oauth',
                'trigger_word' => 'mybot:',
                'text'         => 'mybot: test',
            ]
        );

        $slackbot = new Slackbot();

        $oauth = new OAuth();
        $oauth->setRequestUtility($requestUtility);

        $slackbot->setOauth($oauth);
        $slackbot->setRequestUtility($requestUtility);

        $this->expectException('\Exception');
        $this->expectExceptionMessage('State is not provided');

        $slackbot->run();
    }

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
        $botUserId = '<@'.$config->get('botUserId').'>';

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($this->getDummyRequest());

        $response = $slackbot->respond();

        $this->assertEquals('pong', $response);

        $inputsOutputs = [
            [
                'i' => [
                    'message' => "$botUserId {$commandPrefix}ping",
                ],
                'o' => 'pong',
            ],
            [
                'i' => [
                    'message' => "$botUserId {$commandPrefix}pong",
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

        $this->assertEquals((new Dictionary())->get('generic-messages')['noCommandMessage'], $slackbot->getLastError());
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

        $result = $slackbot->getCommandByMessage();

        $this->assertEquals('Message is empty', $slackbot->getLastError());

        $this->assertFalse($result);
    }

    /**
     * Test getOauth.
     */
    public function testGetOauth()
    {
        $oauth = new OAuth();
        $slackbot = new Slackbot();

        $this->assertEquals(new OAuth(), $slackbot->getOauth());

        $oauth->setClientId('12345');
        $slackbot->setOauth($oauth);

        $this->assertEquals('12345', $slackbot->getOauth()->getClientId());
    }

    /**
     * Test getCurrentCommand.
     */
    public function testGetCurrentCommand()
    {
        $slackbot = new Slackbot();
        $slackbot->setCurrentCommand('help');

        $this->assertEquals('help', $slackbot->getCurrentCommand());
    }

    /**
     * Test setConfig in constructor.
     */
    public function testConstructorSetConfig()
    {
        $config = new Config();
        $config->set('testKey', 'testValue');

        $slackbot = new Slackbot($config);
        $this->assertEquals('testValue', $slackbot->getConfig()->get('testKey'));
    }

    /**
     * Test testGetMessageEventListener.
     */
    public function testGetMessageEventListener()
    {
        $slackbot = new Slackbot();
        $listener = new EventListener();

        $requestUtility = new RequestUtility();
        $requestUtility->setContent('{"token":"Jdsfsdfvdfv","event":{"type":"message","text":"<@U3Qdfvdfv> test"}}');
        $listener->setRequestUtility($requestUtility);

        $slackbot->setListener($listener);

        $this->assertEquals('<@U3Qdfvdfv> test', $slackbot->getMessage());
    }

    /**
     * Test getMessageSlashCommandListener.
     */
    public function testGetMessageSlashCommandListener()
    {
        $slackbot = new Slackbot();
        $listener = new SlashCommandListener();

        $requestUtility = new RequestUtility();
        $requestUtility->setPost(
            ['text' => '<@U3Qdfvdfv> test']
        );
        $listener->setRequestUtility($requestUtility);

        $slackbot->setListener($listener);

        $this->assertEquals('<@U3Qdfvdfv> test', $slackbot->getMessage());
    }

    /**
     * Test processRequest.
     */
    public function testUrlVerificationAction()
    {
        $slackbot = new Slackbot();

        // mock request
        $requestUtility = new RequestUtility();

        $challenge = '3eZbrw1aBm2rZgRNFdxV2595E9CY3gmdALWMmHkvFXO7tYXAYM8P';
        $request = [
            'token'     => 'Jhj5dZrVaK7ZwHHjRyZWjbDl',
            'challenge' => $challenge,
            'type'      => 'url_verification',
        ];

        $requestUtility->setContent(json_encode($request));

        $slackbot->setRequestUtility($requestUtility);

        $this->expectOutputString($challenge);

        $slackbot->run();
    }

    /**
     * Test youTalkingToMe.
     */
    public function testYouTalkingToMe()
    {
        $slackbot = new Slackbot();
        $listener = $slackbot->getListener();

        $listener->setRequest($this->getDummyRequest());

        $this->assertEquals(true, $slackbot->youTalkingToMe());

        $listener->setRequest($this->getDummyRequest('dummyBotId'));

        $this->assertEquals(false, $slackbot->youTalkingToMe());

        $listener->setRequest($this->getDummyRequest('dummyBotId', ''));

        $this->assertEquals(false, $slackbot->youTalkingToMe());
    }

    /**
     * Test messageActions.
     */
    public function testMessageActions()
    {
        $utility = new RequestUtility();
        $utility->setGet(['action' => 'message_actions']);
        $utility->setPost([
            'payload' => '{
  "actions": [
    {
      "name": "recommend",
      "value": "yes",
      "type": "button"
    }
  ],
  "callback_id": "comic_1234_xyz",
  "team": {
    "id": "T47563693",
    "domain": "watermelonsugar"
  },
  "channel": {
    "id": "C065W1189",
    "name": "forgotten-works"
  },
  "user": {
    "id": "U045VRZFT",
    "name": "brautigan"
  },
  "action_ts": "1458170917.164398",
  "message_ts": "1458170866.000004",
  "attachment_id": "1",
  "token": "xAB3yVzGS4BQ3O9FACTa8Ho4",
  "original_message": {
    "text":"New comic book alert!",
    "attachments":[{
        "title":"Blah",
        "fields":[{
                "title":"Blah",
                "value":"1",
                "short":true
            },
            {
                "title":"Issue",
                "value":"3",
                "short":true
            }],
            "author_name":"Blah",
            "author_icon":"https://api.slack.comhttps://a.slack-edge.com/test.png",
            "image_url":"http://i.imgur.com/OJkaVOI.jpg?1"},
            {"title":"Synopsis","text":"Blah Blah"},
            {"fallback":"Blah",
            "title":"Blah",
            "callback_id":"comic_1234_xyz","color":"#3AA3E3","attachment_type":"default",
            "actions":[
            {"name":"recommend","text":"Recommend","type":"button","value":"recommend"},
            {"name":"no","text":"No","type":"button","value":"bad"}]}]},
  "response_url": "https://hooks.slack.com/actions/T47563693/6204672533/x7ZLaiVMoECAW50Gw1ZYAXEM"
}',
        ]);

        $slackbot = new Slackbot();
        $slackbot->setRequestUtility($utility);

        $messageAction = $slackbot->run();

        $actions = [
            [
                'name'  => 'recommend',
                'value' => 'yes',
                'type'  => 'button',
            ],
        ];

        $messageAction->setActions($actions);

        $this->assertEquals($actions, $messageAction->getActions());

        $utility->setPost([]);

        $this->assertEmpty($slackbot->run());
    }

    private function getDummyRequest($botUserId = null, $text = null)
    {
        /**
         * Form the request.
         */
        $config = new Config();

        if ($botUserId === null) {
            $botUserId = '<@'.$config->get('botUserId').'>';
        }

        if ($text === null) {
            $commandPrefix = $config->get('commandPrefix');
            $text = "{$botUserId} {$commandPrefix}ping";
        }

        return [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => $text,
        ];
    }

    /**
     * Test formattingUtility.
     */
    public function testGetFormattingUtility()
    {
        $slackbot = new Slackbot();

        $utility = new FormattingUtility();
        $slackbot->setFormattingUtility($utility);

        $this->assertEquals($utility, $slackbot->getFormattingUtility());
    }

    /**
     * Test formattingUtility.
     */
    public function testGetFormattingUtilityNotSet()
    {
        $slackbot = new Slackbot();

        $utility = new FormattingUtility();

        $this->assertEquals($utility, $slackbot->getFormattingUtility());
    }
}
