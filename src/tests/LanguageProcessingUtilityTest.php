<?php

namespace Slackbot\Tests;

use Slackbot\utility\LanguageProcessingUtility;

/**
 * Class LanguageProcessingUtilityTest.
 */
class LanguageProcessingUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test stem.
     */
    public function testStem()
    {
        $utility = new LanguageProcessingUtility();

        $result = $utility->stem('Stemming is funnier than a bummer says the sushi loving computer scientist');

        $this->assertEquals('Stem is funnier than a bummer say the sushi love comput scientist', $result);

        $result = $utility->stem('');

        $this->assertEquals('', $result);
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
