<?php

namespace Botonomous\utility;

use PHPUnit\Framework\TestCase;

class ArrayUtilityTest extends TestCase
{
    /**
     * Test filterArray.
     */
    public function testFilterArray()
    {
        $utility = new ArrayUtility();

        $toFilter = [
            'token'    => '123',
            'dummyKey' => '333',
            'access'   => 'public',
        ];

        $expected = [
            'token'  => '123',
            'access' => 'public',
        ];

        $filtered = $utility->filterArray($toFilter, [
            'token',
            'access',
        ]);

        $this->assertEquals($expected, $filtered);
    }

    /**
     * Test filterArray.
     */
    public function testArrayKeyValueExists()
    {
        $utility = new ArrayUtility();

        $inputsOutputs = [
            [
                'input' => [
                    'test'  => 1,
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => '',
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => false,
            ],
            [
                'input' => [
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => false,
            ],
            [
                'input' => [
                    'test'  => ' ',
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => false,
            ],
            [
                'input' => [
                    'test'  => 0,
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => 00,
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => '0',
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => true,
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => false,
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
            [
                'input' => [
                    'test'  => [],
                    'test2' => 2,
                ],
                'key'      => 'test',
                'expected' => true,
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $this->assertEquals(
                $inputOutput['expected'],
                $utility->arrayKeyValueExists($inputOutput['key'], $inputOutput['input'])
            );
        }
    }
}
