<?php

namespace Slackbot;

use PHPUnit\Framework\TestCase;

/**
 * Class EventTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class EventTest extends TestCase
{
    public function testGetBotId()
    {
        $event = new Event('message');
        $event->setBotId('B123');

        $this->assertEquals('B123', $event->getBotId());
    }
}
