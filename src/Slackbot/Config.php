<?php

namespace Slackbot;

/**
 * Class Config.
 */
class Config extends AbstractConfig
{
    protected static $configs = [
        'baseUrl'              => 'http://localhost:8888',
        'timezone'             => 'Australia/Melbourne',
        'accessToken'          => 'xoxb-164178835570-n652D5Trt0Ik9D7w3BYTF1hA',
        'channel'              => '#general',
        'botUserId'            => 'U4U58QKGS',
        'botUsername'          => 'test_app',
        'chatLogging'          => true,
        'tmpFolderName'        => 'tmp',
        'chatLoggingFileName'  => 'chat_log',
        'iconURL'              => 'YOUR_BOT_ICON_URL_48_BY_48',
        'asUser'               => true,
        // possible values are: slashCommand, event
        'listenerType'         => 'slashCommand',
        // possible values are: slack, json, slashCommand
        'response'      => 'slashCommand',
        // this is used if there is no command has been specified in the message
        'defaultCommand'     => 'help',
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
        'clientId'     => '168274793846.167633250276',
        'clientSecret' => '1234567890',
        'scopes'       => ['bot'],
        /*
         * For interactive messages and events,
         * use this token to verify that requests are actually coming from Slack
         */
        'verificationToken'    => 'UByHpCps5wcFBmELqiP5UmLR',
        'apiAppId'             => 'A4XJM7C84',
        'enabledAccessControl' => false,
    ];
}
