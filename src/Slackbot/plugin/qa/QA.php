<?php

namespace Slackbot\plugin\qa;

use Slackbot\plugin\AbstractPlugin;
use Slackbot\utility\StringUtility;

/**
 * Class QA.
 */
class QA extends AbstractPlugin
{
    /**
     * @return string
     */
    public function index()
    {
        $questions = $this->getDictionary()->get('question-answer');

        $stringUtility = new StringUtility();
        foreach ($questions as $question => $questionInfo) {
            if ($stringUtility->findInString($question, $this->getSlackbot()->getRequest('text'))) {
                // found - return random answer
                $answers = $questionInfo['answers'];

                return $answers[array_rand($answers)];
            }
        }
    }
}
