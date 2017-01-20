<?php

namespace Slackbot\Tests;

use Slackbot\utility\LanguageProcessingUtility;

/**
 * Class LanguageProcessingUtilityTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class LanguageProcessingUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test stem.
     */
    public function testStemPhp()
    {
        $utility = new LanguageProcessingUtility();

        $result = $utility->stem('Stemming is funnier than a bummer says the sushi loving computer scientist');

        $this->assertEquals('Stem is funnier than a bummer sai the sushi love comput scientist', $result);
    }

    /**
     * Test stem.
     */
    public function testStemEmpty()
    {
        $utility = new LanguageProcessingUtility();

        $result = $utility->stem('');

        $this->assertEquals('', $result);
    }

    /**
     * Test removePunctuations.
     */
    public function testRemovePunctuations()
    {
        $utility = new LanguageProcessingUtility();

        $result = $utility->removePunctuations('A dummy text?');

        $expected = 'A dummy text';

        $this->assertEquals($expected, $result);

        $result = $utility->removePunctuations('A dummy text.');

        $expected = 'A dummy text';

        $this->assertEquals($expected, $result);
    }

    /**
     * Test removeStopWords.
     */
    public function testRemoveStopWords()
    {
        $utility = new LanguageProcessingUtility();

        $inputsOutputs = [
            [
                'i' => 'Stemming is funnier than a bummer says the sushi loving computer scientist',
                'o' => 'Stemming funnier bummer sushi loving computer scientist',
            ],
        ];

        foreach ($inputsOutputs as $inputOutput) {
            $result = $utility->removeStopWords($inputOutput['i']);
            $this->assertEquals($inputOutput['o'], $result);
        }
    }
}
