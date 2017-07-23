<?php

namespace Botonomous\plugin\help;

use PHPUnit\Framework\TestCase;

/** @noinspection PhpUndefinedClassInspection */
class HelpConfigTest extends TestCase
{
    public function testGet()
    {
        $config = new HelpConfig();
        $result = $config->get('testConfigKey');
        $this->assertEquals('testConfigValue', $result);
    }
}
