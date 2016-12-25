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
        $testData = $dictionary->get('test');

        $expected = [
            'test1',
            'test2',
        ];

        $this->assertEquals($expected, $testData);

        // get it again to check load only is called once
        $testData = $dictionary->get('test');

        $this->assertEquals($expected, $testData);
    }
}
