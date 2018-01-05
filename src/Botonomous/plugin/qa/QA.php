<?php

namespace Botonomous\plugin\qa;

use Botonomous\plugin\AbstractPlugin;
use Botonomous\utility\StringUtility;

/**
 * Class QA.
 */
class QA extends AbstractPlugin
{
    private $questions;

    /**
     * @throws \Exception
     *
     * @return string
     */
    public function index()
    {
        $questions = $this->getQuestions();
        $stringUtility = new StringUtility();

        if (empty($questions)) {
            return '';
        }

        foreach ($questions as $question => $questionInfo) {
            if ($stringUtility->findInString($question, $this->getSlackbot()->getListener()->getMessage())) {
                // found - return random answer
                $answers = $questionInfo['answers'];

                return $answers[array_rand($answers)];
            }
        }

        return '';
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getQuestions(): array
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
