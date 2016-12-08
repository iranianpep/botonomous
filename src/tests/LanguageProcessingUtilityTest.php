<?php

use Slackbot\utility\LanguageProcessingUtility;

class LanguageProcessingUtilityTest extends PHPUnit_Framework_TestCase
{
    public function testStem()
    {
        $utility = new LanguageProcessingUtility();

        $result = $utility->stem('Stemming is funnier than a bummer says the sushi loving computer scientist');

        $this->assertEquals('Stem is funnier than a bummer say the sushi love comput scientist', $result);

        $result = $utility->stem('');

        $this->assertEquals('', $result);
    }

    public function testRemoveStopWords()
    {
        $utility = new LanguageProcessingUtility();

        $ios = [
            [
                'i' => 'Stemming is funnier than a bummer says the sushi loving computer scientist',
                'o' => 'Stemming funnier bummer sushi loving computer scientist'
            ]
        ];

        foreach ($ios as $io) {
            $result = $utility->removeStopWords($io['i']);
            $this->assertEquals($io['o'], $result);
        }
    }
}
