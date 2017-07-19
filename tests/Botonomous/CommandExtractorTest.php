<?php

namespace Botonomous;

use PHPUnit\Framework\TestCase;

/**
 * Class CommandExtractorTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class CommandExtractorTest extends TestCase
{
    /**
     * Test getConfig.
     */
    public function testGetConfig()
    {
        $commandExtractor = new CommandExtractor();
        $config = new Config();
        $commandExtractor->setConfig($config);

        $this->assertEquals($config, $commandExtractor->getConfig());
    }
}
