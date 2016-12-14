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
        $command = new Command();
        $info = $command->get('ping');
        
        $this->assertEquals($info['module'], 'Ping');
    }

    public function testGetAll()
    {
        $command = new Command();
        $commands = $command->getAll();

        $this->assertEquals($commands['ping']['module'], 'Ping');
    }
}
