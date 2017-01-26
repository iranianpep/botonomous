<?php

namespace Slackbot\Tests;

use Slackbot\plugin\help\Help;

/** @noinspection PhpUndefinedClassInspection */
class HelpTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        require_once 'PhpunitHelper.php';
        parent::__construct();
    }

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
