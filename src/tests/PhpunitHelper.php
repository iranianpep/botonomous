<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\Slackbot;

class PhpunitHelper
{
    const VERIFICATION_TOKEN = 'verificationToken';

    public function getSlackbot()
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

        return $slackbot;
    }
}
