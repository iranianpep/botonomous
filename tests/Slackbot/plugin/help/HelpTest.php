<?php

namespace Slackbot\plugin\help;

use PHPUnit\Framework\TestCase;
use Slackbot\PhpunitHelper;

/** @noinspection PhpUndefinedClassInspection */
class HelpTest extends TestCase
{
    /**
     * test index.
     */
    public function testIndex()
    {
        $index = (new Help((new PhpunitHelper())->getSlackbot()))->index();
        $this->assertFalse(empty($index));
    }

    /**
     * test invalid commands in index.
     */
    public function testIndexInvalidCommands()
    {
        $slackbot = (new PhpunitHelper())->getSlackbot();
        $slackbot->setCommands(['dummy']);

        $index = (new Help($slackbot))->index();

        $this->assertTrue(empty($index));
    }
}
