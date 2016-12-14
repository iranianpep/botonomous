<?php

namespace Slackbot\Tests;

use Slackbot\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $info = (new Command())->get('ping');
        $this->assertEquals($info['module'], 'Ping');
    }

    public function testGetAll()
    {
        $commands = (new Command())->getAll();
        $this->assertEquals($commands['ping']['module'], 'Ping');
    }
}
