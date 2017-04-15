<?php

namespace Slackbot;

use Slackbot\client\ApiClientTest;

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
                        'dummyUserName',
                    ],
                    'userId' => [
                        'dummyUserId',
                    ],
                ],
            ],
        ];
    }

    public function getRequest()
    {
        return [
            'user_name' => 'dummyUserName',
            'user_id'   => 'dummyUserId',
        ];
    }

    public function getWhiteList()
    {
        return new WhiteList($this->getRequest());
    }

    public function getBlackList()
    {
        return new BlackList($this->getRequest());
    }

    public function getUserInfoClient()
    {
        return (new ApiClientTest())->getApiClient('{
            "ok": true,
            "user": {
                "id": "U023BECGF",
                "name": "bobby",
                "deleted": false,
                "color": "9f69e7",
                "profile": {
                    "first_name": "Bobby",
                    "last_name": "Tables",
                    "real_name": "Bobby Tables",
                    "email": "bobby@slack.com",
                    "skype": "my-skype-name",
                    "phone": "+1 (123) 456 7890",
                    "image_24": "https:\/\/...",
                    "image_32": "https:\/\/...",
                    "image_48": "https:\/\/...",
                    "image_72": "https:\/\/...",
                    "image_192": "https:\/\/..."
                },
                "is_admin": true,
                "is_owner": true,
                "has_2fa": true
            }
        }');
    }
}
