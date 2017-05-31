<?php

namespace Botonomous;

use PHPUnit\Framework\TestCase;

/**
 * Class DictionaryTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class DictionaryTest extends TestCase
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

    /**
     * Test getValueByKey.
     */
    public function testGetValueByKey()
    {
        $dictionary = new Dictionary();

        $this->assertEquals('testValue', $dictionary->getValueByKey('test-key-value', 'testKey'));
    }

    /**
     * Test getValueByKey.
     */
    public function testGetValueByInvalidKey()
    {
        $dictionary = new Dictionary();

        $this->expectException('\Exception');
        $this->expectExceptionMessage("Key: 'testInvalidKey' does not exist in file: test-key-value");

        $this->assertEquals('testValue', $dictionary->getValueByKey('test-key-value', 'testInvalidKey'));
    }

    /**
     * Test getValueByKey.
     */
    public function testGetValueByKeyWithReplacements()
    {
        $dictionary = new Dictionary();

        $this->assertEquals(
            'testValue dummy user',
            $dictionary->getValueByKey(
                'test-key-value',
                'testKeyWithReplacements',
                ['user' => 'dummy user']
            )
        );
    }
}
