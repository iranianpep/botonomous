# slackbot

[![Latest Stable Version](https://poser.pugx.org/slackbot/slackbot/v/stable)](https://packagist.org/packages/slackbot/slackbot)
[![Build Status](https://travis-ci.org/iranianpep/slackbot.svg?branch=master)](https://travis-ci.org/iranianpep/slackbot)
[![Code Climate](https://codeclimate.com/github/iranianpep/slackbot/badges/gpa.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![Test Coverage](https://codeclimate.com/github/iranianpep/slackbot/badges/coverage.svg)](https://codeclimate.com/github/iranianpep/slackbot/coverage)
[![Issue Count](https://codeclimate.com/github/iranianpep/slackbot/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/slackbot)
[![License](https://poser.pugx.org/slackbot/slackbot/license)](https://packagist.org/packages/slackbot/slackbot)

Simple Slackbot that listens to Slack messages and send back appropriate responses to a channel(s). The bot uses Slack outgoing webhooks to receive the messages and sends the messages back using cURL.

## Getting started
Using composer the slackbot can be installed quickly:
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