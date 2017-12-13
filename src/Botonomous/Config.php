<?php

namespace Botonomous;

use Botonomous\listener\SlashCommandListener;

/**
 * Class Config.
 */
class Config extends AbstractConfig
{
    protected static $configs = [
        'timezone'             => 'Australia/Melbourne',
        'oAuthToken'           => 'OAuth_Access_Token',
        'botUserToken'         => 'Bot_User_OAuth_Access_Token',
        'botUserId'            => 'YOUR_BOT_USER_ID',
        'botUsername'          => 'YOUR_BOT_USERNAME',
        'logger'               => [
            'enabled' => true,
            'monolog' => [
                'channel'  => 'logger',
                'handlers' => [
                    'file' => [
                        // should be full path
                        'fileName' => 'bot.log',
                    ],
                ],
            ],
        ],
        'iconURL'              => 'YOUR_BOT_ICON_URL_48_BY_48',
        'asUser'               => true,
        // possible values are: SlashCommandListener::KEY, EventListener::KEY
        'listener'         => SlashCommandListener::KEY,
        // this is used if there is no command has been specified in the message
        'defaultCommand'     => 'qa',
        'commandPrefix'      => '/',
        /*
         * App credentials - This is required for Event listener
         * Can be found at https://api.slack.com/apps
         */
        'clientId'     => 'YOUR_APP_CLIENT_ID',
        'clientSecret' => 'YOUR_APP_SECRET',
        'scopes'       => ['bot'],
        /*
         * use this token to verify that requests are actually coming from Slack
         */
        'verificationToken'    => 'YOUR_APP_VERIFICATION_TOKEN',
        'appId'                => 'YOUR_APP_ID',
        'accessControlEnabled' => false,
    ];
}
