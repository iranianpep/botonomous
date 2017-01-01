<?php

namespace Slackbot\Tests;

use Slackbot\CommandContainer;

/**
 * Class CommandTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get.
     */
    public function testGet()
    {
        $info = (new CommandContainer())->get('ping');
        $this->assertEquals($info['plugin'], 'Ping');
    }

    /**
     * Test getAll.
     */
    public function testGetAll()
    {
        $commands = (new CommandContainer())->getAll();
        $this->assertEquals($commands['ping']['plugin'], 'Ping');
    }
}
