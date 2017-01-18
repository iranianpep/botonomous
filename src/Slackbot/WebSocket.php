<?php

namespace Slackbot;

use React\EventLoop\Factory;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class WebSocket
{
    private $webSocketUrl;

    public function client()
    {
        $loop = Factory::create();

        $logger = new Logger();
        $writer = new Stream("php://output");
        $logger->addWriter($writer);

        $client = new \Devristo\Phpws\Client\WebSocket($this->getWebSocketUrl(), $loop, $logger);

        $client->on('message', function ($message) use ($client, $logger) {
            $logger->notice('Got message');

            // body goes here

            $client->close();
        });

        $client->open();
        $loop->run();
    }

    /**
     * @return string
     */
    public function getWebSocketUrl()
    {
        return $this->webSocketUrl;
    }

    /**
     * @param string $webSocketUrl
     */
    public function setWebSocketUrl($webSocketUrl)
    {
        $this->webSocketUrl = $webSocketUrl;
    }
}
