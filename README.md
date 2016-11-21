# slackbot
Simple Slackbot that listens to Slack messages and send back appropriate responses to a channel(s). The bot uses Slack outgoing webhooks to receive the messages and sends the messages back using cURL.

## Getting started
Using composer the slackbot can be installed quickly:
```
composer require slackbot/slackbot
```

Start listening to Slack messages:

```
$slackbot = new Slackbot();
$slackbot->listen();
```

Send a message to Slack:
```
$slackbot->send('Hello Slack!');
```