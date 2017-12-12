<?php

namespace Botonomous\utility;

use PHPUnit\Framework\TestCase;

/**
 * Class FileUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class FileUtilityTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testJsonFileToArray()
    {
        $dir = $this->getSlackbotDir().DIRECTORY_SEPARATOR.'dictionary'.DIRECTORY_SEPARATOR.'test.json';

        $array = (new FileUtility())->jsonFileToArray($dir);

        $expected = [
            'test1',
            'test2',
        ];

        $this->assertEquals($expected, $array);
    }

    /**
     * Test jsonFileToArrayEmptyPath.
     */
    public function testJsonFileToArrayEmptyPath()
    {
        try {
            (new FileUtility())->jsonFileToArray('');
        } catch (\Exception $e) {
            $this->assertEquals(FileUtility::EMPTY_FILE_PATH_MESSAGE, $e->getMessage());
        }
    }

    /**
     * Test jsonFileToArrayMissingFile.
     */
    public function testJsonFileToArrayMissingFile()
    {
        try {
            (new FileUtility())->jsonFileToArray('/path/to/dummy.json');
        } catch (\Exception $e) {
            $this->assertEquals(FileUtility::MISSING_FILE_MESSAGE, $e->getMessage());
        }
    }

    /**
     * Test jsonFileToArrayInvalidFile.
     */
    public function testJsonFileToArrayInvalidFile()
    {
        $dir = $this->getSlackbotDir().DIRECTORY_SEPARATOR.'Config.php';

        try {
            (new FileUtility())->jsonFileToArray($dir);
        } catch (\Exception $e) {
            $this->assertEquals(FileUtility::INVALID_JSON_FILE_MESSAGE, $e->getMessage());
        }
    }

    private function getSlackbotDir()
    {
        $namespaceParts = explode('\\', __NAMESPACE__);
        $rootNamespace = $namespaceParts[0];

        return dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.$rootNamespace;
    }
}
