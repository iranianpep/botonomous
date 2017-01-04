<?php

namespace Slackbot\Tests;

use Slackbot\Command;
use Slackbot\CommandContainer;

/**
 * Class CommandTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class CommandContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get.
     */
    public function testGet()
    {
        $commandObject = (new CommandContainer())->get('ping');

        $this->assertTrue($commandObject instanceof Command);

        $this->assertEquals($commandObject->getPlugin(), 'Ping');
    }

    /**
     * Test getAll.
     */
    public function testGetAll()
    {
        $commands = (new CommandContainer())->getAll();
        $this->assertEquals($commands['ping']->getPlugin(), 'Ping');
    }
}
