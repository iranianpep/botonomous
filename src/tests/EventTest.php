<?php

namespace Slackbot\Tests;

use Slackbot\Dictionary;
use Slackbot\Event;

/**
 * Class EventTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBotId()
    {
        $event = new Event('message');
        $event->setBotId('B123');

        $this->assertEquals('B123', $event->getBotId());
    }
}
