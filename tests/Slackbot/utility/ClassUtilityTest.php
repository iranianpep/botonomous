<?php

namespace Slackbot\utility;

use PHPUnit\Framework\TestCase;
use Slackbot\Channel;
use Slackbot\Event;

class ClassUtilityTest extends TestCase
{
    const DUMMY_ID = 'C0LCK9DH6';
    const DUMMY_NAME = 'general';

    public function testExtractClassNameFromFullName()
    {
        $utility = new ClassUtility();
        $this->assertEquals('WhiteList', $utility->extractClassNameFromFullName('Slackbot\WhiteList'));

        $this->assertEquals('WhiteList', $utility->extractClassNameFromFullName('WhiteList'));

        $this->assertEquals('test', $utility->extractClassNameFromFullName('Slackbot\WhiteList\test'));

        $this->assertEquals('', $utility->extractClassNameFromFullName(''));
    }

    public function testLoadAttributes()
    {
        $utility = new ClassUtility();

        $channel = new Channel();
        $info = [
            'id'   => self::DUMMY_ID,
            'name' => self::DUMMY_NAME,
        ];

        $channel = $utility->loadAttributes($channel, $info);

        $this->assertEquals(self::DUMMY_ID, $channel->getSlackId());
        $this->assertEquals(self::DUMMY_NAME, $channel->getName());

        $event = new Event('message');
        $info = [
            'bot_id' => self::DUMMY_ID,
            // should be ignore
            'non_existent_attr' => 'dummy_value',
            'ts'                => '1355517523.000005',
            'event_ts'          => '1355517523.000005',
        ];

        $event = $utility->loadAttributes($event, $info);

        $this->assertEquals(self::DUMMY_ID, $event->getBotId());
        $this->assertEquals('1355517523.000005', $event->getTimestamp());
        $this->assertEquals('1355517523.000005', $event->getEventTimestamp());
        $this->assertEmpty($event->getText());
    }
}
