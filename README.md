# slackbot
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
>>> nltk.download()
```