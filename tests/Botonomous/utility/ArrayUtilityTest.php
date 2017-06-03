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

    public function testSetNestedArrayValue()
    {
        $utility = new ArrayUtility();

        $inputsOutputs = [
            [
                'input'     => ['test' => 1, 'test2' => 2],
                'path'      => ['test'],
                'value'     => 3,
                'expected'  => ['test'  => 3, 'test2' => 2],
            ],
            [
                'input'     => ['test' => 1],
                'path'      => ['dummy'],
                'value'     => 3,
                'expected'  => ['test' => 1, 'dummy' => 3],
            ],
            [
                'input' => [
                    'test'  => 1,
                ],
                'path'      => ['dummy1', 'dummy2'],
                'value'     => 3,
                'expected'  => ['test'  => 1, 'dummy1' => ['dummy2' => 3]],
            ],
            [
                'input'     => ['test' => 1, 'test2' => 2],
                'path'      => ['test'],
                'value'     => null,
                'expected'  => ['test'  => null, 'test2' => 2],
            ],
            [
                'input' => [
                    'test'  => 1,
                    'test2' => ['test3' => 3, 'test4' => 4],
                ],
                'path'      => ['test2', 'test4'],
                'value'     => 5,
                'expected'  => [
                    'test'  => 1,
                    'test2' => ['test3' => 3, 'test4' => 5],
                ],
            ],
            [
                'input' => [
                    'test'  => 1,
                    'test2' => ['test3' => 3, 'test4' => 4],
                ],
                'path'      => ['test2', 'test3'],
                'value'     => 6,
                'expected'  => [
                    'test'  => 1,
                    'test2' => ['test3' => 6, 'test4' => 4],
                ],
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $utility->setNestedArrayValue(
                $inputOutput['input'],
                $inputOutput['path'],
                $inputOutput['value']
            );

            $this->assertEquals($inputOutput['expected'], $inputOutput['input']);
        }
    }

    public function testGetArrayPath()
    {
        $utility = new ArrayUtility();

        $inputsOutputs = [
            [
                'input' => [
                    'test'  => 1,
                    'test2' => 2,
                ],
                'path'      => ['test'],
                'expected'  => 1,
            ],
            [
                'input' => [
                    'test'  => 1,
                ],
                'path'      => ['dummy'],
                'expected'  => null,
            ],
            [
                'input' => [
                    'test'  => 1,
                ],
                'path'      => ['dummy1', 'dummy2'],
                'expected'  => null,
            ],
            [
                'input' => [
                    'test'  => 1,
                    'test2' => [
                        'test3' => 3,
                        'test4' => 4,
                    ],
                ],
                'path'      => ['test2', 'test4'],
                'expected'  => 4,
            ],
            [
                'input' => [
                    'test'  => 1,
                    'test2' => [
                        'test3' => 3,
                        'test4' => 4,
                    ],
                ],
                'path'      => ['test2'],
                'expected'  => [
                    'test3' => 3,
                    'test4' => 4,
                ],
            ],
            [
                'input' => [
                    'test'  => 1,
                    'test2' => false,
                ],
                'path'      => ['test2'],
                'expected'  => false,
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $result = $utility->getNestedArrayValue(
                $inputOutput['input'],
                $inputOutput['path']
            );

            $this->assertEquals($inputOutput['expected'], $result);
        }
    }
}
