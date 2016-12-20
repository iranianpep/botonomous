# Slackbot Framework

[![Latest Stable Version](https://poser.pugx.org/slackbot/slackbot/v/stable)](https://packagist.org/packages/slackbot/slackbot)
[![Build Status](https://travis-ci.org/iranianpep/slackbot.svg?branch=master)](https://travis-ci.org/iranianpep/slackbot)
[![Code Climate](https://codeclimate.com/github/iranianpep/slackbot/badges/gpa.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![Test Coverage](https://codeclimate.com/github/iranianpep/slackbot/badges/coverage.svg)](https://codeclimate.com/github/iranianpep/slackbot/coverage)
[![Issue Count](https://codeclimate.com/github/iranianpep/slackbot/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![License](https://poser.pugx.org/slackbot/slackbot/license)](https://packagist.org/packages/slackbot/slackbot)
[![Code consistency](https://squizlabs.github.io/PHP_CodeSniffer/analysis/iranianpep/slackbot/grade.svg)](https://squizlabs.github.io/PHP_CodeSniffer/analysis/iranianpep/slackbot)
[![StyleCI](https://styleci.io/repos/73189365/shield?branch=master)](https://styleci.io/repos/73189365)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/d9b77f1a-3d4a-423f-b473-30a25496f9a0/mini.png)](https://insight.sensiolabs.com/projects/d9b77f1a-3d4a-423f-b473-30a25496f9a0)

A PHP framework to create Slackbots faaaaaaaaster!

## Requirements
- PHP 5.5+ (also compatible with HHVM)
- Slack channel

## Getting started
Using composer:
```
composer require slackbot/slackbot
```

Start listening to Slack messages:
```
(new \Slackbot\Slackbot($_POST))->listenToSlack();
```

Send a message to Slack (make sure response is set to slack in the config):
```
$slackbot->send('Hello Slack!');
```

## Configurations
|   Name    | Type | Description |
|:----------|:-----|:------------|
| testKey | string | This is only used for testing and to make sure that Config class works fine. value: `testValue` |
| testKeyReplace | string | This is used only for testing and to make sure that Config class works fine. value: `testValue {replaceIt}` |
| defaultTimeZone | string | default value: `Australia/Melbourne` |
| apiToken | string | Your API key |
| channelName | string | default value: `#general` |
| botUsername | string | Bot username |
| outgoingWebhookToken | string | Slack outgoing webhook token |
| chatLogging | boolean | If is true, all the conversations are logged in a text file |
| tmpFolderName | string | Temporary folder for the log file |
| chatLoggingFileName | string | Log file name |
| iconURL | string | Bot image URL |
| response | string | Response type. Possible values are `slack` or `json` |
| rootNamespace | string | Root namespace |
| defaultCommand | string | Default command if there is no command has been specified in the message |
| noCommandMessage | string | Message in case there is no command found in the message |
| unknownCommandMessage | string | Message in case the command in the message is unknown |

## Add a new plugin / command
Add the new command to `src/Slackbot/Command.php` and also add the plugin file to `src/Slackbot/plugin`.

## Using Python nltk

1. Install Python
```
brew install python
```

2. Install nltk
```
curl https://bootstrap.pypa.io/ez_setup.py -o - | sudo python
sudo easy_install pip
sudo pip install -U nltk
```

3. Install nltk data
    Start Python and run the following
```
>>> import nltk
>>> nltk.download('all')
```

## Buy me half of a coffee if you like!
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BXMKEZ23PX8K2)
