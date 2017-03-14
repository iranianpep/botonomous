<?php

namespace Slackbot\Tests;

use PHPUnit\Framework\TestCase;
use Slackbot\Dictionary;
use Slackbot\plugin\qa\QA;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class QATest extends TestCase
{
    public function __construct()
    {
        require_once 'PhpunitHelper.php';
        parent::__construct();
    }

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
