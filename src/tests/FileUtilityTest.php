<?php

use Slackbot\utility\FileUtility;

class FileUtilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @throws Exception
     */
    public function testJsonFileToArray()
    {
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Slackbot' . DIRECTORY_SEPARATOR . 'dictionary' . DIRECTORY_SEPARATOR . 'test.json';

        $array = (new FileUtility())->jsonFileToArray($dir);

        $expected = [
            'test1',
            'test2'
        ];

        $this->assertEquals($expected, $array);
    }

    /**
     * @expectedException Exception
     * @throws Exception
     */
    public function testJsonFileToArrayEmptyPath()
    {
        $this->setExpectedException('Exception', 'File path is empty');

        (new FileUtility())->jsonFileToArray('');
    }

    /**
     * @expectedException Exception
     * @throws Exception
     */
    public function testJsonFileToArrayMissingFile()
    {
        $this->setExpectedException('Exception', 'File: \'/path/to/dummy.json\' does not exist or is not a file');

        (new FileUtility())->jsonFileToArray('/path/to/dummy.json');
    }

    /**
     * @expectedException Exception
     * @throws Exception
     */
    public function testJsonFileToArrayInvalidFile()
    {
        $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Slackbot' . DIRECTORY_SEPARATOR . 'Config.php';

        $this->setExpectedException('Exception', 'File: \'/Applications/MAMP/htdocs/slackbot/src/Slackbot/Config.php\' is not a json file');

        (new FileUtility())->jsonFileToArray($dir);
    }
}
