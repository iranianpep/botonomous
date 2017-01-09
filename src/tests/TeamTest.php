<?php

namespace Slackbot\Tests;

/**
 * Class TeamTest.
 */
use Slackbot\Team;

/** @noinspection PhpUndefinedClassInspection */
class TeamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSlackId()
    {
        $team = new Team();
        $team->setSlackId('T0LCJF334');

        $this->assertEquals('T0LCJF334', $team->getSlackId());
    }

    public function testGetName()
    {
        $team = new Team();
        $team->setName('test');

        $this->assertEquals('test', $team->getName());
    }

    public function testGetIcon()
    {
        $team = new Team();

        $icon = [
            'image_34'      => 'http:',
            'image_44'      => 'http:',
            'image_default' => true
        ];

        $team->setIcon($icon);

        $this->assertEquals($icon, $team->getIcon());
    }

    public function testGetEmailDomain()
    {
        $team = new Team();
        $team->setEmailDomain('test');

        $this->assertEquals('test', $team->getEmailDomain());
    }
    
    public function testGetDomain()
    {
        $team = new Team();
        $team->setDomain('test');

        $this->assertEquals('test', $team->getDomain());
    }
    
    public function testIsIconDefault()
    {
        $team = new Team();

        $icon = [
            'image_34'      => 'http:',
            'image_44'      => 'http:',
            'image_default' => true
        ];

        $team->setIcon($icon);

        $this->assertTrue($team->isIconDefault());

        $icon = [
            'image_34'      => 'http:',
            'image_44'      => 'http:',
        ];

        $team->setIcon($icon);

        $this->assertFalse($team->isIconDefault());
    }
}
