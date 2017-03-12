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
            [
                'input'  => 'message_timestamp',
                'output' => 'MessageTimestamp',
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

    /**
     * Test isString1FollowedByString2.
     */
    public function testIsString1FollowedByString2()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'subject'    => 'This is email',
                's1'         => 'is',
                's2'         => 'email',
                'exceptions' => [],
                'output'     => true,
            ],
            [
                'subject'    => 'This is email',
                's1'         => 'This',
                's2'         => 'email',
                'exceptions' => ['is'],
                'output'     => false,
            ],
            [
                'subject'    => 'This is email',
                's1'         => 'This',
                's2'         => 'email',
                'exceptions' => [],
                'output'     => true,
            ],
            [
                'subject'    => 'This is email and first name',
                's1'         => 'is',
                's2'         => 'first name',
                'exceptions' => ['email'],
                'output'     => false,
            ],
            [
                'subject'    => 'This is email and first name',
                's1'         => 'is',
                's2'         => 'email',
                'exceptions' => ['email'],
                'output'     => true,
            ],
            [
                'subject'    => 'This is email and first name',
                's1'         => 'email',
                's2'         => 'is',
                'exceptions' => ['email'],
                'output'     => false,
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            $this->assertEquals(
                $inputOutput['output'],
                $utility->isString1FollowedByString2(
                    $inputOutput['subject'],
                    $inputOutput['s1'],
                    $inputOutput['s2'],
                    $inputOutput['exceptions']
                )
            );
        }
    }

    public function testEndsWith()
    {
        $utility = new StringUtility();

        $inputOutputs = [
            [
                'haystack' => 'test',
                'needle'   => 'test',
                'output'   => true,
            ],
            [
                'haystack' => 'test1',
                'needle'   => 'test',
                'output'   => false,
            ],
            [
                'haystack' => 'message_ts',
                'needle'   => '_ts',
                'output'   => true,
            ],
            [
                'haystack' => 'message_ts',
                'needle'   => '_ts ',
                'output'   => false,
            ],
            [
                'haystack' => 'message_ts',
                'needle'   => '',
                'output'   => true,
            ],
            [
                'haystack' => '',
                'needle'   => '',
                'output'   => true,
            ],
            [
                'haystack' => '',
                'needle'   => 't',
                'output'   => false,
            ],
        ];

        foreach ($inputOutputs as $inputOutput) {
            $this->assertEquals(
                $inputOutput['output'],
                $utility->endsWith($inputOutput['haystack'], $inputOutput['needle'])
            );
        }
    }
}
