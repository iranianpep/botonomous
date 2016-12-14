<?php

use Slackbot\utility\FileUtility;

class FileUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testJsonFileToArray()
    {
        $utility = new FileUtility();
        
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Slackbot' . DIRECTORY_SEPARATOR . 'dictionary' . DIRECTORY_SEPARATOR . 'test.json';

        $array = $utility->jsonFileToArray($dir);

        $expected = [
            'test1',
            'test2'
        ];

        $this->assertEquals($expected, $array);
    }
}
