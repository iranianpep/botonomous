<?php

namespace Slackbot\plugin\qa;

use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\StringUtility;

/**
 * Class QA.
 */
class QA extends AbstractPlugin
{
    private $questions;

    /**
     * @return string
     */
    public function index()
    {
        $questions = $this->getQuestions();

        $stringUtility = new StringUtility();
        $text = $this->getSlackbot()->getRequest('text');

        if (empty($questions)) {
            return '';
        }

        foreach ($questions as $question => $questionInfo) {
            if ($stringUtility->findInString($question, $text)) {
                // found - return random answer
                $answers = $questionInfo['answers'];

                return $answers[array_rand($answers)];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    public function getQuestions()
    {
        if (!isset($this->questions)) {
            $this->setQuestions($this->getDictionary()->get('question-answer'));
        }

        return $this->questions;
    }

    /**
     * @param array $questions
     */
    public function setQuestions(array $questions)
    {
        $this->questions = $questions;
    }
}
