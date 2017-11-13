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
    public function jsonToArray(string $json)
    {
        $array = empty($json) ? [] : json_decode($json, true);
        if ($array === null || !is_array($array) || json_last_error() !== 0) {
            throw new \Exception('Invalid JSON content');
        }

        return $array;
    }

    /**
     * @param string $toRemove
     * @param string $subject
     *
     * @return string
     */
    public function removeStringFromString(string $toRemove, string $subject): string
    {
        // pattern: !\s+! is used to replace multiple spaces with single space
        return trim(preg_replace('!\s+!', ' ', str_replace($toRemove, '', $subject)));
    }

    /**
     * @param string $toFind
     * @param string $wordBoundary
     *
     * @return string
     */
    private function getFindPattern(string $toFind, string $wordBoundary): string
    {
        return $wordBoundary === true ? "/\b{$toFind}\b/" : "/{$toFind}/";
    }

    /**
     * @param string $toFind
     * @param string $subject
     * @param bool $wordBoundary If true $toFind is searched with word boundaries
     *
     * @return bool
     */
    public function findInString(string $toFind, string $subject, bool $wordBoundary = true): bool
    {
        $pattern = $this->getFindPattern($toFind, $wordBoundary);

        return preg_match($pattern, $subject) ? true : false;
    }

    /**
     * @param string $toFind
     * @param string $subject
     * @param bool $wordBoundary
     *
     * @return mixed
     */
    public function findPositionInString(string $toFind, string $subject, bool $wordBoundary = true)
    {
        $pattern = $this->getFindPattern($toFind, $wordBoundary);
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
     * @param string $string
     *
     * @return string
     */
    public function snakeCaseToCamelCase(string $string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    /**
     * Check subject to see whether $string1 is followed by $string2.
     *
     * @param string $subject
     * @param string $string1
     * @param string $string2
     * @param array $exceptions
     *
     * @return bool
     */
    public function isString1FollowedByString2(
        string $subject,
        string $string1,
        string $string2,
        array $exceptions = []
    ): bool {
        $exceptionsString = '';
        if (!empty($exceptions)) {
            $exceptions = implode('|', $exceptions);
            $exceptionsString = "(?<!{$exceptions})";
        }

        $pattern = '/'.$string1.'(?:\s+\w+'.$exceptionsString.'){0,2}\s+'.$string2.'\b/';

        return preg_match($pattern, $subject) ? true : false;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    public function endsWith(string $haystack, string $needle): bool
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
    public function applyReplacements($subject, array $replacements)
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
