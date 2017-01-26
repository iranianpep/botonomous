<?php

namespace Slackbot\Tests;

use Slackbot\utility\StringUtility;

/**
 * Class StringUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class StringUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Convert json to array.
     */
    public function testJsonToArray()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'input'  => '',
                'output' => [],
            ],
            [
                'input'  => '0',
                'output' => [],
            ],
            [
                'input'  => 0,
                'output' => [],
            ],
            [
                'input'  => '{"foo-bar": 12345}',
                'output' => [
                    'foo-bar' => 12345,
                ],
            ],
            [
                // this is an invalid json
                'input'  => '{bar:"baz"}',
                'output' => [],
            ],
            [
                // this is an invalid json
                'input'  => "{'bar':'baz'}",
                'output' => [],
            ],
            [
                'input'  => '{"foo-bar": 12345,}',
                'output' => [],
            ],
            [
                'input'  => '{"a":1,"b":2,"c":3,"d":4,"e":5}',
                'output' => [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                    'd' => 4,
                    'e' => 5,
                ],
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

    /**
     * Test removeStringFromString.
     */
    public function testRemoveStringFromString()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'input'  => [
                    'toRemove' => 'test',
                    'subject'  => 'this contains test',
                ],
                'output' => 'this contains',
            ],
            [
                'input'  => [
                    'toRemove' => 'test',
                    'subject'  => 'this contains test test test',
                ],
                'output' => 'this contains',
            ],
            [
                'input'  => [
                    'toRemove' => 'test',
                    'subject'  => 'this contains test test test and another word',
                ],
                'output' => 'this contains and another word',
            ],
            [
                'input'  => [
                    'toRemove' => ' ',
                    'subject'  => 'this contains test test test and another word',
                ],
                'output' => 'thiscontainstesttesttestandanotherword',
            ],
            [
                'input'  => [
                    'toRemove' => '',
                    'subject'  => 'this contains test test test and another word',
                ],
                'output' => 'this contains test test test and another word',
            ],
            [
                'input'  => [
                    'toRemove' => '',
                    'subject'  => 'this contains test test test and another word ',
                ],
                'output' => 'this contains test test test and another word',
            ],
            [
                'input'  => [
                    'toRemove' => 'blah blah',
                    'subject'  => 'this contains    test test test and another word',
                ],
                'output' => 'this contains test test test and another word',
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            $result = $utility->removeStringFromString(
                $inputOutput['input']['toRemove'],
                $inputOutput['input']['subject']
            );
            $this->assertEquals($inputOutput['output'], $result);
        }
    }

    /**
     * Test snakeCaseToCamelCase.
     */
    public function testSnakeCaseToCamelCase()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'input'  => 'email_domain',
                'output' => 'EmailDomain',
            ],
            [
                'input'  => 'id',
                'output' => 'Id',
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            $this->assertEquals(
                $inputOutput['output'],
                $utility->snakeCaseToCamelCase($inputOutput['input'])
            );
        }
    }

    /**
     * Test findInString.
     */
    public function testFindInString()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'toFind' => 'email',
                'string' => 'This is email',
                'output' => true,
            ],
            [
                'toFind' => 'email',
                'string' => 'This is',
                'output' => false,
            ],
            [
                'toFind' => 'emailTest',
                'string' => 'This is',
                'output' => false,
            ],
            [
                'toFind' => 'email',
                'string' => '',
                'output' => false,
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            $this->assertEquals(
                $inputOutput['output'],
                $utility->findInString($inputOutput['toFind'], $inputOutput['string'])
            );
        }
    }
}
