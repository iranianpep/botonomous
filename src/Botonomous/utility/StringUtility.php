<?php

namespace Botonomous\utility;

/**
 * Class StringUtility.
 */
class StringUtility extends AbstractUtility
{
    /**
     * @param string $json
     *
     * @throws \Exception
     *
     * @return array|mixed
     */
    public function jsonToArray($json)
    {
        $array = empty($json) ? [] : json_decode($json, true);

        if ($array === null || !is_array($array) || json_last_error() !== 0) {
            throw new \Exception('Invalid JSON content');
        }

        return $array;
    }

    /**
     * @param $toRemove
     * @param $subject
     *
     * @return string
     */
    public function removeStringFromString($toRemove, $subject)
    {
        // pattern: !\s+! is used to replace multiple spaces with single space
        return trim(preg_replace('!\s+!', ' ', str_replace($toRemove, '', $subject)));
    }

    /**
     * @param $toFind
     * @param $subject
     * @param bool $wordBoundary If true $toFind is searched with word boundaries
     *
     * @return bool
     */
    public function findInString($toFind, $subject, $wordBoundary = true)
    {
        $pattern = $wordBoundary === true ? "/\b{$toFind}\b/" : "/{$toFind}/";

        return preg_match($pattern, $subject) ? true : false;
    }

    /**
     * @param      $toFind
     * @param      $subject
     * @param bool $wordBoundary
     *
     * @return mixed
     */
    public function findPositionInString($toFind, $subject, $wordBoundary = true)
    {
        $pattern = $wordBoundary === true ? "/\b{$toFind}\b/" : "/{$toFind}/";

        $positions = [];
        if (preg_match_all($pattern, $subject, $matches, PREG_OFFSET_CAPTURE) && !empty($matches[0])) {
            foreach ($matches[0] as $match) {
                $positions[] = $match[1];
            }
        }

        return $positions;
    }

    /**
     * Convert snake case to camel case e.g. admin_user becomes AdminUser.
     *
     * @param $string
     *
     * @return string
     */
    public function snakeCaseToCamelCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * Check subject to see whether $string1 is followed by $string2.
     *
     * @param $subject
     * @param $string1
     * @param $string2
     * @param array $exceptions
     *
     * @return bool
     */
    public function isString1FollowedByString2($subject, $string1, $string2, array $exceptions = [])
    {
        $exceptionsString = '';
        if (!empty($exceptions)) {
            $exceptions = implode('|', $exceptions);
            $exceptionsString = "(?<!{$exceptions})";
        }

        $pattern = '/'.$string1.'(?:\s+\w+'.$exceptionsString.'){0,2}\s+'.$string2.'\b/';

        return preg_match($pattern, $subject) ? true : false;
    }

    /**
     * @param $haystack
     * @param string $needle
     *
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        if ($length === 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }

    /**
     * Apply replacements in a string
     * Replacement key in the string should be like {replacementKey}.
     *
     * @param $subject mixed
     * @param $replacements array
     *
     * @return mixed
     */
    public function applyReplacements($subject, $replacements)
    {
        if (empty($replacements) || !is_string($subject)) {
            return $subject;
        }

        foreach ($replacements as $key => $value) {
            $subject = str_replace('{'.$key.'}', $value, $subject);
        }

        return $subject;
    }
}
