<?php

namespace Slackbot\Tests;

use Slackbot\Command;
use Slackbot\Config;
use Slackbot\utility\MessageUtility;

/**
 * Class MessageUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class MessageUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \Exception
     */
    public function testRemoveMentionedBotUsername()
    {
        $config = new Config();
        $utility = new MessageUtility($config);

        $botUsername = $config->get('botUsername');
        $removed = $utility->removeMentionedBotUsername("@{$botUsername} /help");

        $this->assertEquals($removed, ' /help');

        $removed = $utility->removeMentionedBotUsername(' /help');

        $this->assertEquals($removed, ' /help');
    }

    /**
     *  Test extractCommandName.
     */
    public function testExtractCommandName()
    {
        $utility = new MessageUtility();
        $command = $utility->extractCommandName('/help dummy @test /help de');

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(' /help dummy @test /help de');

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(' dummy /help dummy @test /help dummy');

        $this->assertEquals(null, $command);
    }

    /**
     * @throws \Exception
     */
    public function testExtractCommandDetails()
    {
        $utility = new MessageUtility();
        $botUsername = (new Config())->get('botUsername');
        $commandObject = $utility->extractCommandDetails("@{$botUsername} /ping");

        $commandObjectExpected = new Command('ping');
        $commandObjectExpected->setPlugin('Ping');
        $commandObjectExpected->setDescription('Use as a health check');

        $this->assertEquals($commandObjectExpected, $commandObject);
    }
}
