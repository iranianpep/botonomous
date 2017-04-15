<?php

namespace Slackbot;

/* @noinspection PhpUndefinedClassInspection */
use PHPUnit\Framework\TestCase;

/** @noinspection PhpUndefinedClassInspection */
class CommandTest extends TestCase
{
    const PING_KEY = 'ping';

    public function testGetAction()
    {
        $this->assertEquals(Command::DEFAULT_ACTION, (new Command(self::PING_KEY))->getAction());
    }

    public function testGetKey()
    {
        $this->assertEquals(self::PING_KEY, (new Command(self::PING_KEY))->getKey());
    }
}
