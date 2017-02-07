# Slackbot Framework
[![Slackbot Framework](http://ajaxlivesearch.com/img/robo-256.png)](http://ajaxlivesearch.com/img/robo-256.png)

[![Latest Stable Version](https://poser.pugx.org/slackbot/slackbot/v/stable)](https://packagist.org/packages/slackbot/slackbot)
[![Build Status](https://travis-ci.org/iranianpep/slackbot.svg?branch=master)](https://travis-ci.org/iranianpep/slackbot)
[![Code Climate](https://codeclimate.com/github/iranianpep/slackbot/badges/gpa.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![Test Coverage](https://codeclimate.com/github/iranianpep/slackbot/badges/coverage.svg)](https://codeclimate.com/github/iranianpep/slackbot/coverage)
[![Issue Count](https://codeclimate.com/github/iranianpep/slackbot/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![License](https://poser.pugx.org/slackbot/slackbot/license)](https://packagist.org/packages/slackbot/slackbot)
[![Code consistency](https://squizlabs.github.io/PHP_CodeSniffer/analysis/iranianpep/slackbot/grade.svg)](https://squizlabs.github.io/PHP_CodeSniffer/analysis/iranianpep/slackbot)
[![StyleCI](https://styleci.io/repos/73189365/shield?branch=master)](https://styleci.io/repos/73189365)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d9b77f1a-3d4a-423f-b473-30a25496f9a0/mini.png)](https://insight.sensiolabs.com/projects/d9b77f1a-3d4a-423f-b473-30a25496f9a0)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/039ffa789e6a4040b9b8d596ede07db4)](https://www.codacy.com/app/iranianpep/slackbot?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=iranianpep/slackbot&amp;utm_campaign=Badge_Grade)

A PHP framework to create Slackbots faaaaaaaaster!

## Requirements
- PHP 5.5+ (including PHP 7.0+)
- Slack team

## Getting started
Using composer:
```
composer require slackbot/slackbot
```

Start listening to Slack messages:
```
try {
    (new \Slackbot\Slackbot())->run();
} catch (Exception $e) {
    echo $e->getMessage();
}
```

Send a message to Slack (make sure response is set to slack in the config):
```
$slackbot->send('general', 'Hello Slack!');
```

Also every functionality needs to be handled by a command e.g. `/help` which belongs to a plugin e.g. `Help` plugin. In other words, a plugin can have one or more commands.

## Add a new plugin / command
Add the new command to `src/Slackbot/CommandContainer.php` and also add the plugin file to `src/Slackbot/plugin`. For every command plugin name, action (which is a function with the same name in the plugin) and description need to be specified. e.g.
```
protected static $commands = [
    'ping' => [
        'plugin'      => 'Ping',
        'action'      => 'index',  
        'description' => 'Use as a health check',
    ]
];
```

Please note if `action` is not specified, `index` is considered as the default action. Finally, for each action add a function with the same name to the plugin file:
```
/**
 * Class Ping.
 */
class Ping extends AbstractPlugin
{
    /**
     * @return string
     */
    public function index()
    {
        return 'pong';
    }
}
```

## Configurations
|   Name    | Type | Description |
|:----------|:-----|:------------|
| testKey | string | This is only used for testing and to make sure that Config class works fine. value: `testValue` |
| testKeyReplace | string | This is used only for testing and to make sure that Config class works fine. value: `testValue {replaceIt}` |
| baseUrl | string | Base URL for listener. value: `http://localhost:8888` |
| defaultTimeZone | string | default value: `Australia/Melbourne` |
| apiToken | string | Your API key which can be found at Custom Integrations -> Bots -> Edit configuration (https://codejetter.slack.com/apps/manage/custom-integrations). This is required for outgoing webhook listeners |
| channelName | string | default value: `general` |
| botUsername | string | Bot username |
| verificationToken | string | Slack verification token which can be found at Custom Integrations settings. This is required for outgoing webhook and event listeners. This also can be used for slash commands as well. For Event listeners and can be found at https://api.slack.com/apps |
| chatLogging | boolean | If is true, all the conversations are logged in a text file |
| tmpFolderName | string | Temporary folder for the log file |
| chatLoggingFileName | string | Log file name |
| iconURL | string | Bot image URL |
| response | string | Response type. Possible values are `slack`, `json` or `slashCommand` |
| rootNamespace | string | Root namespace |
| defaultCommand | string | Default command if there is no command has been specified in the message |
| commandPrefix | string | Command prefix which is `/` by default |
| noCommandMessage | string | Message in case there is no command found in the message |
| unknownCommandMessage | string | Message in case the command in the message is unknown |
| confirmReceivedMessage | string | Message in case a message is received by the bot |
| listenerType | string | Type of the listener for Slack requests. Possible values are `webhook` or `event` |
| clientId | string | App credential: client id - This is required for Event listeners and can be found at https://api.slack.com/apps |
| clientSecret | string | App credential: client secret - This is required for Event listeners and can be found at https://api.slack.com/apps |
| scopes | array | App credential: permission scopes - This is required for Event listeners and can be found at https://api.slack.com/apps |
| apiAppId | string | The unique identifier for the application. This is required for Event listeners and can be found at https://api.slack.com/apps and your app url |

## Buy me half of a coffee if you like!
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BXMKEZ23PX8K2)
