<?php

namespace Slackbot\Tests;

use PHPUnit\Framework\TestCase;
use Slackbot\Event;

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
