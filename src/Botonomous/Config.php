<?php

namespace Botonomous;

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
        'chatLogging'          => true,
        'chatLoggingFile'      => 'chat_log',
        'iconURL'              => 'YOUR_BOT_ICON_URL_48_BY_48',
        'asUser'               => true,
        // possible values are: slashCommand, event
        'listener'         => 'slashCommand',
        // this is used if there is no command has been specified in the message
        'defaultCommand'     => 'qa',
        'commandPrefix'      => '/',
        /*
         * Generic messages
         */
        'noCommandMessage' => "Sorry. I couldn't find any command in your message.
        List the available commands using /help",
        'unknownCommandMessage' => "Sorry. I do not know anything about your command: '/{command}'.
        List the available commands using /help",
        // leave it empty to disable it
        'confirmReceivedMessage' => ":point_right: {user}I've received your message and am thinking about that ...",
        'blacklistedMessage'     => 'Sorry, we cannot process your message as we detected it in the blacklist',
        'whitelistedMessage'     => 'Sorry, we cannot process your message as we could not find it in whitelist',
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
