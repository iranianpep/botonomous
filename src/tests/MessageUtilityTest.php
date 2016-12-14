<?php

use Slackbot\Config;
use Slackbot\utility\MessageUtility;

class MessageUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveMentionedBotUsername()
    {
        $config = new Config();
        $utility = new MessageUtility($config);
        
        $botUsername = $config->get('botUsername');
        $removed = $utility->removeMentionedBotUsername("@{$botUsername} /help");

        $this->assertEquals($removed, ' /help');

        $removed = $utility->removeMentionedBotUsername(" /help");

        $this->assertEquals($removed, ' /help');
    }

    public function testExtractCommandName()
    {
        $utility = new MessageUtility();
        $command = $utility->extractCommandName('/help ewdwedew @test /help de');

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(' /help ewdwedew @test /help de');

        $this->assertEquals('help', $command);

        $command = $utility->extractCommandName(' ddsfsdf /help ewdwedew @test /help de');

        $this->assertEquals(null, $command);
    }
    
    public function testExtractCommandDetails()
    {
        $utility = new MessageUtility();
        $botUsername = (new Config())->get('botUsername');
        $commandDetails = $utility->extractCommandDetails("@{$botUsername} /ping");

        $expected = [
            'module' => 'Ping',
            'description' => 'Use as a health check',
            'action' => 'index',
            'class' => 'Slackbot\plugin\Ping'
        ];

        $this->assertEquals($expected, $commandDetails);
    }
}
