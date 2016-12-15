<?php

namespace Slackbot\Tests;

use Slackbot\utility\StringUtility;

/**
 * Class StringUtilityTest
 * @package Slackbot\Tests
 */
class StringUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Convert json to array
     */
    public function testJsonToArray()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'input' => '',
                'output' => []
            ],
            [
                'input' => '0',
                'output' => []
            ],
            [
                'input' => 0,
                'output' => []
            ],
            [
                'input' => '{"foo-bar": 12345}',
                'output' => [
                    'foo-bar' => 12345
                ]
            ],
            [
                // this is an invalid json
                'input' => '{bar:"baz"}',
                'output' => []
            ],
            [
                // this is an invalid json
                'input' => "{'bar':'baz'}",
                'output' => []
            ],
            [
                'input' => '{"foo-bar": 12345,}',
                'output' => []
            ],
            [
                'input' => '{"a":1,"b":2,"c":3,"d":4,"e":5}',
                'output' => [
                    "a" => 1,
                    "b" => 2,
                    "c" => 3,
                    "d" => 4,
                    "e" => 5
                ]
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            try {
                $this->assertEquals($inputOutput['output'], $utility->jsonToArray($inputOutput['input']));
            } catch (\Exception $e) {
                $this->assertEquals('Invalid JSON content', $e->getMessage());
            }
        }
    }
}
