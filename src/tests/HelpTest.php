<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\plugin\help\Help;
use Slackbot\Slackbot;

/** @noinspection PhpUndefinedClassInspection */
class HelpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test index.
     */
    public function testIndex()
    {
        $this->assertFalse(empty((new Help($this->getSlackbot()))->index()));
    }

    /**
     * test invalid commands in index.
     */
    public function testIndexInvalidCommands()
    {
        $slackbot = $this->getSlackbot();
        $slackbot->setCommands(['dummy']);

        $this->assertTrue(empty((new Help($slackbot))->index()));
    }

    /**
     * @throws \Exception
     *
     * @return \Slackbot\Slackbot
     */
    private function getSlackbot()
    {
        $config = new Config();

        /**
         * Form the request.
         */
        $botUsername = '@'.$config->get('botUsername');
        $request = [
            'token' => $config->get('outgoingWebhookToken'),
            'text'  => $botUsername.' /help',
        ];

        $slackbot = new Slackbot();

        // get listener
        $listener = $slackbot->getListener();

        // set request
        $listener->setRequest($request);

        return $slackbot;
    }
}
