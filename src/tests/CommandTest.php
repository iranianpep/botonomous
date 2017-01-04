<?php

namespace Slackbot;

/** @noinspection PhpUndefinedClassInspection */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    const PING_KEY = 'ping';

    public function testGetAction()
    {
        $command = new Command(self::PING_KEY);

        $this->assertEquals(Command::DEFAULT_ACTION, $command->getAction());
    }

    public function testGetKey()
    {
        $command = new Command(self::PING_KEY);

        $this->assertEquals(self::PING_KEY, $command->getKey());
    }
}
