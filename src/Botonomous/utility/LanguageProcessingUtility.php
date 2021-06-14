<?php

namespace Botonomous\utility;

use Botonomous\Dictionary;
use NlpTools\Stemmers\PorterStemmer;
use NlpTools\Tokenizers\WhitespaceTokenizer;

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
        $tokens = (new WhitespaceTokenizer())->tokenize($text);

        $stemmed = (new PorterStemmer())->stemAll($tokens);

        return implode(' ', $stemmed);
    }

    /**
     * @param $text
     * @param string $language
     *
     * @throws \Exception
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
     * @throws \Exception
     *
     * @return string
     */
    public function removePunctuations($text)
    {
        $punctuations = (new Dictionary())->get('punctuations');

        return str_replace($punctuations, '', $text);
    }
}
