<?php

namespace Slackbot\Tests;

use Slackbot\Dictionary;

/**
 * Class DictionaryTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get.
     */
    public function testGet()
    {
        $dictionary = new Dictionary();
        $expected = [
            'test1',
            'test2',
        ];

        $this->assertEquals($expected, $dictionary->get('test'));

        // get it again to check load only is called once
        $this->assertEquals($expected, $dictionary->get('test'));
    }
}
