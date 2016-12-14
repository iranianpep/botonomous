<?php
use Slackbot\Command;

/**
 * Created by PhpStorm.
 * User: ehsan.abbasi
 * Date: 14/12/2016
 * Time: 12:55 PM
 */
class CommandTest extends PHPUnit_Framework_TestCase
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
