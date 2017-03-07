<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\Slackbot;

class PhpunitHelper
{
    const VERIFICATION_TOKEN = 'verificationToken';

    public function getSlackbot($command = 'ping', $text = '')
    {
        $config = new Config();
        $commandPrefix = $config->get('commandPrefix');

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get(self::VERIFICATION_TOKEN),
            'text'  => "{$botUsername} {$commandPrefix}{$command}{$text}",
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        return $slackbot;
    }

    public function getDictionaryData($listKey)
    {
        return [
            'access-control' => [
                $listKey => [
                    'username' => [
                        'dummyUserName'
                    ],
                    'userId' => [
                        'dummyUserId'
                    ]
                ],
            ]
        ];
    }

    public function getRequest()
    {
        return [
            'user_name' => 'dummyUserName',
            'user_id'   => 'dummyUserId',
        ];
    }
}
