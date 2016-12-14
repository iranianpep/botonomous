<?php

namespace Slackbot\utility;

use Slackbot\Dictionary;

class LanguageProcessingUtility extends AbstractUtility
{
    /**
     * @param $text
     * @return string
     */
    public function stem($text)
    {
        // Execute the python script with the JSON data
        $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'py' . DIRECTORY_SEPARATOR . 'stemmer.py';

        //return exec('python ' . $filePath . ' ' . escapeshellarg(json_encode([$text])), $output);
        return shell_exec('python ' . $filePath . ' ' . escapeshellarg(json_encode([$text])));
    }

    /**
     * @param $text
     * @param string $language
     * @return string
     */
    public function removeStopWords($text, $language = 'en')
    {
        $stopWords = (new Dictionary())->get('stopwords-' . $language);

        $words = explode(' ', $text);

        if (!empty($words)) {
            foreach ($words as $key => $word) {
                if (in_array($word, $stopWords)) {
                    unset($words[$key]);
                }
            }
        }

        return implode(' ', $words);
    }
}
