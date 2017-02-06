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
        $commandContainer = new CommandContainer();
        $commandObject = $commandContainer->getAsObject('ping');

        $this->assertTrue($commandObject instanceof Command);

        /* @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($commandObject->getPlugin(), 'Ping');
    }

    /**
     * Test getAll.
     */
    public function testGetAllAsObject()
    {
        $commands = (new CommandContainer())->getAllAsObject();
        /* @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($commands['ping']->getPlugin(), 'Ping');
    }

    /**
     * Test getAll.
     */
    public function testGetAllAsObjectUnknownSetter()
    {
        $commands = new CommandContainer();
        $allCommands = $commands->getAll();

        // set unknown attribute / property
        $allCommands['ping']['testKey'] = 'testValue';

        $commands->setAll($allCommands);

        $commandObjects = $commands->getAllAsObject();

        /* @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals($commandObjects['ping']->getPlugin(), 'Ping');
    }

    /**
     * Test getAll.
     */
    public function testGetAllAsObjectEmpty()
    {
        $commands = new CommandContainer();

        $allCommands = $commands->getAll();

        $commands->setAll([]);

        /* @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals([], $commands->getAllAsObject());

        // reset the command container
        $commands->setAll($allCommands);
    }
}
