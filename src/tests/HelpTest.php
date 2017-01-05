<?php

namespace Slackbot\Tests;

use Slackbot\Config;
use Slackbot\plugin\help\Help;
use Slackbot\Slackbot;

class HelpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test index
     */
    public function testIndex()
    {
        $index = (new Help($this->getSlackbot()))->index();
        $this->assertFalse(empty($index));
    }

    /**
     * test invalid commands in index
     */
    public function testIndexInvalidCommands()
    {
        $slackbot = $this->getSlackbot();
        $slackbot->setCommands(['dummy']);

        $index = (new Help($slackbot))->index();

        $this->assertTrue(empty($index));
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
            'text'  => $botUsername.' /ping',
        ];

        return new Slackbot($request);
    }
}
