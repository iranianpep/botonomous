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
<table width='100%'>
<thead>
<tr>
<th>Name</th>
<th>Type</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>testKey</td>
<td>string</td>
<td>value: testValue</td>
</tr>
<tr>
<td>testKeyReplace</td>
<td>string</td>
<td>value: testValue {replaceIt}</td>
</tr>
<tr>
<td>defaultTimeZone</td>
<td>string</td>
<td>default value: Australia/Melbourne</td>
</tr>
<tr>
<td>apiToken</td>
<td>string</td>
<td>Your API key</td>
</tr>
<tr>
<td>channelName</td>
<td>string</td>
<td>default value: #general</td>
</tr>
<tr>
<td>botUsername</td>
<td>string</td>
<td>Your bot username</td>
</tr>
<tr>
<td>outgoingWebhookToken</td>
<td>string</td>
<td>Your outgoing webhook token</td>
</tr>
<tr>
<td>chatLogging</td>
<td>boolean</td>
<td>When is set to true, all the conversations are logged in a text file</td>
</tr>
<tr>
<td>tmpFolderName</td>
<td>string</td>
<td>Temporary folder for the log file</td>
</tr>
<tr>
<td>chatLoggingFileName</td>
<td>string</td>
<td>Log file name</td>
</tr>
<tr>
<td>iconURL</td>
<td>string</td>
<td>Bot image URL</td>
</tr>
<tr>
<td>response</td>
<td>string</td>
<td>Response type. Possible values are json or slack</td>
</tr>
<tr>
<td>rootNamespace</td>
<td>string</td>
<td>Root namespace</td>
</tr>
<tr>
<td>defaultCommand</td>
<td>string</td>
<td>Default command if there is no command has been specified in the message</td>
</tr>
<tr>
<td>noCommandMessage</td>
<td>string</td>
<td>Message in case there is no command found in the message</td>
</tr>
<tr>
<td>unknownCommandMessage</td>
<td>string</td>
<td>Message in case the command in the message is unknown</td>
</tr>
</tbody>
</table>

## Add a new plugin / command
Add the new command to `src/Slackbot/Command.php` and also add the plugin file to `src/Slackbot/plugin`.

## Using Python nltk

1.Install Python
```
brew install python
```

2.Install nltk
```
curl https://bootstrap.pypa.io/ez_setup.py -o - | sudo python
sudo easy_install pip
sudo pip install -U nltk
```

3.Install nltk data

Start Python and run the following
```
>>> import nltk
>>> nltk.download('all')
```

## Buy me half of a coffee if you like!
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BXMKEZ23PX8K2)
