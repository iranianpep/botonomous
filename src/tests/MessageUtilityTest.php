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
        $config = new Config();
        $commandPrefix = $config->get('commandPrefix');

        $command = $utility->extractCommandName("{$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(" {$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(" dummy {$commandPrefix}help dummy @test {$commandPrefix}help dummy");

        $this->assertEquals(null, $command);

        $config->set('commandPrefix', '');
        $commandPrefix = $config->get('commandPrefix');

        $command = $utility->extractCommandName("{$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(" dummy {$commandPrefix}help dummy @test {$commandPrefix}help dummy");

        $this->assertEquals('dummy', $command);

        $command = $utility->extractCommandName(" {$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $config->set('commandPrefix', '@');
        $commandPrefix = $config->get('commandPrefix');

        $command = $utility->extractCommandName("{$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(" {$commandPrefix}help dummy @test {$commandPrefix}help de");

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(" dummy {$commandPrefix}help dummy @test {$commandPrefix}help dummy");

        $this->assertEquals(null, $command);
    }

    /**
     * @throws \Exception
     */
    public function testExtractCommandDetails()
    {
        $utility = new MessageUtility();
        $config = new Config();
        $botUsername = $config->get('botUsername');
        $commandPrefix = $config->get('commandPrefix');
        $commandObject = $utility->extractCommandDetails("@{$botUsername} {$commandPrefix}ping");

        $expected = new Command('ping');
        $expected->setPlugin('Ping');
        $expected->setDescription('Use as a health check');

        $this->assertEquals($expected, $commandObject);
    }

    /**
     * test removeTriggerWord.
     */
    public function testRemoveTriggerWord()
    {
        $utility = new MessageUtility();
        $result = $utility->removeTriggerWord('google_bot: do this google_bot', 'google_bot:');

        $this->assertEquals('do this google_bot', $result);
    }

    /**
     * test linkToUser.
     */
    public function testLinkToUser()
    {
        $utility = new MessageUtility();

        $this->assertEquals('<@U024BE7LH>', $utility->linkToUser('U024BE7LH'));

        $this->assertEquals('<@U024BE7LH|bob>', $utility->linkToUser('U024BE7LH', 'bob'));

        $this->assertEquals('<@U024BE7LH>', $utility->linkToUser('U024BE7LH', ''));

        $this->setExpectedException(
            'Exception',
            'User id is not provided'
        );

        $this->assertEquals('<@U024BE7LH>', $utility->linkToUser(''));
    }
}
