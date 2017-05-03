<?php

namespace Botonomous\utility;

use PHPUnit\Framework\TestCase;
use Botonomous\Command;
use Botonomous\Config;

/**
 * Class MessageUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class MessageUtilityTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testRemoveMentionedBot()
    {
        $config = new Config();
        $utility = new MessageUtility($config);

        $botUserId = $config->get('botUserId');
        $removed = $utility->removeMentionedBot("<@{$botUserId}> /help");

        $this->assertEquals($removed, ' /help');

        $removed = $utility->removeMentionedBot(' /help');

        $this->assertEquals($removed, ' /help');

        $removed = $utility->removeMentionedBot("<@{$botUserId}> /help <@{$botUserId}>");

        $this->assertEquals($removed, " /help <@{$botUserId}>");

        $removed = $utility->removeMentionedBot("<@{$botUserId}> <@{$botUserId}>");

        $this->assertEquals($removed, " <@{$botUserId}>");

        $removed = $utility->removeMentionedBot("Test <@{$botUserId}>");

        $this->assertEquals($removed, 'Test ');
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
        $botUserId = $config->get('botUserId');
        $commandPrefix = $config->get('commandPrefix');
        $commandObject = $utility->extractCommandDetails("<@{$botUserId}> {$commandPrefix}ping");

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

        $this->expectException('\Exception');
        $this->expectExceptionMessage('User id is not provided');

        $this->assertEquals('<@U024BE7LH>', $utility->linkToUser(''));
    }

    public function testIsBotMentioned()
    {
        $config = new Config();
        $utility = new MessageUtility($config);

        $botUserId = $config->get('botUserId');

        $this->assertEquals(true, $utility->isBotMentioned("<@{$botUserId}> /help"));
        $this->assertEquals(true, $utility->isBotMentioned("How are you <@{$botUserId}>?"));
        $this->assertEquals(false, $utility->isBotMentioned('/help'));
    }
}
