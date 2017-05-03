<?php

namespace Botonomous\plugin\qa;

use PHPUnit\Framework\TestCase;
use Botonomous\Dictionary;
use Botonomous\PhpunitHelper;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class QATest extends TestCase
{
    /**
     * Test index.
     */
    public function testIndex()
    {
        $question = 'test';
        $slackbot = (new PhpunitHelper())->getSlackbot('qa', " {$question}");

        $answer = (new QA($slackbot))->index();

        $questionAnswer = (new Dictionary())->get('question-answer');
        $this->assertContains($answer, $questionAnswer[$question]['answers']);
    }

    /**
     * Test index.
     */
    public function testIndexEmptyQuestions()
    {
        $question = 'test';
        $slackbot = (new PhpunitHelper())->getSlackbot('qa', " {$question}");

        $qaPlugin = new QA($slackbot);
        $qaPlugin->setQuestions([]);

        $this->assertEmpty($qaPlugin->index());
    }

    /**
     * Test index.
     */
    public function testIndexNotFoundQuestion()
    {
        $question = 'dummy';
        $slackbot = (new PhpunitHelper())->getSlackbot('qa', " {$question}");

        $answer = (new QA($slackbot))->index();

        $this->assertEmpty($answer);
    }
}
