<?php

class MessageUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveMentionedBotUsername()
    {
        $utility = new \Slackbot\utility\MessageUtility();
        $removed = $utility->removeMentionedBotUsername('@YOUR_BOT_USERNAME /help');

        $this->assertEquals($removed, ' /help');
    }

    public function testExtractCommand()
    {
        $utility = new \Slackbot\utility\MessageUtility();
        $command = $utility->extractCommand('/help ewdwedew @test /help de');

        $this->assertEquals('help', $command);
    }
}
