<?php

namespace Slackbot\utility;

use Slackbot\Dictionary;

/**
 * Class LanguageProcessingUtility.
 */
class LanguageProcessingUtility extends AbstractUtility
{
    /**
     * @param $text
     *
     * @return string
     */
    public function stem($text)
    {
        // Execute the python script with the JSON data
        $filePath = dirname(__DIR__).DIRECTORY_SEPARATOR.'py'.DIRECTORY_SEPARATOR.'stemmer.py';

        return shell_exec('python '.$filePath.' '.escapeshellarg(json_encode([$text])));
    }

    /**
     * @param $text
     * @param string $language
     *
     * @return string
     */
    public function removeStopWords($text, $language = 'en')
    {
        $stopWords = (new Dictionary())->get('stopwords-'.$language);

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

    /**
     * @param $text
     *
     * @return string
     */
    public function removePunctuations($text)
    {
        $punctuations = (new Dictionary())->get('punctuations');

        return str_replace($punctuations, '', $text);
    }
}
