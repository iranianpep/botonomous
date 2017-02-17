<?php

namespace Slackbot\Tests;

use Slackbot\Dictionary;
use Slackbot\plugin\qa\QA;

/**
 * Class PingTest.
 */

/** @noinspection PhpUndefinedClassInspection */
class QATest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        require_once 'PhpunitHelper.php';
        parent::__construct();
    }

    /**
     * Test pong.
     */
    public function testIndex()
    {
        $question = 'test';
        $slackbot = (new PhpunitHelper())->getSlackbot('qa', " {$question}");

        $answer = (new QA($slackbot))->index();

        $qa = (new Dictionary())->get('question-answer');
        $this->assertContains($answer, $qa[$question]['answers']);
    }
}
