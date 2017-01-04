<?php

namespace Slackbot;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAction()
    {
        $command = new Command('ping');

        $this->assertEquals(Command::DEFAULT_ACTION, $command->getAction());
    }
}
